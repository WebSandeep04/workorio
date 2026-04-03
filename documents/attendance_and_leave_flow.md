# Comprehensive Architecture: Attendance, Leave, Worklog, and Real-Time Tracking

This document outlines the operational flow, business logic, and architectural rules governing the core resource management modules in the Lead Management System.

---

## 1. Core Database Entities
The system relies on the following primary tables to manage various aspects of resource availability:

| Module | Primary Tables | Purpose |
| :--- | :--- | :--- |
| **Attendance** | `attendance`, `movements` | Stores daily punch records and individual actions (In, Out, Break). |
| **Leave** | `leave_requests`, `leave_ledgers`, `leave_types` | Manages balance-deductible (Casual/Sick) and quota-based (RH/SL/HD) absences. |
| **Worklog** | `worklogs`, `worklog_approvals` | Records daily productive output; mandatory companion to attendance. |
| **Tracking** | `employee_locations` | Real-time GPS breadcrumbs for employees with `is_tracking = true`. |
| **Rules** | `shifts`, `employment_types`, `holidays` | Masters defining shift timings, leave quotas, and calendar holidays. |

---

## 2. Attendance Mechanism (Punch Flow)
Attendance is the foundational record of an employee's presence.

### 2.1 The Daily Lifecycle
- **Punch-In:** Records the start of work.
    - **Shift Enforcement:** Shift timings determine late-coming windows (`late_min`).
    - **Geofencing:** Radius check against "Allowed Places" (`place_radius`) unless `is_wfh = 1` or `is_emergency = 1`.
    - **Late Tracking:** `late_minutes` are calculated relative to the shift start time and stored in the `attendance` record.
    - **Mode Tracking:** Every movement is tagged with a `mode` (`web` for browser, `mobile` for API/App).
- **Breaks:** Users can start/end multiple break cycles. Punching in/out for work is blocked while an active "Break" status exists. 
    - **Locking:** Break actions are blocked if the attendance record is marked as `is_locked`.
- **Cycles:** The system supports multiple Punch-In/Out cycles (e.g., Office -> Field -> Office). Each is recorded as an independent `movement` pair.
- **Punch-Out:** Records the end of work. Mandatory requirement before submitting the daily Worklog.

### 2.2 Shift & Policy Rules
- **Late Coming:** If a user punches in after `Shift Start + late_min`, a mandatory `late_reason` must be provided.
- **Late Allowance:** Employees have a monthly quota for late minutes (`min_per_month_late_allow`). If exceeded, they must apply for an SL or Half-Day leave instead of punching in.
- **Sunday/Holiday Policy:**
    - **Voluntary Presence:** If a user punches on a Sunday or Holiday, the status is marked as **`S/W`** (Sunday Working) or **`H/W`** (Holiday Working).
    - **Mandatory Worklog:** Even on non-working days (S/H), if attendance is recorded, a worklog MUST be submitted.

---

## 3. Worklog Enforcement (The "Gatekeeper")
The worklog system ensures that time spent (Attendance) is accounted for with productive output.

### 3.1 The Attendance Lock (Chronological Block)
The system implements a block that disables the Attendance Toggle if gaps are detected:
- **Rule:** If attendance was recorded on Date X, the user must have either a **Worklog Entry** or an **Approved Leave** for Date X to punch in on Date X+1.
- **Exemptions:** Sundays and Holidays where no attendance was marked are automatically skipped. Approved leaves (including Half-Day) allow the user to bypass the worklog requirement for that specific day.
- **Validation:** Controlled via `canPerformAttendanceAction` in both Web and API controllers.

### 3.2 Submission Prerequisites
- **Punch-Out Requirement:** A user cannot submit today's worklog until they have performed a final "Punch Out" or "Field Out."
- **Task Completion:** Users are blocked from punching out if they have pending tasks that haven't been updated/remarked for the current day.
- **Time Accuracy:** Total worklog duration must match or exceed the minimum hours required by the selected `EntryType`.

### 3.3 Task-Related Restrictions ("The Shift Finish Guard")
To ensure real-time reporting accuracy, the system ties shift completion to task updates:
- **The Punch-Out Blocker:** A user cannot "Punch Out" or "Field Out" if they have pending tasks (not completed) that have NOT been updated with remarks or a status change today.
- **The Punch-In Reminder:** Upon "Punch In," the response includes a `show_task_reminder` flag if the user has pending tasks, prompting them to check their priority items.

---

## 4. Leave Management System
Governed by `LeaveRequest` models, focusing on balance integrity and shift window protection.

### 4.1 Leave Categories
1.  **Standard Leaves (Casual/Sick):** Deductible from balance.
2.  **Restricted Holidays (RH):** Yearly quota (e.g., 2 RH per year).
3.  **Half-Day Leave (HD):** 
    - Deducts 0.5 from leave balance.
    - Exempts user from late allowance blocks for the day.
4.  **Short Leave (SL):**
    - **Quota:** Monthly limit (e.g., 2 SL per month).
    - **Timing Windows:** Strict enforcement based on **Shift Master**.
        - **Morning Window:** `Shift Start` to `Shift Start + SL Start Limit`.
        - **Evening Window:** `Shift End - SL End Limit` to `Shift End`.
    - **Mid-Shift Protection:** SL is rejected if the request falls during core hours.

---

## 5. Approval Hierarchy & Locking
The system follows a strict hierarchical approval flow to ensure oversight.

### 5.1 Approval Roles
- **Direct Manager:** Approves Worklogs and Leave Requests for mapped subordinates.
- **System Admin:** Final authority. Approves records for users with no managers, manages attendance unlocking, and manual attendance entries.

### 5.2 Record Locking
- **Attendance Locking:** Once approved by an Admin or manually locked via command, `is_locked = 1`. end-users can no longer perform punch-in, punch-out, or break actions for that day.
- **Unlocking:** Requires Admin intervention via the **Unlock Attendance** utility, which logs the action in `attendance_unlock_logs`.

---

## 6. API vs. Web Implementation (Developer Reference)
Although sharing the same database, the Web and API controllers have specific implementation details:

| Feature | Web Implementation | API (Mobile) Implementation |
| :--- | :--- | :--- |
| **Mode** | Records movements with `mode = 'web'`. | Records movements with `mode = 'mobile'`. |
| **Real-Time Data** | Basic status return. | Returns `working_hours`, `completed_hours`, and `is_tracking` status. |
| **Late Reasons** | Direct string capture. | Provides a list of predefined `LateReason` objects for selection. |
| **Validation Parity** | `canPerformAttendanceAction` includes leave check. | **Fixed**: Now matches Web to include leave check (previously worklog only). |
| **Late Tracking** | Stores `late_minutes` in `attendance`. | **Fixed**: Corrected bug where `late_minutes` were calculated but not stored. |
| **Locking Checks** | Applied to all punch types and break actions. | Applied to all punch types and break actions. |

---

## 7. Attendance Summary Codes
Reference table for reporting and automated status generation:

| Code | Meaning | Logic |
| :--- | :--- | :--- |
| **P** | Present | Regular working day with sufficient hours. |
| **S/W** | Sunday Working | Attendance recorded on a Sunday. |
| **H/W** | Holiday Working | Attendance recorded on a Holiday. |
| **SL** | Short Leave | Approved SL with < 7 hours worked. |
| **HD** | Half Day | Approved Half-Day leave. |
| **L** | On Leave | Approved Casual/Sick leave. |
| **A** | Absent | No attendance OR leave recorded for a working day. |
| **H** | Holiday | Gazetted Holiday (No work required). |
| **S** | Sunday | Weekly Off (No work required). |

---

## 8. Implementation Checklist for Developers
- [ ] **Shift Limits:** Ensure `sl_start_limit` and `sl_end_limit` are set in the Shift Master for SL validation.
- [ ] **Worklog Gaps:** `canPerformAttendanceAction` must check for both Worklog OR Approved Leave on missing dates.
- [ ] **Task Blocker:** `Task` model check must include tasks due today or older that haven't been updated today.
- [ ] **Late Logic:** Ensure `late_minutes` are correctly persisted in the `attendance` table during punch-in (both Web/API).
- [ ] **Locking Integrity:** Verify that `is_locked` prevents all operations including `startBreak` and `endBreak`.
- [ ] **Tracking Filter:** Maintain the Stationary Filter in GPS acquisition to prevent database bloat.
