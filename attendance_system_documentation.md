# Comprehensive Attendance System Analysis & Architecture

This document provides a deep, definitive analysis of the entire attendance flow, from API punch-ins on the mobile app to the final calculations and admin overrides on the Approval screen.

---

## 1. The Separation of Concerns (UI vs Database)
The system architecture separates data calculation from presentation to ensure high performance on the Approval UI.
- **The Source of Truth**: The `AttendanceReportService` is strictly responsible for calculating statuses. Whenever an employee punches in/out, or an Admin modifies attendance/leaves, this service recalculates the final status and saves it permanently to the `computed_status` column.
- **The Approval UI**: The UI simply mirrors the database. It does **not** calculate statuses on the fly. If the database `computed_status` is `null` (e.g., they haven't punched out), the UI displays `NA`.

---

## 2. API Punch-In Logic & Restrictions
When an employee triggers a punch-in (whether for `office`, `field`, or `break`) via the API, the system enforces several strict rules:

### A. Pre-requisite Blocks
- **Worklog Enforcement (`is_worklog`)**: If enabled, the employee is strictly blocked from punching in today if they haven't submitted their previous day's worklog. The system scans back to their account creation date, skipping holidays and week-offs, and ensures all past *Present* days have a worklog.
- **Attendance Lock**: If today's attendance record has been finalized by Admin (`is_locked = 1`), no further punch-ins are allowed.
- **Active Break Conflict**: An employee cannot punch in for `office` or `field` if they are currently on an active break that hasn't been ended.
- **Emergency Punch**: Skips regular GPS/location checks but flags the attendance as `is_emergency = 1`.

### B. Late Arrival & Shift Cutoff
If this is the **first office/field punch-in** of the day, the system evaluates if they are late:
- **Base Expected Time**: Matches the employee's `shift->start_time`.
- **Dynamic Midpoint Shift**: If the employee has an approved **Pre-Lunch Half Day Leave**, their expected start time dynamically shifts to the exact midpoint of their shift (e.g., `Shift Start + (Shift Duration / 2)`).
- **Cutoff Time**: The strict cutoff is calculated as `Expected Start Time + shift->late_min` (grace period).
- **Late Penalty**: If the current time is greater than the Cutoff Time:
    - The employee is flagged as late.
    - `late_minutes` is recorded as the absolute difference between punch time and cutoff time.
    - The punch-in is **blocked** and prompts the employee for a `late_reason`. Once provided, the punch succeeds.

---

## 3. API Punch-Out Logic & Restrictions
When the employee finishes work or goes on a break:
- **Early Leaving Check**: If this is a final punch-out and it is *before* the shift's `end_time`, the system calculates `early_leaving_minutes`. The punch-out is **blocked** unless a `short_leave_reason` is provided.
- **Auto-ending Breaks**: If the user punches out for the day while currently on an active break, the break is forcefully ended to prevent hanging open cycles.
- **Completion Flag**: Final punch-outs flag the record with `is_completed = 1`.

---

## 4. Backend Status Determinations & Hierarchy
The `AttendanceReportService` evaluates the exact status of a day based on total logged hours (`total_hr`), shift requirements, and leaves.

### Shift Requirements Reference
- **Full Day Hours**: Standard requirement for a full day of pay (e.g., 8 hours).
- **Half Day Hours**: Standard bare-minimum to avoid being marked completely absent (e.g., 4.5 hours).

### The Status Hierarchy
1. **Present (P)**: 
   - The employee logged `total_hr` >= `Full Day Hours` of the shift.
2. **Half Day (P2)**: 
   - The employee worked less than the full day requirement but successfully met the `Half Day Hours` requirement.
3. **Absent (A)**:
   - The employee has 0 punched hours and no active leave, holiday, or week-off.
   - Or they punched in but failed to meet even the `Half Day Hours` bare minimum.
4. **Leave Variants**:
   - **Full Day Leave (L)**: Completely exempts the employee from attendance requirements.
   - **Half Day Leave (HD)**: Requires the employee to work the `Half Day Hours` to receive a Half Day status for their working session.
   - **Short Leave (SL)**: Provides a strict hour allowance (e.g., 2 hours). These hours are mathematically added to the employee's actually worked hours (`Effective Hours = Worked Hours + SL Allowance`). If `Effective Hours` >= `Full Day Hours`, they are marked as **Present (with SL)**.
   - **Restricted Holiday (RH) / Unpaid Leave (LWP)**: Tracked specifically with warning badges.

### Holiday & Weekly Off Scenarios
- **Holiday (H)** & **Weekly Off (S)**: Standard off days.
- **Holiday/Week-Off Working (H/W or W/O-W)**: Employee punched in and worked on their off day. Overtime rules and `grant_comp_off_for_overtime` apply, crediting their leave ledger automatically when approved.

---

## 5. Work From Home (WFH) Penalties
Work From Home acts as a strict modifier in the system:
- **Status Downgrade**: If an employee works from home (`is_wfh = 1`), any standard **Present** status is strictly downgraded to a **Half Day** (code `P2`) with the reason: `"WFH Policy (Treated as Half Day)"`.
- **Leave Credit Halving**: When an Admin approves a WFH attendance on a Holiday or Weekly Off, the system only credits **0.5** leaves to their ledger instead of the standard 1.0.

---

## 6. Overlap Detection (UI Level)
Since Short Leaves do not enforce rigid start/end times in the database calculation, the system employs **duration-based** overlap detection on the Approval UI to flag suspicious behavior.

An "Overlap" warning flag will appear dynamically under the employee's primary status in these scenarios:
1. **Full Day Leave Overlap:** The employee has an approved Full Day Leave, but they also have punch-in/out records for the day resulting in > 0 hours.
2. **Half Day Leave Overlap:** The employee has an approved Half Day Leave, but they worked a full 8+ hours anyway.
3. **Short Leave Overlap:** The employee has an approved Short Leave, but they worked a full 8+ hours anyway.

*In all cases, the Overlap warning means the employee did not actually utilize the leave they applied for.*

---

## 7. Notable Edge Cases
- **Negative Leave Balances (-2):** The system permits Admins to forcefully apply leaves for employees even if their balance is 0. This results in negative ledger balances indicating the employee has borrowed against future quotas.
- **Short Leave Stacking:** Employees cannot stack a Half Day Leave + a Short Leave on the same day. If both exist, the system ignores the Short Leave.
- **Monthly Late Limits Removed:** The old monthly late-minute grace tracking has been removed and no longer dynamically blocks employee punch-ins.
