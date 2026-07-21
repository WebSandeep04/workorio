# Workorio Dynamic Payroll Module - Implementation Plan

This document outlines the architecture, data models, and integration plan for building a robust, dynamic Payroll Module into the existing Workorio HRMS.

## ✅ Approved Architectural Decisions

Based on user feedback and deep analysis of the existing codebase, the following core decisions have been finalized:

1. **Multi-Tenant Architecture**: We will use the existing `TenantModel` multi-database architecture. Consequently, `company_id` will be omitted from the payroll schemas because the data is securely isolated per tenant database.
2. **Formula Engine**: We will implement `symfony/expression-language` as the formula parsing engine. It is the industry standard in PHP for safely evaluating complex math and logic formulas (e.g., `(basic * 0.4) + 1500`).
3. **Attendance Integration & "Dummy User" Fallback**: 
   - The payroll engine will rely entirely on a **locked/finalized monthly attendance summary**. It will not calculate Loss of Pay (LOP) dynamically on-the-fly during payroll processing.
   - The existing `attendance` table is strictly linked to `user_id`. **For employees who do not have system access but require attendance tracking, the HR team will create a "Dummy User" account.** This allows the system to log their attendance seamlessly without requiring architectural rewrites to the core attendance module.
4. **Strict Unlock Workflow**: Amending past attendance or payroll will follow a strict deletion workflow to prevent stale data.

---

## 🗄️ Proposed Database Architecture & Models

All new models will extend standard `Illuminate\Database\Eloquent\Model`.

### 1. Salary Components (`salary_components`)
Master list of earnings, deductions, and employer contributions.
- `id`, `name`, `type` (earning, deduction, employer_contribution)
- `calculation_type` (fixed, percentage, formula, rule), `is_active`

### 2. Salary Structures (`salary_structures`)
Defines the structure templates (e.g., Staff, Manager).
- `id`, `name`, `salary_type` (fixed, structured)

### 3. Salary Structure Components (`salary_structure_components`)
Pivot table linking components to structures with their specific values/formulas.
- `id`, `salary_structure_id`, `salary_component_id`
- `value`, `formula`

### 4. Monthly Attendance Summaries (`monthly_attendance_summaries`)
The locked/finalized attendance data, directly mirroring the `AttendanceReportService` output to prevent recalculations during payroll generation.
- `id`, `employee_id`, `month`, `year`, `is_locked` (boolean)
- **Core Days**: `total_working_days`, `days_worked`, `days_absent`, `attendance_percentage`
- **Presents**: `total_present_combined`, `total_present`, `total_halfday`, `total_weekly_offs_worked`, `total_holidays_worked`
- **Leaves/Holidays**: `days_on_leave` (Paid), `total_unpaid_leaves` (LOP), `total_short_leaves` (SL), `total_weekly_offs`, `total_holidays`
- **Hours**: `total_hours`, `total_office_hours`, `total_field_hours`, `total_break_time`, `avg_hours_per_day`
- **Late/Exceptions**: `total_less_8_30`, `total_more_8_30`, `late_count`, `total_late_minutes`
- **JSON Data**: `total_cycles` (JSON), `late_logs` (JSON)

### 5. Payroll Settings (`payroll_settings`)
Configures how payroll is processed per tenant.
- `id`, `salary_cycle_start`, `salary_cycle_end`
- `attendance_based` (boolean), `pf_enabled`, `esi_enabled`, `pt_enabled`, `tds_enabled`

### 6. Statutory Rules (`statutory_rules`)
Dynamic configuration for government rules.
- `id`, `type` (PF, ESI, PT, TDS)
- `employee_rate`, `employer_rate`, `salary_limit`, `calculate_on`

### 7. Employee Salary (`employee_salaries`)
Assigns a structure to an employee and sets their gross.
- `id`, `employee_id` (Foreign Key to existing `employees` table)
- `salary_structure_id`, `gross_salary`, `effective_from`

### 8. Payrolls (`payrolls`)
The master record for a monthly payroll run.
- `id`, `month`, `year`, `status` (Draft, Processed, Locked)

### 9. Payroll Details (`payroll_details`) & Components (`payroll_component_details`)
The calculated totals and granular breakdown for a specific employee in a payroll run.
- `id`, `payroll_id`, `employee_id`, `gross_salary`, `net_salary`, etc.
- `id`, `payroll_detail_id`, `salary_component_id`, `amount`

---

## ⚙️ Workflow & Integration Rules

These rules dictate how the new module interacts with the existing codebase:

1. **Dynamic Salary Cycles**: The `payroll_settings.salary_cycle_start` and `salary_cycle_end` fields will store integers (1-31). 
   - Inserting `31` acts as a universal "End of Month" flag. The backend (Laravel Carbon) will automatically adjust `31` to `28`, `29`, or `30` depending on the current month.
   - For mid-month cycles (e.g., 26th to 25th), the backend will automatically recognize that the start date (26) belongs to the previous month since it is numerically higher than the end date (25).
2. **Leave Management Synergy**: The system must dynamically check if absent days are covered by approved, *paid* leave requests (`LeaveRequest`) to avoid unnecessary Loss of Pay (LOP) deductions during the summary generation.
3. **Advances & Loans Distinction**: The current `Advance` model is linked to customer sales (`sales_record_id`). For future "Advance Salary" features, a distinct table (e.g., `employee_advances`) must be created.
4. **Late Minutes & Overtime**: The logic for penalizing late arrivals from the `Shift` model (grace periods, bounce days) is strictly executed *during* the generation of the `monthly_attendance_summaries`.
5. **Amending Past Attendance (Unlock Workflow)**: If attendance needs to be amended after being locked, the workflow in the UI is strict to prevent stale data:
   - First, the Admin clicks **"Void/Delete Payslip"** (deleting the payroll record).
   - Second, the Admin clicks **"Unlock/Delete Summary"** (physically deleting the row from `monthly_attendance_summaries`).
   - The daily attendance punches are edited.
   - The Admin clicks **"Finalize & Lock"** to insert a brand new, recalculated row into the summary table.
   - Payroll is regenerated.

---

## 🚀 Execution Plan (How we will build it)

### Phase 1: Database & Models (Backend Foundation)
- [ ] Create migrations for the 9 tables defined above.
  - *Note: Every migration must include `if (Schema::getConnection()->getName() === 'mysql') { return; }` at the top of the `up()` method to ensure they only run on tenant databases, following the existing project convention.*
- [ ] Create Eloquent models extending standard `Illuminate\Database\Eloquent\Model`.
- [ ] Define relationships (e.g., Employee `hasOne` EmployeeSalary).
- [ ] Update `App\Models\Tenant` to include `is_payroll_enabled` feature flag.

### Phase 2: Payroll Engine Service (Core Logic)
- [ ] Create a `PayrollCalculationService`.
- [ ] Implement Formula Parser for dynamic components.
- [ ] Implement Statutory Calculator.
- [ ] Implement Attendance Integration (calculating working days, LOP, and prorating earnings).
  - **Bug Fix Required:** Update `app/Services/AttendanceReportService.php` to include `'total_holidays' => $totalHolidays` in the return array at the end of `calculateMonthlySummary`.

### Phase 3: Web Controllers & Routes
- [ ] Create standard Web Controllers for Salary Components, Structures, and Statutory Rules to handle Blade views and form submissions.
- [ ] Create endpoints/actions for processing payrolls (batch processing).
- [ ] Implement locking/unlocking mechanisms for processed payrolls via controller actions.

### Phase 4: Frontend (Blade) Integration (UI Architecture)
- **Reference UI**: All new views must strictly follow the UI pattern established in `http://localhost:8000/branches` (`branches.blade.php`).
- **Core UI Components**:
  - **Tables**: Use the `.modern-card .data-table-card` wrapper with `.custom-table`. Include sticky headers (`position: sticky`), clear borders, and row hover effects (`transform: translateY(-1px)`).
  - **Pagination**: Implement dynamic jQuery-based pagination (`buildSimplePagination()`) paired with a metadata display (`Showing X-Y from Z data`).
  - **Modals**: Use `.modal-modern` with `.modal-header` (solid background `#434AFA`), `.form-label-modern`, and `.form-control-modern` for all Create/Edit forms.
  - **Action Buttons**: Use inline `.btn-action` (edit/delete) within table rows and `.table-search-btn` for top-level actions (Add).
- **Client-Side Logic (jQuery AJAX)**:
  - **No Page Reloads**: All CRUD operations must use asynchronous `$.ajax` calls (GET for listing, POST/PUT for saving, DELETE for removal).
  - **Loading States**: Display a `.loading-state` spinner (`bi-arrow-repeat spin`) in the table body while fetching data, and disable submit buttons with a spinner during form submission.
  - **Error/Success Handling**: Use the global `showAlert(type, message)` for toast notifications on success/failure, and display inline modal errors (`#modalError`) for validation failures.
- **Specific Screens**:
  - [ ] Build Blade views for Payroll Settings and Component Management using the reference table and modal architecture.
  - [ ] Build Salary Structure Assignment interface.
  - [ ] Build Monthly Attendance Review screen (with **"Finalize & Lock"** and **"Unlock/Delete Summary"** buttons).
  - [ ] Build Payroll Processing dashboard (with batch generate, view payslips, and **"Void/Delete Payslip"** buttons).
  - [ ] Professional PDF Payslip Generation using a backend library like `barryvdh/laravel-dompdf`.

## Verification Plan

### Automated & Manual Verification
- Write PHPUnit tests for the `PayrollCalculationService`.
- Test end-to-end flow in a test tenant database: Define rules -> Assign to Employee -> Run Payroll -> Generate Payslip -> Verify Net Salary matches expected manual calculation.
