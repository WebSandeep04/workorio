# Comprehensive Attendance System Analysis

This document provides a deep, definitive analysis of how the current attendance mechanisms function, including a comprehensive breakdown of all statuses, scenarios, edge cases, and shift constraints.

---

## 1. Punch In Logic (The Entry Point)
When an employee triggers a punch-in (whether for `office`, `field`, or `break`), the system performs rigorous checks before allowing the punch to succeed.

### A. Pre-requisite Blocks
- **Worklog Check**: If `is_worklog` is enabled for the employee, they are strictly blocked from punching in today if they haven't submitted their previous day's worklog. (Exceptions: If they were on an approved leave, week-off, or holiday yesterday).
- **Attendance Lock**: If today's attendance record has been finalized (`is_locked = 1`), no further punch-ins are allowed.
- **Active Break**: You cannot punch in for `office` or `field` if you are currently on an active break that hasn't been ended.
- **Emergency Punch**: If the employee hits the emergency punch button, it skips regular GPS/location checks but flags the attendance as `is_emergency = 1`.

### B. Late Arrival & Shift Cutoff Calculation
If this is the **first office/field punch-in** of the day, the system evaluates if the employee is late.
- **Base Expected Time**: Matches the employee's `shift->start_time`.
- **Dynamic Midpoint Shift**: If the employee has an approved/pending **Pre-Lunch Half Day Leave**, the expected start time dynamically shifts to the exact midpoint of their shift (e.g., `Shift Start + (Shift Duration / 2)`).
- **Cutoff Time**: The strict cutoff is calculated as `Expected Start Time + shift->late_min` (grace period).
- **Late Penalty**: If the current time is greater than the Cutoff Time:
    - The employee is flagged as late.
    - `late_minutes` is recorded as the absolute difference between punch time and cutoff time.
    - The punch-in is **rejected** and the employee is prompted to provide a `late_reason`. (Once provided, the punch goes through).

---

## 2. Punch Out Logic
When the employee finishes work or goes on a break:
- **Location Validation**: Similar strict GPS/geofencing checks apply unless it's an emergency punch.
- **Early Leaving Check**: If this is a final punch-out and it is *before* the shift's `end_time`, the system calculates `early_leaving_minutes`. If early, a `short_leave_reason` is strictly required before the punch goes through.
- **Auto-ending Breaks**: If the user punches out for the day while currently on a break, the active break is forcefully ended.
- **Completion Flag**: Final punch-outs flag the attendance record with `is_completed = 1`.

---

## 3. Status Determinations & Reporting
The system evaluates the exact status of a day based on total logged hours (`total_hr`), shift requirements, and leaves.

### Shift Requirements Reference
- **Full Day Hours**: E.g., 8 hours (Standard requirement for a full day of pay).
- **Half Day Hours**: E.g., 4.5 hours (Standard bare-minimum to avoid being marked completely absent).
- **Halfday Leave Req (Min)**: E.g., 270 minutes (4.5 hours) - Used specifically to validate if someone worked enough on a day where they took a Half Day Leave.

### The Status Hierarchy
1. **Present**: 
   - The employee has no active half-day leave and their logged `total_hr` >= `Full Day Hours` of the shift.
   - *Exception*: If the shift designates today as a "Half Working Day" (like a Saturday), they only need to meet the `Half Day Hours` to get a Present status.
2. **Half Day**: 
   - The employee worked less than the full day requirement but successfully met the `Half Day Hours` requirement.
   - If they have an approved Half Day Leave, they *must* meet the `Halfday Leave Req (Min)`. If they do, they get "Present" for their working half, and "Leave" for the other half (effectively a Half Day status).
3. **Absent**:
   - The employee has 0 punched hours and no active leave, holiday, or week-off.
   - Or they punched in but failed to meet even the `Half Day Hours` bare minimum.
4. **Leave (Full / Half / Short)**:
   - **Full Day Leave**: Completely exempts the employee from attendance requirements.
   - **Half Day Leave**: Changes the expected punch-in time (if pre-lunch) and enforces the `Halfday Leave Req` instead of the standard Full Day Hours.
   - **Short Leave**: Provides a specific hour allowance (e.g., 2 hours). These hours are mathematically added to the employee's worked hours to help them meet the Full Day requirement without penalty. (Note: Short Leave is voided/overridden if they also take a Half Day leave).
5. **Holiday / Week Off**:
   - System strictly checks the shift's `week_offs` array (e.g., Sunday) and the company's Holiday calendar.
   - Overtime rules apply if they punch in on these days (e.g., `grant_comp_off_for_overtime`).

---

## 4. Edge Cases & Overrides
- **Month Lateness Limit**: The system tracks how many late minutes an employee has accrued over the month against `min_per_month_late_allow`. However, it *does not block* their punch-ins if they exceed this (it only tracks it for payroll/reporting purposes).
- **Time Restriction on Overtime**: If the shift enforces time restrictions on Week-Offs/Holidays (`enforce_time_restriction_on_overtime`), employees cannot punch out later than their standard shift `end_time`, even if they wanted to work extra.
- **Short Leave Stacking**: Employees cannot stack a Half Day Leave + a Short Leave. If both exist, the system forces the Short Leave allowance to 0 and treats it as a standard Half Day.
