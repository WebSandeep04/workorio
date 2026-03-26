# Attendance & Leave Management Architecture and Flow

This document details the complete operational flow, logic structure, and database mechanisms governing the Attendance and Leave functionality within the Workorio platform.

## 1. Attendance Mechanism Flow

### 1.1 Punch-In & Punch-Out Workflow
The Attendance subsystem tracks employee time using movements (`office`, `field`, `break`).
- **Endpoint Initialization:** Employees submit a punch action tracking their `movement_type` along with geographic parameters via `AttendanceController@punchIn`.
- **Validation Gates:**
    1. **Worklog Lock Security:** Before permitting attendance, `canPerformAttendanceAction()` validates whether the user (`is_worklog = 1`) has filled in their worklogs chronologically for every past working day since their account creation date. (It smartly bypasses Sundays, Holidays, and Approved Leave days).
    2. **Active Break Check:** Disallows changing states if the user hasn't actively ended their ongoing break.
    3. **First-Punch Initialization:** Generates an `Attendance` parent row on the first punch-in of the day.
    4. **Geographic Fencing & Location Authentication:** Evaluates the provided `latitude`/`longitude` coordinates against the authorized work coordinates tied to the user via their `employee_places` mappings. 
    5. **WFH & Emergency Gates:** Can bypass strict location fences via a `work_from_home` flag (if authorized) or via an `emergency_attendance` flow requiring photographic verification.
- **Auto-Punch Adjustments:** If an employee directly punches into 'office' work while formally checked into 'field' work, the system automatically triggers an 'Auto-ended (Office work started)' logout for the field punch to ensure accurate overlap limits.
- **Log Insertion:** Inserts a `Movement` record stamping the action (`in`/`out`). Total daily hours are continuously computed by finding the delta between the earliest `in` and the latest `out` movements.

### 1.2 Reporting & Status Aggregation
Complex aggregation maps exist inside `AttendanceController` methods `_fetchMonthlyReportData()` and `_fetchDateReportData()`.
- The processor evaluates every day in the month loop and prioritizes status assignment in this explicit cascade:
    1. **`S/W` (Sunday Working):** Attendance recorded on a Sunday. (Tracked as a distinct category).
    2. **`H/W` (Holiday Working):** Attendance recorded on an official Holiday (non-Sunday).
    3. **`P (SL)` (Present with Short Leave):** The user was granted Short Leave but managed to clock >= 7 hours.
    4. **`P` (Present):** Worked >= 7 hours on an ordinary working day.
    5. **`P2` (Half Day):** Worked >= 4 hours but < 7 hours.
    6. **`SL` (Short Leave):** Granted an SL configuration and didn't hit 7 hours.
    7. **`H` (Holiday):** No attendance logged, but date coincides with the Active `Holiday` table.
    8. **`S` (Sunday):** No attendance logged, and day maps to Sunday natively.
    9. **`RH` (Restricted Holiday):** No attendance logged, but user took an approved Restricted Holiday Leave.
    10. **`L` (Leave):** No attendance logged, but user has standard approved Leave.
    11. **`A` (Absent):** Working day, no attendance logs, no leave coverage.
- **Shift Analysis & Late Tracking:** Matches the first punch-in time against the employee's assigned `Shift` parameters. If the punch breaches the shift's `start_time` + `late_min` grace threshold, it records a Late occurrence securely in the metrics.

### 1.3 Sunday & Holiday Attendance Logic
The system allows employees to mark attendance on Sundays and Holidays, but handles them as distinct categories:
- **Status Assignment:** Any attendance movement recorded on a Sunday result in `S/W` (Sunday Working) status. Attendance on an official Holiday results in `H/W`.
- **Summary Metrics:** `S/W` and `H/W` days are tracked independently. They are included in the overall "Total Present" combined metric for monthly summaries.
- **Worklog Requirement:** Working on a Sunday or Holiday **mandates** a worklog submission to unlock subsequent attendance actions, identical to a regular working day.

---

## 2. Leave Management Flow

### 2.1 Request Structuring
The platform utilizes standard Leave Types (Casual, Sick) governed by a globally configurable ledger, along with "Virtual" Leave Types: Restricted Holidays (RH) and Short Leaves (SL) which are governed by periodic quotas defined directly in the `EmploymentType` assigned to the worker.

- **Leave Retrieval Options:** The `fetchLeaveTypes()` method builds the UI options explicitly for the user. It aggregates actual LeaveType tables and evaluates bounds (e.g. SL is bound computationally explicitly to per-month allowances, removing remaining balance calculations tied to previous months).
- **Date & Timeline Enforcement:** Submissions strictly lock start/end dates. SL requests explicitly bind end dates to mirror start dates entirely, substituting timeline validations with `start_time` and `end_time` logic clamped strictly to the employee's specific active shift periods.

### 2.2 Approval & Execution Engine
`LeaveController@store` governs pending payload submissions.
- **Constraint Parsing:**
    - RH: Checks if `rh_allowed` parameter permits additional days.
    - SL: Evaluates if `sl_allowed` monthly limit is exhausted. Ensures time is strictly intra-day.
    - General Leaves: Connects with the active `LeaveBalanceService` to assert if enough accrued ledger balance natively sits underneath the requested Leave Type.
- **Hierarchical Approval Gates (`fetchApprovals`)**:
    - Queries restrict visibility. Team Managers can exclusively see subordinates mapping to them via `whereHas` logic. System Admins exclusively catch orphans (subordinates lacking assigned managers) ensuring zero approval overlap.
- **Ledger Impact Execution (`approve` / `reject` / `destroy`)**:
    - When a standard leave is transitioned to `Approved`, the `LeaveBalanceService` natively executes double-entry deductions (`debitLeave`) committing ledger transactions.
    - Canceling an approved leave automatically triggers `creditLeave`, instantly refunding the employee's ledger.

### 2.3 Automated Accrual Mechanics
Periodic leaf replenishment operations are driven by the `DailyLeaveAccrual` cron command logic.
- Analyzes daily whether active personnel have cleared thresholds based on the `rules` nested inside their `EmploymentType`.
- Checks working day requirements (ensuring employees have recorded valid worklogs/attendance bounds) before issuing configured partial accruals mapping continuously to standard arrays.

### 2.4 Worklog Lock Implications & Mandatory Logs
The `canPerformAttendanceAction` logic includes any day with recorded attendance in the mandatory worklog completion check:
- **Unified Rule:** If an employee has punched attendance on ANY day (Regular, Sunday, or Holiday), they must complete a worklog for that day to unlock the system for the next day.
- **Skip Logic:** Sundays and Holidays are only skipped if the user was **Absent** (no attendance recorded).
- **Result:** Employees who work on a Sunday will see `S/W` in their report, and their Monday punch-in **will be blocked** if the Sunday worklog is missing.
- **Bulk Validation:** The administrator's Missing Entries summary also factors in Sunday/Holiday work when identifying gaps in organizational worklog compliance.

---

## 3. Real-Time Tracking Mechanism

The platform includes a real-time location tracking subsystem designed for field personnel, integrated directly with the attendance lifecycle.

### 3.1 Data Acquisition (Mobile API)
Tracking is driven by the mobile application which periodically transmits GPS coordinates to `EmployeeLocationController@store`.

- **Activation Gate:** Tracking only functions if `is_tracking = 1` is enabled for the specific user in the `employees` table.
- **Intelligence Filtering (Noise Reduction):** To prevent database bloat and "GPS drift" while stationary, the API implements two layers of filtering:
    1. **Exact Duplicate Check:** Skips saving if latitude/longitude (normalized to 8 decimal places) matches the last record.
    2. **Stationary/Jitter Filter:** If movement speed is < 1 km/h and the distance from the last 5 recorded points is < 15 meters, the coordinate is discarded as jitter.
- **Timestamping:** Every valid movement is persisted in `employee_locations` with a `tracked_at` precision timestamp.

### 3.2 Live Monitoring Dashboard
Administrators monitor field movement via the `TrackingController@index` view, which provides a map-based visualization of the breadcrumb trail.

- **Status-Driven Visualization:** The map markers update dynamically based on the employee's current **Attendance Status**:
    - 🟢 **Green (Active):** Currently Punched In (Office or Field).
    - 🟡 **Yellow (Break):** Attendance session is active but user has a 'start' break movement without an 'end'.
    - 🔴 **Red (Inactive):** Not currently on duty (Punched Out or Not Started).
- **History Playback:** Admins can filter by date to reconstruct the travel path of any employee, overlaid with their specific Punch-in/Out timestamps for that day.
- **Resource Optimization:** The tracking fetch logic uses optimized Eloquent relations (`with(['attendances' => ...])`) to minimize the impact on system performance during high-concurrency monitoring.
