# Implement Employee Loan System

This plan outlines the architecture and implementation steps for introducing an employee loan system. It accommodates dynamic rules like maximum loan percentage based on employment type, flexible installments, and dynamic skip policies.

## User Review Required
> [!IMPORTANT]
> Please review the data model and workflows. Specifically, confirm how we handle automatic payroll deductions if an installment is due in the current month.

## Open Questions Resolved
1. **Approval Workflow**: Yes, loan requests will require approval before becoming active.
2. **Deductions**: Yes, loan installment deductions will automatically be integrated into the monthly Payroll generation.
3. **Validation Threshold**: Skipped for now.

## Proposed Changes

### Database & Migrations

#### [NEW] `database/migrations/xxxx_xx_xx_add_max_loan_percentage_to_employment_types_table.php`
- Add a `max_loan_percentage` column (decimal) to cap how much an employee can request based on their gross salary.

#### [NEW] `database/migrations/xxxx_xx_xx_create_loans_table.php`
- `id`, `employee_id`
- `amount` (Total loan amount)
- `total_installments`
- `installment_amount`
- `status` (Enum: pending, approved, active, completed, rejected)
- `reason` (Text description)

#### [NEW] `database/migrations/xxxx_xx_xx_create_loan_installments_table.php`
- `id`, `loan_id`
- `installment_number`
- `due_month` (Date/String denoting when the deduction should happen)
- `amount`
- `status` (Enum: pending, paid, skipped)
- `skip_strategy` (Nullable Enum: `add_to_next`, `extend_period`)
- `paid_on` (Nullable Date)

### Models

#### [MODIFY] `app/Models/EmploymentType.php`
- Add `max_loan_percentage` to the `$fillable` array.

#### [MODIFY] `app/Models/Employee.php`
- Add a `loans()` relationship returning `hasMany(Loan::class)`.

#### [NEW] `app/Models/Loan.php`
- Define `employee()` and `installments()` relationships.
- Create helper methods like `remainingBalance()`.

#### [NEW] `app/Models/LoanInstallment.php`
- Define `loan()` relationship.

### Services and Logic

#### [NEW] `app/Services/LoanService.php` (or LoanController)
- **Request Creation**: 
  - Validate requested `amount <= (Employee's Gross Salary * EmploymentType's max_loan_percentage / 100)`.
  - Distribute the loan into `N` equal installments.
- **Skip Installment Logic**:
  - `add_to_next`: Mark current installment as `skipped`. Find the next pending installment and add the skipped amount to it.
  - `extend_period`: Mark current installment as `skipped`. Create a new installment at the end of the loan schedule with the skipped amount, increasing the `total_installments` by 1.
- **Insufficient Salary Handling (Payroll Integration)**:
  - If during payroll generation, the take-home salary is less than the scheduled installment amount, apply one of two dynamic options:
    - **Option 1 (Partial Deduction)**: Deduct only what the take-home salary can cover. The leftover installment amount is added to the next month's installment (or extended).
    - **Option 2 (Full Skip)**: Skip the entire installment for this month (using the skip logic defined above).

### UI & Routes

#### [MODIFY] `routes/web.php` or `routes/api.php`
- Add endpoints for `/loans` (Resource route), `/loans/{loan}/skip-installment`.

#### Frontend Views (Blade/Tailwind)
- **Design Guidelines**: Ensure all new views match the existing UI/UX and layout structure of `resources/views/myleads.blade.php`.
- **[NEW] `resources/views/loans/index.blade.php`**: Loan Dashboard for employees to view their loans and request new ones.
- **[NEW] `resources/views/loans/create.blade.php`**: Loan Request Form with dynamic validation against maximum limits.
- **[NEW] `resources/views/loans/admin_index.blade.php`**: Admin Dashboard to view/approve loans and manage dynamic installment skipping.
- **[MODIFY] `resources/views/employment_types/edit.blade.php`**: Add a field for configuring `max_loan_percentage`.

## Verification Plan

### Manual Verification
- Create a test employee with a salary of $10,000 and an employment type with a 50% max loan limit.
- Attempt to request a $6,000 loan (should fail).
- Request a $4,000 loan for 4 months ($1,000/month).
- Trigger the "skip installment" feature using the `add_to_next` strategy and verify the next month's deduction becomes $2,000.
- Trigger the "skip installment" feature using the `extend_period` strategy and verify a 5th installment is created for $1,000.
