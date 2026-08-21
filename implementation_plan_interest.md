# Add Interest Rates to Loans

Currently, the loan management system handles interest-free loans by dividing the principal amount by the EMI amount. This plan outlines the necessary changes to support loans with an interest rate, using the standard banking Reducing Balance method.

## Proposed Changes

### Database Changes
1. **Create `loan_interests` Table:** Create a new migration `create_loan_interests_table.php` to store the global interest rate.
   - `id`
   - `interest_rate` (decimal, e.g. annual percentage rate)
   - `timestamps`
2. **Update `loans` Table:** Create a new migration `add_interest_fields_to_loans_table.php` to add the following columns:
   - `applied_interest_rate` (decimal): To lock in the interest rate at the time the loan was created (so future rate changes don't affect old loans).
   - `total_interest` (decimal): The total amount of interest projected to be paid.
   - `total_payable` (decimal): The principal + total interest.
3. **Update `loan_installments` Table:** Create a new migration `add_interest_component_to_loan_installments_table.php` to add:
   - `principal_component` (decimal)
   - `interest_component` (decimal)

### Models
#### [NEW] `LoanInterest.php`
- Model to manage the global interest rate setting.

#### [MODIFY] [Loan.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/app/Models/Loan.php)
- Add helper methods to calculate the outstanding interest and total remaining payable amount based on the new fields.

#### [MODIFY] [LoanInstallment.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/app/Models/LoanInstallment.php)
- Add `principal_component` and `interest_component` to `$guarded` or `$fillable`.

### Controllers
#### [MODIFY] [LoanController.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/app/Http/Controllers/LoanController.php)
- **Interest Retrieval:** In the `store` method, fetch the current active interest rate from the `loan_interests` table.
- **EMI Calculation (Reducing Balance Loop):** Update the `while ($remainingAmount > 0)` loop. For each month:
  1. Calculate Monthly Interest = `Remaining Principal * (Annual Interest Rate / 12 / 100)`
  2. Calculate Principal Component = `Requested EMI Amount - Monthly Interest`
     - *(Note: If the Requested EMI is less than the Monthly Interest, the loan will never be paid off. We must add validation to ensure the requested EMI is greater than the first month's interest).*
  3. `Remaining Principal -= Principal Component`
  4. Create the `LoanInstallment` saving both the principal and interest components for that month.
- **Save Totals:** Accumulate the total interest across all loops and save it to the `loans` table.

### Views (UI)
#### [MODIFY] [resources/views/loans/index.blade.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/resources/views/loans/index.blade.php) (and other relevant views)
- Update the display tables to show the `Applied Interest Rate`, `Total Interest`, and `Total Payable` amounts alongside the principal.
- (Optional but recommended) Add a settings page or section for an Admin to update the value in the `loan_interests` table.

## Verification Plan
### Automated / Logic Checks
- Ensure validation blocks submission if `Requested EMI <= First Month Interest`.

### Manual Verification
- Set the global interest rate to 10% annually.
- Request a loan of 10,000 with an EMI of 1,000.
- Verify the first installment has an interest component of ~83.33 (10,000 * 10% / 12) and a principal component of ~916.67.
- Verify the second installment calculates interest on the new remaining balance (10,000 - 916.67 = 9083.33).
- Check the generated installment schedule to ensure the sum of all installments equals the newly calculated `total_payable` amount.
