# Comprehensive Attendance Status Logic

This document details the exact conditions, variables, and scenario breakdowns used to determine an employee's attendance status. It covers both the **Web Attendance Report** (which calculates the Base Status) and the **Nightly Attendance Email** (which applies final override penalties).

> [!NOTE]
> The **Base Status** is calculated centrally in `AttendanceReportService.php` and locally inside `AttendanceController` (for specific reports like date-wise). The **Night Mail** (`SendNightAttendanceMail.php`) evaluates this base status and applies strict penalties, such as downgrading a status when grace periods are exhausted or when an employee forgets to punch out.

## Key Variables Explained
*   **`Actual Time` (`hours`)**: Total hours physically worked (Time between first IN and last OUT, bounded by shift rules).
*   **`Full Day Hr` / `Half Day Hr`**: The required hours for a full/half day, derived from the employee's specific shift configuration.
*   **`SL Duration`**: The allowed duration for a Short Leave, pulled dynamically from the shift's `sl_end_limit`.
*   **`Effective Time`**: `Actual Time` + `SL Duration` (if applicable) + `Half Day Hr` (if they have an approved Half Day Leave).
*   **`Monthly Grace Exhausted`**: When the employee's accumulated late minutes exceed their allowed monthly limit (`min_per_month_late_allow`).

---

## Short Leave (SL) Scenario Breakdown

Short Leave (SL) is strictly used to help an employee reach a **Full Day** status. If it fails to do that, the SL is discarded and the system evaluates them purely on their physical working hours.

1. **`Actual Time` + `SL` >= `Full Day Hr`**
   * **Result**: `P (SL)` (Present with SL)
   * *Explanation*: The SL successfully bridged the gap to a full working day. (If they physically worked a full day anyway, the system ignores the SL and grants a pure `P`).

2. **`Actual Time` + `SL` >= `Half Day Hr` (but `< Full Day Hr`)**
   * **Result**: `P2` (Halfday) OR `A` (Absent)
   * *Explanation*: The SL failed to bridge the gap to a full day, so the SL tag is dropped. The system falls back to strict actual hours:
      * If `Actual Time` >= `Half Day Hr` ➔ `P2` (Halfday)
      * If `Actual Time` < `Half Day Hr` ➔ `A` (Absent) - *The employee is heavily penalized for not physically working at least a half-day.*

3. **`Actual Time` + `SL` < `Half Day Hr`**
   * **Result**: `A` (Absent)
   * *Explanation*: Even with the SL, they failed to reach a half day. This inherently means `Actual Time` is less than a half day, triggering an immediate `A`.

---

## Status Logic Table

| Base Code | Base Label | Night Mail Final Status | Night Mail Reason | Condition Logic |
| :--- | :--- | :--- | :--- | :--- |
| **W/O-W** | `[Day] Working` | `[Day] Working` | `-` | Date is a marked Weekly Off AND `Actual Time > 0` |
| **W/O** | `[day]` | `[Day]` | `-` | Date is a marked Weekly Off AND `Actual Time == 0` |
| **H/W** | `holiday working` | `Holiday Working`| `-` | Date is a marked Holiday AND `Actual Time > 0` |
| **H** | `holiday` | `Holiday` | `-` | Date is a marked Holiday AND `Actual Time == 0` |
| **A** | `absent by less hr` | `absent` | `Worked less than [X] hrs` | Leave Type is `SL` AND `Actual Time < Half Day Hr`. (SL is completely denied if they don't physically work a half-day). |
| **P** | `present` | `present` | `-` | `Actual Time >= Full Day Hr`. (Overrides any partial/half leaves and marks as pure present). |
| **P (SL)** | `present with SL` | `present with SL` | `Present with SL` | `Effective Time >= Full Day Hr` AND `SL Duration > 0`. (SL stacking with HD leave is prevented). |
| **P** | `present` | **`halfday`** (Override) | `Monthly grace exhausted` | `Effective Time >= Full Day Hr` AND Employee was Late AND the accumulated late minutes exceed the monthly grace allowance. |
| **P** | `present` | `present` | `Covered under grace` | `Effective Time >= Full Day Hr` AND Employee was Late, but still within the monthly grace allowance. |
| **P** | `present` | `present` | `-` | `Effective Time >= Full Day Hr` AND Employee was not Late. |
| **P2** | `halfday` | `halfday` | `Approved Half Day` | Has Half Day Leave AND didn't reach full day effectively. |
| **P2** | `halfday` | `halfday` | `Worked less than [X] hrs` | `Actual Time >= Half Day Hr` but `< Full Day Hr` AND no grace exhaustion applied. (Base halfday calculation). |
| **P2** | `halfday` | `halfday` | `Monthly grace exhausted` | Base status is halfday, but they were also late and exceeded the monthly grace limit. |
| **L** | `leave` | `On Leave` | `On Leave` | Leave type is `L` (Full day leave) |
| **LWP** | `unpaid leave` | `LWP` | `Unpaid Leave` | Leave type is `LWP` or leave is an unpaid leave request. Applies to missing attendance records with unpaid leave. |
| **RH** | `restricted holiday` | `Restricted Holiday` | `-` | Leave type is `RH` |
| **A** | `absent by less hr` | `absent` | `Worked less than [X] hrs` | `Actual Time > 0` but `Actual Time < Half Day Hr` AND No leaves apply. |
| **A** | `absent` | `absent` | `No attendance recorded` | `Actual Time == 0` AND No leaves apply. |
| **A** (Override) | `absent by less hr` | **`absent`** | `punchout is missing` | Night Mail explicitly checks if `punch_out === 'Not Marked'`. If true, it completely overrides the base status to absent regardless of hours. |
