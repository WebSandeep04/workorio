# Payroll Exemption Feature

The purpose of this feature is to allow authorized users to exempt employees from the late penalty calculation for specific dates. Exemptions can be granted at the Branch, Department, or Individual Employee level.

## User Review Required

No major architectural concerns. The plan directly addresses the requirements. Please review the proposed changes and let me know if you approve.

## Proposed Changes

### Database Changes

#### [NEW] `create_penalty_exemptions_table`
Create a new migration for the `penalty_exemptions` table:
- `id`
- `type` (Enum: 'branch', 'department', 'employee')
- `branch_id` (foreign key, nullable)
- `department_id` (foreign key, nullable)
- `employee_id` (foreign key, nullable)
- `date` (date)
- `reason` (string, nullable)
- timestamps

#### [NEW] `add_exemption_columns_to_payroll_penalties_table`
Create a migration to add audit columns to the existing `payroll_penalties` table:
- `total_late_count` (integer, default 0)
- `exempted_late_count` (integer, default 0)
*(The existing `late_count` column will represent the final "Penalty-Eligible Lates").*

### Models & Services

#### [NEW] `app/Models/PenaltyExemption.php`
- Model definition for the new table.
- Relationships to `Branch`, `Department`, and `Employee`.

#### [MODIFY] `app/Services/PayrollCalculationService.php`
- Update `calculateEmployeePayroll()` logic:
  - Fetch all `penalty_exemptions` in the payroll month matching the employee, their department, or their branch.
  - Query the `attendances` table for those specific dates where `is_late = 1` for the employee.
  - The count of those matching attendances becomes the `exempted_late_count`.
  - Calculate `actual_late_count` = `total_late_count` - `exempted_late_count`.
  - Use `actual_late_count` to calculate the penalty.
  - Store the `total_late_count` and `exempted_late_count` in the `PayrollPenalty` model.

### Controllers & Routing

#### [NEW] `app/Http/Controllers/Payroll/PenaltyExemptionController.php`
- `index()`: Serve the UI view.
- `fetch()`: Return JSON data for the DataTable.
- `store()`: Validate and create a new exemption.
- `destroy()`: Delete an existing exemption.

#### [MODIFY] `routes/web.php`
- Add the necessary routes under the `/payroll` group.

### UI & Reporting

#### [MODIFY] `config/menu.php`
- Add an "Exemptions" option under the Payroll section in the sidebar.

#### [NEW] `resources/views/payroll/exemptions.blade.php`
- Create the Exemption management view.
- Include a DataTable to list exemptions.
- Add an "Add Exemption" modal with dynamic fields (Branch/Department/Employee dropdowns will toggle based on the selected Exemption Type).

#### [MODIFY] `app/Http/Controllers/Payroll/PayrollController.php`
- Update `fetchReportData()`, `exportReport()`, and `exportReportPdf()` to fetch and pass the new penalty audit columns (`total_late_count`, `exempted_late_count`).

#### [MODIFY] `resources/views/payroll/report.blade.php` & `resources/views/payroll/pdf/report.blade.php`
- Add columns: `Total Lates`, `Exempted Lates`, and `Eligible Lates` before the `Penalty Days` column.

---

## Verification Plan

### Automated/Code Verification
- Ensure `PenaltyExemption` creation routes correctly validate relationships based on the selected type.
- Ensure `PayrollCalculationService` correctly subtracts exemptions only when the employee actually had a late attendance on the exempted date.

### Manual Verification
- Go to Payroll > Exemptions and add a test exemption for an employee on a known late date.
- Generate the payroll report.
- Verify the `Total Lates`, `Exempted Lates`, and `Eligible Lates` show correctly and the final penalty amount reflects the reduced late count.
