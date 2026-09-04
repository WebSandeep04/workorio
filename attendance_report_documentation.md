# Attendance System Analysis: Punch-In/Out & Status Logic

This document provides a deep analysis of how the current attendance punch-in and punch-out mechanisms function, along with a comprehensive breakdown of all attendance statuses and their specific scenarios.

---

## 1. Punch-In Logic (`Api\AttendanceController@punchIn`)

When a user attempts to punch in (for `office`, `field`, or `break`), the system runs a series of validations and logic flows:

### A. Pre-requisite Validations
1. **Worklog Dependency**: If the user has `is_worklog` enabled, they must have completed their previous day's worklog (or have an approved leave/holiday/week-off) before they can perform any attendance action today. If incomplete, punch-in is **blocked**.
2. **Attendance Lock**: If today's attendance record (`is_locked`) is locked, punch-in is **blocked**.
3. **Active Break Check**: If the user is currently on an active break (punched into a break but not ended it), punching in for office or field is **blocked**.

### B. Location & Place Validation (Office/Field only)
- If it is the **first** office/field punch-in of the day, location constraints are applied.
- The system checks the user's distance against their allowed location radiuses (`is_place_allowed`).
- If the user is outside the allowed radius, punch-in is **blocked**, unless they flagged it as an `emergency_attendance`.

### C. Late Punch-In Calculation
- Only checked during the **first** office/field punch-in of the day.
- Checks the user's shift start time and allowed `late_min` grace period.
- If the user is late and does NOT have an approved/pending leave for today:
  - A `late_reason` is **required**. If missing, the API throws an error prompting for a reason.
  - The exact `late_minutes` are recorded.

### D. Movement Execution & Smart Detection
- **Auto-Checkout**: If punching in for `office`, it automatically punches them out of `field` (and vice-versa).
- **Movement Record**: A `Movement` row is created with `movement_action = 'in'`, time, location, and `late_reason` (if applicable).
- **Smart Early Return**: If the user had an approved full-day leave for today but punched in anyway, the leave is flagged with `has_attendance_overlap`.

### E. Live Tracking Flag
- The API response returns an `is_tracking` boolean flag based on the `Employee` model settings. This instructs the frontend mobile app whether it should continuously track the user's location while they are punched in.

---

## 2. Punch-Out Logic (`Api\AttendanceController@punchOut`)

### A. Pre-requisite Validations
1. **Worklog Dependency**: Same as punch-in.
2. **Task Validation**: A strict blocker. If the user has pending tasks (not marked done/completed) with due dates up to today, and these tasks were **not updated today**, the punch-out is **blocked**. The user must add remarks or update the task status.
3. **Attendance Lock**: Same as punch-in.
4. **Active Break Check**: Cannot punch out of office/field if currently on a break.
5. **State Validation**: Checks if the user is actually punched in for the requested `movement_type` before allowing a punch-out.
6. **Location Validation**: Location distance checking is enforced upon punch-out, identical to punch-in.

### B. Execution & Status Computation
- Creates a `Movement` row with `movement_action = 'out'`.
- **Crucial Step - Status Determination**: Immediately triggers `AttendanceReportService->computeAndSaveDailyStatus()`. This means the user's total hours, late minutes, and final daily status (Present, Halfday, Absent, etc.) are **exactly determined and permanently updated during the punch-out action**.
- Similar to punch-in, the API response also includes the `is_tracking` flag to update the frontend's tracking state.

---

## 3. Break Logic
- **Start Break**: Creates a `break` movement with action `start`. Blocked if another break is already active or if attendance is locked.
- **End Break**: Creates a `break` movement with action `end`. Blocked if no active break exists.




