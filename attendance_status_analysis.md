# Deep Analysis of Attendance Statuses & Scenarios

The exact attendance status (e.g., Present, Absent, Halfday) is definitively computed during the **punch-out** action via the `AttendanceReportService`. The system calculates total effective hours based on the *first* IN movement and the *last* OUT movement, bounded strictly by the employee's shift timings.

Below is an exhaustive breakdown of every possible status, the scenarios that trigger them, and the specific rules applied in the backend logic.

---

## 1. Statuses Based on Working Hours

### **Present (`P`)**
- **Scenario 1 (Normal Shift)**: The employee's total worked hours are equal to or greater than the required Full Day Shift hours.
- **Scenario 2 (With Short Leave)**: The employee took an approved Short Leave (SL). Their actual worked hours plus the Short Leave allowance hours are >= the Full Day hours requirement. The system effectively grants them the remaining hours needed for a full day. (Label internally tracks as `present with SL`).
- **Scenario 3 (Overachieving on Half Day Leave)**: The employee was on an approved Half Day leave, but ended up working equal to or more than the required Full Day hours anyway. The system awards a Present status but sets the reason to *Worked full day while on Half Day Leave*.

### **Halfday (`P2`)**
- **Scenario 1 (Insufficient Full Day Hours)**: The employee's worked hours are >= the Half Day Shift requirement, but strictly less than the Full Day Shift requirement.
- **Scenario 2 (With Half Day Leave)**: The employee took an approved Half Day leave and successfully worked >= (Full Day Hours / 2). They receive the `P2` status to complete the day.
- **Scenario 3 (WFH Downgrade Policy)**: The employee worked the required Full Day hours, but their attendance is marked as Work From Home (`is_wfh = true`). The system enforces a policy that automatically downgrades a Present to a Halfday. The reason provided is *WFH Policy (Treated as Half Day)*.

### **Absent (`A`)**
- **Scenario 1 (No Attendance / Zero Hours)**: The employee did not punch in at all, logged 0 hours, and has no approved leave for the day. Reason: *No attendance recorded*.
- **Scenario 2 (Insufficient Hours)**: The employee worked some hours, but the total was strictly less than the required Half Day hours. Reason: *Worked less than X hrs*.
- **Scenario 3 (Missing Punch-out / Ghost Shift)**: The employee punched in but never punched out. Even if they mathematically accrued enough hours by the end of the day, the lack of a final `out` movement strictly forces an Absent status. Reason: *punchout is missing*.
- **Scenario 4 (Short Leave Failure)**: The employee took a Short Leave, but even with the SL allowance, their actual worked hours fell below the required Half Day threshold.
- **Scenario 5 (Half Day Leave Failure)**: The employee took a Half Day leave but failed to work the minimum required half-day quota (Full Day Hours / 2). 

---

## 2. Statuses on Special Days (Weekends & Holidays)

### **Weekly Off Working (`W/O-W`)**
- **Scenario**: The employee punched in and worked on their designated Sunday/Weekly Off.
  - *Note on Time Restrictions*: If the shift setting `enforce_time_restriction_on_overtime` is enabled, the employee MUST log at least Half Day hours to earn this status. If it's disabled, any amount of logged time (> 0 hours) earns this status.
  - If `is_wfh` is true, the internal code tracks this as `W/O-W_wfh`.

### **Holiday Working (`H/W`)**
- **Scenario**: The employee punched in and worked on a designated company Holiday.
  - The same time restriction rules apply here as they do for Weekly Off Working.
  - If `is_wfh` is true, the internal code tracks this as `H/W_wfh`.

### **Weekly Off (`W/O` or `S`)**
- **Scenario**: A standard Sunday/Weekly off where the employee logged 0 hours.

### **Holiday (`H`)**
- **Scenario**: A standard company Holiday where the employee logged 0 hours.

---

## 3. Statuses Driven by Approved Leaves

These statuses override standard working rules if the employee logs 0 hours on that day:

- **Leave (`L`)**: The employee has an approved standard full-day leave.
- **Short Leave (`SL`)**: The employee has an approved Short Leave (only falls back to this if they didn't work at all).
- **Half Day (`HD`)**: The employee has an approved Half Day Leave (only falls back to this if they didn't work at all).
- **Restricted Holiday (`RH`)**: The employee took an approved Restricted Holiday.
- **Unpaid Leave (`LWP`)**: The employee is on approved Leave Without Pay.

---

## 4. Notable Status Modifiers & Reasons (`status_reason`)

The `status_reason` column in the database provides human-readable context for why a specific status was assigned:

- **"Late by X mins"**: Always appended if the employee's first punch-in was past the shift start time + allowed grace period.
- **"Worked full day while on Half Day Leave"**: An explicit flag showing the employee overachieved on a day they were granted half-time off.
- **"Worked less than X hrs"**: Explains why an employee received a Halfday or Absent status despite logging some time.
- **"punchout is missing"**: Explicitly penalizes the employee with an Absent status because they forgot to punch out.
- **"No attendance recorded"**: The default reason for an Absent status with 0 hours and no leave.
- **"WFH Policy (Treated as Half Day)"**: Hardcoded backend policy that penalizes all Work From Home attendances by capping them at Halfday regardless of the actual hours logged.
