# New Leave Accrual System Design

This document outlines the architecture and workflow for the updated Leave Accrual system based on your new business rules.

## Core Principles
The new system shifts the source of truth from manual `Worklog` entries to the automated `Attendance` statuses. Employees will earn leave credits by maintaining good attendance (anything other than being marked "Absent").

### The New Requirements:
1. **Source of Truth:** Count days based on the `Attendance` system (and its calculated statuses), completely ignoring the `Worklog` table.
2. **Eligibility Criteria:** Include *everything* (Present, Halfday, Paid Leave, Holidays, Weekly Offs) **except** when the status is strictly `"Absent"`.
3. **Accrual Trigger:** If the generation type is `accrual` and the user hits their configured `valid_days_count` (e.g., 45 days), credit them exactly 1.00 leave.

---

## Step-by-Step Workflow

When the `leave:daily-accruals` command runs (typically daily at 1:00 AM via cron job), it will execute the following steps:

### Step 1: Target Date Selection
The script focuses on evaluating attendance for **Yesterday** (e.g., `Carbon::yesterday()->format('Y-m-d')`).

### Step 2: Fetch Active Users & Shift Data
The system retrieves all active users who have an assigned `employment_type_id`. For each user, it fetches their shift configuration (which is needed to accurately determine attendance status).

### Step 3: Evaluate Attendance Status (The New Logic)
Instead of checking for worklogs, the script will utilize your existing `AttendanceReportService` (specifically methods like `_fetchDateReportData` or `determineStatusAndReason`) to calculate the exact final status of the user for "Yesterday".

The system checks the resulting status:
* **VALID DAYS:** `Present`, `Halfday`, `Holiday`, `Weekly Off`, `Leave`, `Short Leave`, `Restricted Holiday`
* **INVALID DAYS:** `Absent`

If the status evaluates to **Absent**, the user is skipped for this day, and their counter does not increase.

### Step 4: Validate Accrual Rules
If the user's status was **not** Absent, the system fetches the `EmploymentTypeLeaveRule` table for their specific employment type, filtering only for rules where `generation_type` is `'accrual'`.

*(Example: The rule requires 45 Valid Days to earn 1 Leave).*

### Step 5: Update the Accrual Counter
For each valid accrual rule:
1. The system fetches the user's current tracker in the `LeaveAccrualCounter` table.
2. It increments the `valid_days_count` by **1**.

### Step 6: Threshold Check & Reward
The system checks if the new `valid_days_count` matches or exceeds the required threshold (e.g., `45` days).

If the threshold is hit:
1. **Reward:** It triggers the `LeaveBalanceService->creditLeave()` function, rewarding the user with **+1.00** leave balance of that specific type.
2. **Reset:** It resets the user's `valid_days_count` in the `LeaveAccrualCounter` back to **0**.
3. **Log:** It writes a success message to the Laravel log for audit purposes.

---

## Technical Implementation Notes (For Development)

To build this, the following files will need to be updated:

1. **`app\Console\Commands\DailyLeaveAccrual.php`**
   * Remove the `Worklog` check.
   * Inject `AttendanceReportService`.
   * For each user, run the attendance calculation for `$targetDate`.
   * Check if the final status string contains `'absent'`. If it does NOT, increment the counter.
   
2. **`routes/console.php`** (Optional but Recommended)
   * Schedule the `leave:daily-accruals` command to run daily at a time after all attendances for the previous day have been finalized (e.g., 01:00 AM).
