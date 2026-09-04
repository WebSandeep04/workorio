# Deep Analysis of Attendance Statuses & Scenarios

The exact attendance status (e.g., Present, Absent, Halfday) is computed definitively during the **punch-out** action via the `AttendanceReportService`. The system calculates total effective hours based on the *first* IN movement and the *last* OUT movement, bounded strictly by the employee's shift timings.

Below is an exhaustive breakdown of every possible status, the scenarios that trigger them, and the specific rules applied in the backend logic.

---

## 1. Effective Hours and Time Thresholds
The system uses the concept of **Effective Minutes** to determine statuses involving partial leaves.
- `effectiveInMinutes = hoursInMinutes + shortLeaveInMinutes + (hasHalfDayLeave ? halfDayInMinutes : 0)`
- **SL Stacking Prevention**: If an employee takes a Half Day leave, their Short Leave allowance is forced to 0. If they applied for SL, it is internally treated as a Half Day leave.
- **Half Day Working (Shift Policy)**: If the shift dictates the day is a "Half Day Working" day, the `fullDayHr` requirement becomes the standard `halfDayHr`, and the new `halfDayHr` requirement becomes half of that.

---

## 2. Statuses Based on Working Hours

### **Present (`P`)**
- **Scenario 1 (Normal Shift)**: The employee's actual worked hours are `>=` the Full Day Shift hours. Takes precedence over all leaves.
- **Scenario 2 (With Short Leave)**: The employee took an approved Short Leave (SL). Their `effectiveInMinutes` (actual worked + SL allowance) is `>=` the Full Day hours requirement. Internal label: `present with SL`, code `P (SL)`.
- **Scenario 3 (Overachieving on Half Day Leave)**: The employee was on an approved Half Day leave, but actually worked `>=` Full Day hours. They are marked standard Present (`P`), and any overlaps or early returns are expected to be handled manually via the Attendance Approval system.

### **Halfday (`P2`)**
- **Scenario 1 (Standard Halfday)**: The employee's actual worked hours are `>=` the Half Day Shift requirement, but strictly less than the Full Day Shift requirement.
- **Scenario 2 (With Half Day Leave)**: The employee took an approved Half Day leave and successfully worked the required minimum minutes configured in their Shift (`halfday_leave_req_min`, defaults to 270 mins / 4:30 hr).
- **Scenario 3 (WFH Downgrade Policy)**: The employee actually earned a "Present" status, but their attendance is marked as Work From Home (`is_wfh = true`). The system forcefully downgrades it to a Halfday. Reason: *WFH Policy (Treated as Half Day)*.

### **Absent (`A`)**
- **Scenario 1 (No Attendance / Zero Hours)**: The employee did not punch in at all (`hours <= 0`) and has no approved leave. Reason: *No attendance recorded*.
- **Scenario 2 (Insufficient Hours)**: The employee worked some hours, but the total was strictly less than the required Half Day hours. Reason: *Worked less than X hrs*.
- **Scenario 3 (Missing Punch-out / Ghost Shift)**: The employee logged some hours but does not have a final `out` movement for the day. Status forcefully becomes `absent`. Reason: *punchout is missing*.
- **Scenario 4 (Short Leave Failure)**: The employee took a Short Leave, but their **actual** worked hours (without SL allowance) fell below the required Half Day threshold. They are marked Absent. Label: *absent by less hr*.
- **Scenario 5 (Half Day Leave Failure)**: The employee took a Half Day leave but failed to work their shift's required `halfday_leave_req_min` (defaults to 270 mins / 4:30 hr). Label: *absent by less hr*.

---

## 3. Statuses on Special Days (Weekends & Holidays)

### **Weekly Off Working (`W/O-W`)**
- **Scenario**: The employee worked on a designated Weekly Off.
  - *Time Restriction Policy*: If `enforce_time_restriction_on_overtime` is enabled in their shift, their `effectiveInMinutes` MUST be `>=` Half Day hours to earn this status.
  - If disabled, ANY worked time (`hours > 0`) earns this status.
  - WFH downgrade logic does NOT apply to W/O-W (unless the label contains 'present').

### **Holiday Working (`H/W`)**
- **Scenario**: The employee worked on a designated company Holiday.
  - Identical Time Restriction Policy as Weekly Off Working.

### **Weekly Off (`W/O`)**
- **Scenario**: A standard Weekly off where the employee logged 0 hours (or failed the time restriction policy).

### **Holiday (`H`)**
- **Scenario**: A standard Holiday where the employee logged 0 hours (or failed the time restriction policy).

---

## 4. Statuses Driven by Approved Leaves

These fallback statuses apply if the employee logs 0 hours and does not meet any working conditions:

- **Leave (`L`)**: Approved standard full-day leave.
- **Restricted Holiday (`RH`)**: Approved Restricted Holiday.
- **Unpaid Leave (`LWP`)**: Approved Leave Without Pay.

---

## 5. Notable Status Modifiers & Reasons (`status_reason`)

The `status_reason` column in the database provides human-readable context. Rules are applied in this priority:

1. **"WFH Policy (Treated as Half Day)"**: Forcefully overwrites any reason if a Present status is downgraded due to WFH.
2. **"punchout is missing"**: Forcefully overwrites reason if a working shift is missing a final punchout.
3. **"Late by X mins"**: If the employee was late (`late_minutes > 0`), this becomes the base reason.
4. **"Worked less than X hrs"**: Applied to Absents and Halfdays (when not overriden by WFH or Late reasons).
5. **"No attendance recorded"**: Pure zero-hour absent days.
