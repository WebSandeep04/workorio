# Attendance Approval System - Core Architecture

**Route**: `/attendance/approval`
**Controller**: `AttendanceApprovalController.php`
**View**: `resources/views/attendance/approval.blade.php`

The Attendance Approval interface is the absolute command center for HR/Admins. It merges raw physical movement data (punches), calculated threshold rules (graces/restrictions), and the Leave Engine into a single, actionable grid.

Here is a deep dive into every moving part and logic block driving this system.

---

## 1. The Core Fetch Engine (`fetch` method)
The most complex part of the system is building the daily grid for the Admin. 

When an Admin selects a Date, the system doesn't just query the `attendances` table. Instead, it queries **all Active Employees** (ensuring no one is missed) and then joins their daily realities.

### A. Employee Base Resolution
- Queries all employees where `status = 'active'` and their mapped `User` has `is_attendance = 1` and `role_id != 1` (excludes super admins).
- If an employee has no mapped user account, they are surfaced with a hardcoded `status => 'No User'` tag so HR knows there's a missing setup.

### B. Daily Reality Joining
For each valid employee, it queries exactly two things for that `$date`:
1. **Physical Reality (`Attendance`)**: Finds the row matching `user_id` and `date`, eager loading all `Movements` (punches).
2. **Leave Reality (`LeaveRequest`)**: Finds any Approved or Pending leave overlapping that date.

### C. Status Determination & Time Restrictions
The system calls the master brain: `AttendanceReportService@determineStatus()`.
It passes in the physical hours worked, the shift's `fullDayHr` / `halfDayHr`, and critical rule toggles:
- **`isWeeklyOff` / `isHoliday`**: Detected via `Shift` week_offs array and `Holiday` table.
- **`enforce_time_restriction_on_overtime`**: If true, working 5 minutes on a Sunday yields `Absent (W/O)`. They must hit `halfDayHr` to get `W/O-W`.
- **`exempt_grace_on_overtime`**: If true, late arrivals on Sundays do not trigger late grace penalties.

### D. The Overlap Detection Engine
This is a critical security layer. What happens if someone applies for a **Half Day Leave (Pre-Lunch)**, gets it approved, but then physically punches in at 10:00 AM (during their leave)?

The `fetch` engine physically calculates the "Midpoint" of the shift:
```php
$midPoint = $shiftStart->copy()->addMinutes($shiftStart->diffInMinutes($shiftEnd) / 2);
```
- **Pre-Lunch Leave Overlap**: If `punchInTime >= midPoint`, they came in post-lunch. **No Overlap**. (Clean split). If `punchInTime < midPoint`, they worked during their leave. **Overlap Detected!**
- **Short Leave (SL) Overlap**: Compares physical punches against the exact calculated tail-end SL window.
- **Full Day Overlap**: Any physical punch always triggers a Full Day overlap.

If an overlap is detected, the Admin UI shows a bright **Resolve Overlap** button instead of the standard "Approve" button.

---

## 2. Action Logic & Handlers

The Admin has ultimate power over the daily ledger. When they take action, complex state machines fire in the background.

### A. Standard Approval (`approve()`)
- Updates the `attendances` row `status` to `approved`.
- **Comp Off Hook**: Fires `creditHolidayWorking($attendance)`.

### B. Approve with Edit (`updateTimes()`)
If an employee forgot to punch out, the Admin can manually inject an out-time.
- Creates new `Movement` rows for the manual edits.
- Stores a forensic audit trail in `AttendanceEditLog` (who changed it, old times, new times, reason).
- Recalculates total hours.
- **Comp Off Hook**: Fires `creditHolidayWorking($attendance)` based on the *new* edited hours.

### C. Manual Mark Attendance (`markAttendance()`)
If the employee forgot to punch entirely (no record exists), the Admin can force-mark them Present or Half Day.
- Validates the manual times against the Shift's thresholds to see if it triggers late penalties (unless explicitly forced as an exception).
- Creates the `Attendance` record and movements instantly.
- **Comp Off Hook**: Fires `creditHolidayWorking($attendance)`.

### D. Reject & Delete (`reject()`)
- Soft deletes or hard deletes the attendance row.
- Fires a `Mail::to()` `AttendanceRejectedMail` alerting the employee that their physical punches for that day were invalidated by HR (with the reason).

---

## 3. The Resolution Engine (Overlaps & Quick Leaves)

### A. Resolve Overlap: "Void Attendance" (`voidAttendance()`)
If an employee is on a Full Day approved leave but accidentally punches the biometric scanner, an Overlap is generated.
- Admin clicks **"Void Attendance & Allow Leave"**.
- System physically deletes the `Attendance` and `Movements` for that day, restoring the integrity of the Full Day leave.

### B. Quick Leave Injection (`applyQuickLeave()`)
If an employee is completely absent (no punches) and calls in sick, the Admin can apply a leave for them directly from the grid.
- Queries `LeaveBalanceService` to fetch the employee's active balances.
- Injects a `LeaveRequest` with status `approved` and immediately debits the ledger to lock the balance.
- If they are applying a Full Day leave but the employee has a physical `Attendance` row, it forcefully drops the attendance row to prevent an overlap.

---

## 4. The Compensatory Off Engine (`creditHolidayWorking()`)

This is the financial brain of overtime leave-banking. It fires on almost every approval or edit action.

**The Workflow:**
1. **Verification**: Checks if the date is actually a Holiday or Weekly Off.
2. **Time Restriction Check**: Uses `AttendanceReportService@determineStatus` to see if the hours worked met the `enforce_time_restriction_on_overtime` rules. Must return exactly `W/O-W` or `H/W`.
3. **Shift Toggle Validation**: Checks the dynamic `$shift->grant_comp_off_for_overtime` boolean. If the shift dictates they should just get paid out via payroll instead of getting leave balances, it instantly aborts.
4. **Leave Type Discovery**: Searches the `LeaveType` table for names like `%Holiday Working%` or `%Compensatory%`.
5. **Auto-Creation Fail-Safe**: If no such leave type exists, it dynamically runs `LeaveType::create()` to spawn a `Holiday Working` leave type (with `is_deductible = true` so they can spend it later).
6. **Anti-Duplication**: Checks `LeaveLedger` to ensure a credit hasn't already been issued for this specific `attendance_id`.
7. **Credit Issue**: Calls `LeaveBalanceService@creditLeave` to securely deposit `+1.0` day into the employee's ledger.

---

## Conclusion
The `AttendanceApprovalController` acts as a firewall between raw data and the final payroll/leave ecosystem. It forces overlapping realities (punches vs leaves) to be resolved logically, logs every manual intervention forensically, and handles complex double-entry leave accounting automatically through Hooks like `creditHolidayWorking()`.
