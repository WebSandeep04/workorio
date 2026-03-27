# Comprehensive Architecture: Attendance, Leave, Worklog, and Real-Time Tracking

This document outlines the operational flow, business logic, and architectural rules governing the core resource management modules in the Lead Management System.

---

## 1. Core Database Entities
The system relies on the following primary tables to manage various aspects of resource availability:

| Module | Primary Tables | Purpose |
| :--- | :--- | :--- |
| **Attendance** | `attendance`, `movements` | Stores daily punch records and individual actions (In, Out, Break). |
| **Leave** | `leave_requests`, `leave_ledgers`, `leave_types` | Manages balance-deductible (Casual/Sick) and quota-based (RH/SL) absences. |
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
- **Breaks:** Users can start/end multiple break cycles. Punching in/out for work is blocked while an active "Break" status exists.
- **Cycles:** The system supports multiple Punch-In/Out cycles (e.g., Office -> Field -> Office). Each is recorded as an independent `movement` pair.
- **Punch-Out:** Records the end of work. Mandatory requirement before submitting the daily Worklog.

### 2.2 Shift & Policy Rules
- **Late Coming:** If a user punches in after `Shift Start + late_min`, a mandatory `late_reason` must be provided.
- **Sunday/Holiday Policy:**
    - **Voluntary Presence:** If a user punches on a Sunday or Holiday, the status is marked as **`S/W`** (Sunday Working) or **`H/W`** (Holiday Working).
    - **Mandatory Worklog:** Even on non-working days (S/H), if attendance is recorded, a worklog MUST be submitted.

---

## 3. Worklog Enforcement (The "Gatekeeper")
The worklog system ensures that time spent (Attendance) is accounted for with productive output.

### 3.1 The Attendance Lock
The system implements a **Chronological Block** that disables the Attendance Toggle if gaps are detected:
- **Rule:** If attendance was recorded on Date X, but no Worklog or Approved Leave exists for Date X, the user is **blocked from punching in** on Date X+1.
- **Validation:** Controlled via `AttendanceController@canPerformAttendanceAction`.

### 3.2 Submission Prerequisites
- **Punch-Out Requirement:** A user cannot submit today's worklog until they have performed a final "Punch Out" or "Field Out."
- **Task Completion:** Users are blocked from punching out if they have pending tasks that haven't been updated/remarked for the current day.
- **Time Accuracy:** Total worklog duration must match or exceed the minimum hours required by the selected `EntryType`.

### 3.3 Task-Related Restrictions ("The Shift Finish Guard")
To ensure real-time reporting accuracy, the system ties shift completion to task updates:
- **The Punch-Out Blocker:** A user cannot "Punch Out" or "Field Out" if they have pending tasks (not completed) that have NOT been updated with remarks or a status change today.
- **The Punch-In Reminder:** Upon "Punch In," the response includes a `show_task_reminder` flag if the user has pending tasks, prompting them to check their priority items.
- **Workflow Dependencies:** Critical Path tasks are automatically hidden from the user's task list until their predecessor tasks are satisfied (Started/Completed + lag).

---

## 4. Leave Management System
Governed by `LeaveRequest` models, focusing on balance integrity and shift window protection.

### 4.1 Leave Categories
1.  **Standard Leaves (Casual/Sick):** Deducted from `leave_ledger` upon approval.
2.  **Restricted Holidays (RH):** Selected from a predefined holiday list. Users have a yearly quota (e.g., 2 RH per year).
3.  **Short Leave (SL):**
    - **Quota:** Monthly limit (e.g., 2 SL per month).
    - **Timing Windows:** Strict enforcement based on **Shift Master**.
        - **Morning Window:** `Shift Start` to `Shift Start + SL Start Limit`.
        - **Evening Window:** `Shift End - SL End Limit` to `Shift End`.
    - **Mid-Shift Protection:** SL is rejected if the request falls during core hours.

---

## 5. Approval Hierarchy & "Unlocked" Data
The system follows a strict hierarchical approval flow to ensure oversight.

### 5.1 Who Approves What?
| Approver Role | Context | Applicable To |
| :--- | :--- | :--- |
| **Direct Manager** | User has manager(s) mapped. | Worklogs, Leave Requests. |
| **System Admin** | User has NO manager assigned. | Worklogs, Leave Requests, Attendance Approval. |
| **System Admin** | Global / Technical Control. | Attendance Unlocking, Manual Attendance marking. |

### 5.2 Attendance Approval & Unlocking
- **Initial State:** All attendance records start as `pending` (`is_approved = 0`).
- **Administrative Approval:** Admins verify and approve attendance. Approved records are locked for end-users.
- **Unlocking Logic:** If a user needs to modify a past record (e.g., fix a missed Worklog or correct a Punch-In time), an Admin must use the **Unlock Attendance** utility. This sets `is_approved = 0` and logs the action in `attendance_unlock_logs`.

---

## 6. "Unlocated" & Critical Data States
These are edge cases or specific states where data might seem "disconnected" or requires special attention:

1.  **Floating GPS Points (Orphan Tracking):** If `is_tracking = true`, coordinates are saved to `employee_locations` even if the user hasn't Punched-In. This is "Unlocated Data" — movement exists without an associated attendance session.
2.  **Attendance-Worklog Mismatch:** Approved Attendance exists, but the Worklog is `Rejected`. This leaves the day in a "locked" state for the user until the Worklog is resubmitted and approved.
3.  **Cross-Day Movements:** Movements starting at 11:55 PM and ending at 12:15 AM. The system currently timestamps them based on the actual time, but links them to the `attendance` record of the date the "IN" occurred.
4.  **Pending Leave on Worked Day:** If a user applies for leave but still Punches-In, the **Attendance takes precedence**. The user must cancel the leave to refund their balance.

---

## 7. Attendance Summary Codes
Reference table for reporting and automated status generation:

| Code | Meaning | Logic |
| :--- | :--- | :--- |
| **P** | Present | Regular working day with sufficient hours. |
| **S/W** | Sunday Working | Attendance recorded on a Sunday. |
| **H/W** | Holiday Working | Attendance recorded on a Holiday. |
| **SL** | Short Leave | Approved SL with < 7 hours worked. |
| **P (SL)** | Present (with SL) | Approved SL but shift hours successfully completed. |
| **L** | On Leave | Approved Casual/Sick leave. |
| **A** | Absent | No attendance OR leave recorded for a working day. |
| **H** | Holiday | Gazetted Holiday (No work required). |
| **S** | Sunday | Weekly Off (No work required). |

---

## 8. Implementation Checklist for Developers
- [ ] **Shift Limits:** Ensure `sl_start_limit` and `sl_end_limit` are set in the Shift Master for SL validation.
- [ ] **Worklog Gaps:** `canPerformAttendanceAction` must check for existing attendance records on missing dates.
- [ ] **Task Blocker:** `Task` model check must include tasks due today or older that haven't been updated today.
- [ ] **Tracking Filter:** Maintain the Stationary Filter in GPS acquisition to prevent database bloat.
- [ ] **Hierarchy Check:** Always use `whereDoesntHave('managers')` for Admin-level approvals to maintain isolation.
