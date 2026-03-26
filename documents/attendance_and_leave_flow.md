# Comprehensive Architecture: Attendance, Leave, Worklog, and Real-Time Tracking

This document outlines the operational flow, business logic, and architectural rules governing the core resource management modules.

---

## 1. Attendance Mechanism
The attendance system is governed by a **chronological lock** that ensures data integrity and daily work documentation.

### 1.1 Punch-In Rules
- **Shift Assignment:** Users must be assigned a shift (Shift Master) to determine late-coming and short-leave windows.
- **Geofencing:** If a user is assigned specific "Allowed Places," they can only punch in/out within the specified GPS radius.
- **WFH Option:** Remote workers can be flagged as "WFH" to bypass geofence checks while maintaining tracking integrity.

### 1.2 Sunday and Holiday Policy
- **Voluntary Presence:** If a user punches attendance on a Sunday or a gazetted Holiday, the system records the status as:
    - **`S/W` (Sunday Working):** Recorded if the date is a Sunday.
    - **`H/W` (Holiday Working):** Recorded if the date is a Gazetted Holiday.
- **Mandatory Worklog:** If attendance is recorded on ANY day (including weekends/holidays), a **Worklog submission is mandatory**. The system will lock the user's ability to punch in the next working day until the previous worklog is completed.

---

## 2. Worklog Enforcement (The "Attendance Lock")
The worklog system serves as a gatekeeper for the entire platform.

### 2.1 Chronological Integrity
- **The Gap Rule:** Users cannot skip days. If the system detects a gap (Missing Worklog) on a date where attendance was recorded, the Punch-In button is disabled (Locked).
- **Validation Route:** `AttendanceController@canPerformAttendanceAction` and `WorklogApiController@getMissingDates` are the central points for this validation.

---

## 3. Leave Management System
Governed by `LeaveRequest` models, this system manages both balance-deductible and quota-based leaves.

### 3.1 Standard Leave Types (Casual/Sick)
- High-level leaves that deduct from an annual `leave_ledger`.

### 3.2 Restricted Holidays (RH)
- Employees are granted a specific quota of RH per year (defined in `EmploymentType`).
- These are selected from a pre-defined Holiday list marked `is_rh`.

### 3.3 Short Leave (SL) Logic (The Shift Window)
SL is designed for brief absences and is governed by strict timing windows to protect core working hours.
- **Quota:** Defined as "SL allowed per month" in `EmploymentType`.
- **Windows (Shift-Centric):** Limits are defined in the **Shift Master**.
    - **Morning Window:** Allowed between `Shift Start` and `Shift Start + SL Start Limit`.
    - **Evening Window:** Allowed between `Shift End - SL End Limit` and `Shift End`.
    - **Example:** Shift 10 AM - 7 PM with a 2h Start Limit allow SL between 10 AM and 12 PM only.
- **Core Hour Protection:** SL requests containing times during the mid-shift core hours are rejected.
- **Reporting Status:**
    - **`SL`**: Approved Short Leave with < 7 hours worked.
    - **`P (SL)`**: Approved Short Leave with >= 7 hours worked (User completed the day despite a small absence).

---

## 4. Real-Time Tracking & GPS Intelligence
Employees flagged with `is_tracking = true` have their movements monitored via the mobile app.

### 4.1 Data Acquisition & Noise Reduction
To ensure battery efficiency and data quality, the system implements a two-layer filter on incoming GPS coordinates:
1.  **Exact Duplicate Check:** Prevents redundant database writes for identical coordinates.
2.  **Stationary/Jitter Filter:** If a user hasn't moved beyond a minimum distance (e.g., 10 meters) since the last breadcrumb, the new point is discarded to prevent "jitter" while the device is stationary indoors.

### 4.2 Admin Tracking Dashboard
The Dashboard (`TrackingController`) provides a real-time visualization of the field force:
- **Green Marker (Active):** User is Punched-In and actively moving.
- **Yellow Marker (On Break):** User has clicked the "Break" button.
- **Red Marker (Inactive):** User has Punched-Out or hasn't started their day.
- **Breadcrumb View:** Admins can view the chronological "Snail Trail" of a specific employee to verify route compliance.

---

## 5. Attendance Summary Codes
| Code | Meaning | Logic |
| :--- | :--- | :--- |
| **P** | Present | Regular working day with sufficient hours. |
| **S/W** | Sunday Working | Attendance recorded on a Sunday. |
| **H/W** | Holiday Working | Attendance recorded on a Holiday. |
| **SL** | Short Leave | Approved SL with < 7 hours worked. |
| **P (SL)** | Present (with SL) | Approved SL but shift hours successfully completed. |
| **L** | On Leave | Approved Casual/Sick leave. |
| **A** | Absent | No attendance or leave recorded for a working day. |
| **H** | Holiday | Gazetted Holiday (No work required). |
| **S** | Sunday | Weekly Off (No work required). |

---

## 6. Implementation Checklist for Developers
- [ ] **Shift Limits:** Ensure `sl_start_limit` and `sl_end_limit` are set in the Shift Master.
- [ ] **Worklog Gaps:** `getMissingDates` must check for `attendance` records, not just dates.
- [ ] **Tracking Filter:** Do not disable the Stationary Filter in `EmployeeLocationController` as it prevents database bloating.
