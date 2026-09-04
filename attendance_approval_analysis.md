# Deep Analysis of Attendance Approval System

The Attendance Approval system (`/attendance/approval`) serves as the administrative dashboard for HR/Managers to review, correct, and approve daily employee attendance. It acts as the gatekeeper before attendance records are finalized for payroll or reporting.

The core logic is handled by `App\Http\Controllers\AttendanceApprovalController`.

---

## 1. Fetching Data for Approval (`fetch`)

When the UI requests data for a specific date (defaults to today), the system pulls a comprehensive dataset merging Users, Attendances, Leaves, and Shifts.

### Logic Flow:
1. **Target Audience**: Fetches all **Active Employees** OR inactive employees who have recorded attendance on that specific date. Admins (Role ID = 1) are excluded.
2. **Attendance Lookup**: Searches the `attendances` table for records matching the user and date.
3. **Leave Lookup**: Searches `LeaveRequest` for any approved or pending leaves that overlap with the selected date.
4. **Shift Lookup**: Fetches the user's shift for that day to determine expected IN/OUT times.
5. **Status Resolution**:
   - If `computed_status` exists in the DB, it uses it.
   - If not, it calculates it on the fly using `AttendanceReportService` (useful for checking live status of current day).
6. **Conflict Detection**:
   - **Leave Overlap**: If an employee has punched in BUT also has an approved/pending leave for that day, it checks for genuine overlaps (e.g., punched in during a pre-lunch half-day leave, or during a short leave). If an overlap is detected, it flags it for the manager.
   - **Early Out / Short Leave Suggestion**: If the employee punched out early (based on shift end time and `sl_end_limit`), it flags the record as `is_early_out` and suggests applying a Short Leave to cover the deficit.

---

## 2. Core Actions & Rules

### A. Approval & Chronological Rule (`approve`, `bulkApprove`, `postDaily`)
The system strictly enforces **Chronological Approval**. 
- A manager **cannot** approve attendance for Date X if the user has pending (unapproved) attendance on Date Y (where Date Y < Date X).
- If attempted, the API blocks the request with a 422 error. This ensures attendance is processed sequentially without gaps.

### B. Holiday / Weekly Off Credits (`creditHolidayWorking`)
When an attendance record is approved, the system checks if the date was a **Holiday** or a **Weekly Off**.
- If the employee worked on that day, they are rewarded with a **Compensatory Off** (Comp Off).
- **Credit Value**: 
  - 1.0 Full Day credit for working from Office.
  - 0.5 Half Day credit for working from Home (WFH).
- **Ledger Update**: Automatically finds or creates a Leave Type (e.g., "Holiday Working") and adds the credit to the user's `LeaveLedger`.

### C. Rejection (`reject`)
- A manager can reject an attendance record (setting `is_approved = 2`).
- A mandatory reason must be provided.
- The system automatically sends an email (`AttendanceRejectedMail`) to the employee notifying them of the rejection and the reason.

### D. Manual Time Updates & Overrides (`updateTimes`)
HR can manually adjust the In/Out times of an employee.
- **Audit Logging**: Any time change creates a record in `AttendanceEditLog` storing who changed it, old times, new times, and the reason.
- **Movements**: Modifies the first IN movement and last OUT movement. If no OUT movement existed, it creates a manual one.
- **Status Override**: HR can optionally force a specific status (e.g., forcing "Present" despite short hours). This sets `is_overridden = true`. If not forced, it recalculates the status based on the new manual times.

### E. Applying Quick Leave (`applyQuickLeave`)
If an employee is absent or short on hours, HR can quickly apply a leave directly from the approval screen to cover it.
- **Balance Check**: Checks `LeaveLedger` to ensure the user has enough balance (1 day for Full/Short, 0.5 for Half).
- **Request Creation**: Auto-creates an "Approved" `LeaveRequest`.
- **Ledger Deduction**: Instantly deducts the balance from the `LeaveLedger` with a 'debit' transaction type.

### F. Marking Manual Attendance (`markAttendance`)
If an employee forgot to punch in entirely, HR can manually create the attendance record.
- Creates the `Attendance` record and sets it to auto-approved (`is_approved = 1`).
- Creates the necessary `Movement` records for IN (and optionally OUT).
- Triggers the `creditHolidayWorking` logic in case HR is manually marking attendance for a weekend/holiday.

### G. Voiding Attendance (`voidAttendance`)
If a punch-in was a mistake (e.g., punched in on mobile by accident while on leave), HR can void it.
- Completely deletes the `movements` and the `attendance` record from the database.
- The frontend will then reflect the user as "Absent" or "On Leave".
