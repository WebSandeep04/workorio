# Leave Management System - Complete Architecture & Flow

This document details the complete flow, logic, and files involved in the Leave Management System. The system supports dynamic leave types (Paid, Unpaid, SL, RH), hierarchical approvals, ledger-based balance tracking, and integration with the Core Attendance Engine.

## Core Models & Database Tables

1. **`LeaveType.php`** (`leave_types` table)
   - The root configuration for leaves.
   - Key attributes: `is_paid`, `is_deductible` (requires ledger balance), `is_short_leave` (SL), `is_restricted` (RH), `allow_half_day`, `full_day_weight`, `half_day_weight`, `quota_type` (monthly vs yearly).

2. **`EmploymentTypeLeaveRule.php`** (`employment_type_leave_rules` table)
   - Links a `LeaveType` to an `EmploymentType`.
   - Defines how leaves are given: `generation_type` (`prefill`, `accrual`, `unlimited`), `value` (quota), and `max_use_per_month`.

3. **`LeaveRequest.php`** (`leave_requests` table)
   - Stores the employee's request.
   - Key attributes: `start_date`, `end_date`, `total_days`, `status` (pending, approved, rejected, cancelled), `is_half_day`, `has_attendance_overlap`.
   - Special fields for SL: `start_time`, `end_time`, `sl_period`.

4. **`LeaveLedger.php`** (`leave_ledgers` table)
   - Tracks the double-entry accounting for leaves.
   - Every transaction (credit, debit, lapsed) is recorded here, ensuring the `balance_after` is always mathematically verifiable.

## Service Layer

1. **`LeaveBalanceService.php`**
   - **`getBalance()`**: Fetches the latest `balance_after` from `LeaveLedger`. If no ledger exists but the user has an `unlimited` rule, it returns 365.
   - **`creditLeave()` / `debitLeave()`**: Uses database transactions to safely insert a new ledger row with the updated balance.
   - **`initializePrefillLeaves()`**: Called once per user to grant initial balances based on their `EmploymentType`.

2. **`AttendanceReportService.php`**
   - The integration point where Leaves meet Attendance.
   - Uses `determineStatusAndReason()` to upgrade/downgrade status. For example, if a user has an approved Half Day leave (`HD`), their required hours are halved. If they have an SL, the SL duration is added to their physical worked hours to help them hit a Full Day.

## Application Flow

### 1. Leave Request Submission
*Handled by `LeaveController@store` (Web) and `Api\LeaveController@store` (Mobile API).*

- **Validation**: Checks dates, ensures `start_date` is in the future (unless SL, which allows today).
- **Duration Calculation**: Uses `LeaveType` weights. Half days use `half_day_weight` (e.g. 0.5), Full days calculate date diff * `full_day_weight` (e.g. 1.0).
- **Rule Checks**:
  - Checks `max_use_per_month` against existing approved/pending leaves in that calendar month.
  - Checks if the user already has an overlapping leave on those dates.
- **SL Timing Validation (`is_short_leave`)**:
  - Requires the user to have an active `Shift` mapped to their `Employee` record.
  - Dynamically calculates the `eveningMin` based on `shift->end_time` minus `shift->sl_end_limit` (hours).
  - Automatically forces the `sl_period` to `evening` and overrides `start_time` and `end_time` to match exactly this tail-end window of the shift.
  - If the requested time is outside this calculated window, it forcefully rejects the request.
- **RH Validation (`is_restricted`)**:
  - Queries the `Holiday` table to verify if the requested `start_date` actually exists and has `is_rh = 1`. If it's not a configured RH, it throws an error.
- **Monthly Usage Check (`max_use_per_month`)**:
  - If the user's `EmploymentTypeLeaveRule` has `max_use_per_month > 0`, the system queries all `pending` and `approved` leaves of this exact type for the given calendar month.
  - If the `(existing month usage + requested totalDays)` exceeds the rule limit, it blocks the submission.
- **Balance Verification (`is_deductible`)**: 
  - If the leave is deductible, calls `LeaveBalanceService@getBalance` to strictly ensure `balance > requested_days`.
- **Immediate Deduction**: If successful, creates the `LeaveRequest` (status: `pending`) and **immediately debits** the ledger so the balance is mathematically locked and cannot be double-spent.

### 2. Approval Workflow
*Handled by `LeaveController@updateStatus`.*

- **Approve**: Status changes to `approved`. Ledger deduction remains locked. Attendance grid now reflects the leave instantly.
- **Reject / Cancel / Delete**: Status changes to `rejected` or `cancelled`. The system calls `LeaveBalanceService@creditLeave` to refund the exact `total_days` back to the employee's ledger. This ensures that balances are always 100% accurate, even if admins cancel leaves months later.
- **Overlap Checks (During Approval)**: If an admin tries to approve a leave, the system performs a rigid check against any existing `approved` leaves for the same user on overlapping dates. If found, it blocks the approval to prevent double-booking.

### 3. Attendance Evaluation
*Handled by `AttendanceReportService` during report generation.*

- When building the report grid, the system maps all approved leaves by date (`$userLeavesDetails`).
- Inside `determineStatusAndReason()`:
  - **Full Day Leave (`L`)**: Status is `On Leave`.
  - **Restricted Holiday (`RH`)**: Status is `Restricted Holiday`.
  - **Half Day Leave (`HD`)**: The `halfDayHr` is halved. If the user works the remaining half, they get `P2` (Approved Half Day).
  - **Short Leave (`SL`)**: The shift's `sl_end_limit` hours are appended to the physical working hours. If the sum hits the `Full Day Hr` threshold, they get `P (SL)` (Present with SL). If it doesn't, the SL is ignored and they are marked Absent/Halfday strictly based on physical hours.

## Key Edge Cases Handled

1. **Pending Leaves block quotas**: A pending leave immediately deducts from the balance. This prevents an employee from requesting 5 days twice while having a balance of only 5 days.
2. **SL Timing Window Override**: Short leave isn't just an arbitrary 2 hours; it dynamically reads the user's current shift and enforces that the SL can *only* happen at the exact tail-end of the shift, hardcoding the start and end times to match the shift's `end_time` minus the allowed limit.
3. **Multi-Tenant Safety**: All leave rules and histories are strictly bound by the multi-tenant architecture, ensuring data isolation.
4. **Grace and Overtime Rules Integration**: When an employee works on a Weekly Off or Holiday and is also late (exhausting grace periods), they are NOT penalized to a Halfday *unless* the shift explicitly lacks the `exempt_grace_on_overtime` toggle.
5. **No Double-Dipping Leaves**: An employee cannot have a Half Day leave and a Short Leave on the same day that stack. The system explicitly prevents them from covering their missing hours by stacking multiple leave types.
6. **Compensatory Off (Comp Off) Crediting**: When an admin approves an attendance record via `AttendanceApprovalController`, the system fires `creditHolidayWorking()`. If the employee worked on a Weekly Off or Holiday AND they strictly satisfied the `enforce_time_restriction_on_overtime` requirement (yielding a true `W/O-W` or `H/W` status), the system automatically credits their Leave Ledger with `1.0` day under the "Compensatory Off" (or "Holiday Working") leave type so they can use it later.
