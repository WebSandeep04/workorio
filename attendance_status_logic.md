# Attendance Status Logic Report

This document details the exact conditions and logic used to determine an employee's attendance status across both the web attendance report and the nightly attendance email.

> [!NOTE]
> The **Base Status** is calculated centrally in `AttendanceReportService.php` and used in the web reports. The **Night Mail** (`SendNightAttendanceMail.php`) takes this base status and applies additional penalties (like grace period exhaustion and missing punch-outs).

### Key Variables Explained:
*   `hours`: Total hours worked (Time between first IN and last OUT, bounded by shift).
*   `fullDayHr` / `halfDayHr`: The required hours for a full/half day, based on shift settings.
*   `SL hours`: Granted duration of a Short Leave.
*   `effective minutes`: Actual worked minutes + SL duration (if any) + Half Day duration (if any).
*   `Monthly grace exhausted`: When the employee's accumulated late minutes exceed their allowed monthly limit (`min_per_month_late_allow`).

## Status Logic Table

| Base Code | Base Label | Night Mail Final Status | Night Mail Reason | Condition Logic |
| :--- | :--- | :--- | :--- | :--- |
| **W/O-W** | `[Day] Working` | `[Day] Working` | `-` | Date is a marked Weekly Off AND `hours > 0` |
| **W/O** | `[day]` | `[Day]` | `-` | Date is a marked Weekly Off AND `hours == 0` |
| **H/W** | `holiday working` | `Holiday Working`| `-` | Date is a marked Holiday AND `hours > 0` |
| **H** | `holiday` | `Holiday` | `-` | Date is a marked Holiday AND `hours == 0` |
| **A** | `absent by less hr` | `absent` | `Worked less than [X] hrs` | **(NEW)** Leave Type is `SL` AND worked `hours <= halfDayHr`. Completely denies the SL. |
| **P** | `present` | `present` | `-` | **(NEW)** Worked `hours >= fullDayHr`. Overrides any partial/half leaves and marks as pure present. |
| **P (SL)** | `present with SL` | `present with SL` | `Present with SL` | `effective minutes >= fullDayHr` AND `SL hours > 0`. SL stacking with HD is prevented. |
| **P** | `present` | **`halfday`** (Override) | `Monthly grace exhausted` | `effective minutes >= fullDayHr` AND Employee was Late AND the accumulated late minutes exceed the monthly grace allowance. |
| **P** | `present` | `present` | `Covered under grace` | `effective minutes >= fullDayHr` AND Employee was Late, but still within the monthly grace allowance. |
| **P** | `present` | `present` | `-` | `effective minutes >= fullDayHr` AND Employee was not Late. |
| **P2** | `halfday` | `halfday` | `Approved Half Day` | Has Half Day Leave AND didn't reach full day effectively. |
| **P2** | `halfday` | `halfday` | `Worked less than [X] hrs` | `hours >= halfDayHr` but `< fullDayHr` AND no grace exhaustion applied. (Base halfday calculation) |
| **P2** | `halfday` | `halfday` | `Monthly grace exhausted` | Base status is halfday, but they were also late and exceeded the monthly grace limit. |
| **L** | `leave` | `On Leave` | `On Leave` | Leave type is `L` (Full day leave) |
| **RH** | `restricted holiday` | `Restricted Holiday` | `-` | Leave type is `RH` |
| **A** | `absent by less hr` | `absent` | `Worked less than [X] hrs` | `hours > 0` but `hours < halfDayHr` AND No leaves apply. |
| **A** | `absent` | `absent` | `No attendance recorded` | `hours == 0` AND No leaves apply. |
| **A** (Override) | `absent by less hr` | **`absent`** | `punchout is missing` | Night Mail explicitly checks if `punch_out === 'Not Marked'`. If true, it completely overrides the base status to absent regardless of hours. |
