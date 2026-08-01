# Implementation Plan: Rotational Shifts and Historical Shift Tracking

Currently, the software assigns a single shift to an employee. When generating attendance reports for past dates, the system uses the employee's *current* shift, which causes inaccuracies if the employee previously worked a different shift.

To support rotational shifts and ensure historical accuracy, we need to implement a mechanism to track which shift an employee was assigned to on any given date.

## User Review Required

> [!IMPORTANT]
> The proposed solution introduces an "Effective Date" based shift assignment history. Please review this updated approach, which incorporates your feedback, before we begin execution.

## Proposed Changes

We will introduce a new model `EmployeeShift` to track the history of shift assignments. As requested, managers will manually change the shift from the **Employee Master**, and the system will log this change with an effective date.

### 1. Database & Models

#### [NEW] `database/migrations/xxxx_xx_xx_xxxxxx_create_employee_shifts_table.php`
Create a table to store shift assignment history:
- `id`
- `employee_id` (Foreign Key)
- `shift_id` (Foreign Key)
- `effective_from` (Date)
- `created_at`, `updated_at`

#### [NEW] `app/Models/EmployeeShift.php`
Create the model with relationships to `Employee` and `Shift`.

#### [MODIFY] `app/Models/Employee.php`
Add a relationship and a helper method. **CRITICAL UPDATE: To prevent N+1 query performance issues when generating reports, the helper method must filter the eager-loaded collection in-memory, instead of running a database query every time.**
```php
public function shiftHistory()
{
    // Ensure the results are ordered by effective_from DESC and then by ID DESC (to handle multiple updates on the same day)
    return $this->hasMany(EmployeeShift::class, 'employee_id')
                ->orderBy('effective_from', 'desc')
                ->orderBy('id', 'desc');
}

// Helper method to get shift for a specific date
public function getShiftForDate($date)
{
    $targetDate = \Carbon\Carbon::parse($date)->startOfDay();
    
    // Iterate over the eager-loaded collection to prevent N+1 queries in loops
    foreach ($this->shiftHistory as $record) {
        if (\Carbon\Carbon::parse($record->effective_from)->startOfDay()->lte($targetDate)) {
            return $record->shift;
        }
    }
    
    return $this->shiftRelation; // Fallback to current shift
}
```

### 2. Logic Updates (Controllers, Services & Commands)

Every place in the codebase that calculates attendance for a specific date will be updated to use `getShiftForDate($date)` instead of `$employee->shiftRelation`. **All queries that eager load `shiftRelation` (e.g. `User::with('employee.shiftRelation')`) MUST be updated to eager load `shiftHistory.shift` as well.**

#### [MODIFY] `app/Services/AttendanceReportService.php` & `app/Services/DailyStatusService.php`
- Modify `calculateMonthlySummary`, `generateDailyBreakdown`, and `getStatus` methods. Update eager loading and fetch the shift dynamically for *each day* in the loop.

#### [MODIFY] `app/Http/Controllers/AttendanceController.php` & `app/Http/Controllers/Api/AttendanceController.php`
- Update report generation loops to fetch the correct shift per date.
- Update daily punch-in logic (late allowances, etc.) to evaluate based on the active shift.

#### [MODIFY] `app/Http/Controllers/LeaveController.php` & `app/Http/Controllers/Api/LeaveController.php`
- Update leave duration calculations, short leave timing validation, and weekoff logic to evaluate the shift active on the date of the leave application.

#### [MODIFY] `app/Http/Controllers/TrackingController.php` & `app/Http/Controllers/Api/TrackingReportApiController.php` & `app/Http/Controllers/Api/AttendanceReportApiController.php`
- Update API endpoints that generate historical tracking and attendance data to use the date-specific shift.

#### [MODIFY] `app/Http/Controllers/AttendanceApprovalController.php`
- Ensure attendance approvals validate against the shift rules active on the date of the attendance record.

#### [MODIFY] Payroll & CRON Commands
- `app/Http/Controllers/Payroll/MonthlyAttendanceReviewController.php`: Ensure salary and overtime logic uses daily shift data.
- `app/Console/Commands/DailyLeaveAccrual.php` & `app/Console/Commands/BackfillAccruals.php`: Ensure leave accrual checks weekoffs based on the correct shift.
- `app/Console/Commands/SendNightAttendanceMail.php`: Check punch out based on yesterday's shift.

#### [MODIFY] API Profile endpoints
- `app/Http/Controllers/Api/AuthController.php`, `KioskAuthController`, and `EmployeeController`: Replace eager-loaded `shiftRelation` references with `$shift = $employee->getShiftForDate(today())`. Explicitly map this `$shift` object into the JSON payload (e.g. `['shift_relation' => $shift]`) to maintain backward compatibility with mobile apps and frontends.

#### [NEW] Database Integrity & Cascades
- In the `employee_shifts` migration, `employee_id` MUST be defined with `onDelete('cascade')` and `shift_id` MUST be defined with `nullOnDelete()` and be nullable, matching the `employees` table schema to prevent fatal errors if a shift is deleted.

#### [MODIFY] Employee Master Updates (Using Model Events)
To ensure 100% robustness (covering UI, API, bulk uploads, or future CLI scripts), we will NOT manually track shifts inside controllers. Instead, we will use Eloquent Model Events in `app/Models/Employee.php`:
- **`created` event**: If a shift is assigned at creation, automatically insert a record into `employee_shifts` with `effective_from = $employee->date_of_joining ?: today()`.
- **`updated` event**: If `wasChanged('shift_id')` is true, insert a new record. We will check `request('shift_effective_date')` to allow UI to pass a custom effective date (future or past). If absent, it defaults to `today()`.
- **Note on Future Shifts**: If a manager assigns a shift with a *future* date, `shift_id` on the `employees` table will update immediately, BUT `getShiftForDate(today())` will still correctly return the older shift until the future date arrives. This makes `getShiftForDate()` the sole source of truth.

### 3. Migration of Existing Data

#### [NEW] `database/migrations/xxxx_xx_xx_xxxxxx_seed_initial_employee_shifts.php`
Create a one-time migration or command to populate `employee_shifts` for all existing employees based on their current `shift_id`. 
**CRITICAL:** The `effective_from` date MUST be set to a date far in the past (e.g., their `date_of_joining` or `2000-01-01`). If it is set to `today`, then older attendance records will fall back to `shiftRelation`, which might represent a *future* shift if the employee's shift is changed again tomorrow. By setting the initial record to a very old date, we guarantee it covers all historical attendance records up until their first actual rotational shift change.

## Verification Plan

### Manual Verification
1. Open the Employee Master and assign an employee to "Shift A".
2. View their attendance report for today (verifying Shift A rules apply).
3. From the Employee Master, assign the employee to "Shift B" with an effective date of tomorrow.
4. View the attendance report for today (should still show Shift A).
5. Fast-forward to tomorrow and view the report (should now show Shift B).
