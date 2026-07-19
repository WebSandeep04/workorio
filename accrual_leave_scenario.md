# Leave Accrual Scenario & Architecture

This document explains exactly how the "Leave Accrual" feature is currently implemented and operates within your web application based on the codebase analysis.

## Overview
Leave Accrual is an automated process designed to reward employees with leave balances (e.g., 1 paid leave) after they have successfully completed a specific number of "valid" working days (e.g., 30 days).

The core engine driving this is an Artisan command named `leave:daily-accruals`.

## How the Code Works (Step-by-Step)

The logic lives in `app\Console\Commands\DailyLeaveAccrual.php`. Here is the sequence of events when this process runs:

### 1. Target Evaluation Date
When the system runs the accrual logic, it always evaluates the attendance of **"Yesterday"**. 
*Code: `$targetDate = Carbon::yesterday()->format('Y-m-d');`*

### 2. User Eligibility Check
The system fetches all **Active** users. For each user, it verifies:
* Do they have an assigned `employment_type_id`? (If not, they are skipped).

### 3. Valid Working Day Validation
To count "Yesterday" as a valid day towards their accrual target, the system checks if the user actually worked. 
* Currently, it does this by checking if the user has **any entry in the `Worklog` table** for yesterday. 
* If no worklog is found, the system assumes the user was absent or on Leave Without Pay (LWP) and skips them for that day.
*(Note: There is commented-out code suggesting that being on an "approved paid leave" could also count as a valid working day, but it is currently disabled in favor of strictly checking Worklogs).*

### 4. Fetching Accrual Rules
If the user had a valid worklog, the system looks up the `EmploymentTypeLeaveRule` table. It filters for rules linked to the user's specific Employment Type where the `generation_type` is set to **'accrual'**.

### 5. Tracking Progress (The Counter)
For every valid accrual rule found, the system pulls up a specific tracker for that user and leave type in the `LeaveAccrualCounter` table.
* The system increments the `valid_days_count` by **1**.

### 6. Threshold Check & Reward
The system checks if the user has reached their target threshold (e.g., 30 days).
*Code: `if ($counter->valid_days_count >= $rule->value)`*

If they hit the limit:
1. **The Reward:** The system calls `LeaveBalanceService::creditLeave()`. It creates a new transaction in the `LeaveLedger` table, giving the user exactly **+1.00** leave balance of that specific leave type.
2. **The Reset:** The `valid_days_count` in the `LeaveAccrualCounter` is reset back to **0** so the user can start building towards their next accrued leave.
3. **The Log:** A success log is generated (e.g., `"Credited 1 leave to UID {id} for type {leave_type_id}"`).

## Important Observations & Missing Links

1. **Automation Schedule:** 
   While the logic for accruals is robust, the command `leave:daily-accruals` is **not currently scheduled** in your `routes/console.php` file. For this system to work automatically in the background, you will need to schedule it to run daily (e.g., at 1:00 AM) alongside your other cron jobs.
   
2. **Reward Value Hardcoding:**
   When the threshold is reached, the system strictly rewards `1.00` leave balance. 
   *Code: `$this->leaveService->creditLeave(..., 1.00, ...);`*
   If you ever intend to reward 1.5 or 2 leaves per cycle, this specific line in `DailyLeaveAccrual.php` will need to be updated to pull dynamically from the database.

3. **Counting Approved Leaves:**
   If you want employees to still accrue leaves while they are on approved paid vacations, you will need to uncomment the `LeaveRequest` check inside the `DailyLeaveAccrual.php` command. Currently, they only earn points if they actively submitted a Worklog.
