# Attendance, Worklog, and Leave Management System Documentation

This document provides a comprehensive technical and functional overview of the enterprise-level management system. It is designed to serve as the definitive reference for business logic, calculations, and system constraints.

---

## 1. System Overview & Data Relationships

The system is built on a multi-tenant architecture where each tenant manages its own organizational structure.

### 1.1 Organizational Hierarchy
| Entity | Description | Relationship |
| :--- | :--- | :--- |
| **Company (Tenant)** | The top-level organization. | Contains all users, projects, and shifts. |
| **Customer** | Clients of the company. | Linked to Projects. |
| **Project** | Specific work initiatives for a Customer. | Linked to Modules and CustomerProjects. |
| **Module** | Functional sub-units of a Project. | Linked to Worklogs for granular tracking. |

### 1.2 User & Operational Data
*   **Users & Employees**: Every user is an `Employee` with an associated `EmploymentType` and `Shift`.
*   **Worklogs**: Individual task entries linked to `User`, `Customer`, `Project`, and `Module`.
*   **Attendance**: Daily records derived from `Movements` (Punch-In/Out).
*   **Leave**: Requests that modify attendance status and worklog requirements.
*   **Approvals/Reviews**: Intermediary states for Worklogs and Penalty Reasons.

---

## 2. Shift Configuration

Shifts define the "Working Window" for employees. All attendance calculations are relative to the assigned shift.

### 2.1 Shift Parameters
| Parameter | Description | Logic / Enforcement |
| :--- | :--- | :--- |
| **Start Time** | Official work start time. | Used for Late Punch calculation and Start Capping. |
| **End Time** | Official work end time. | Used for End Capping. |
| **Late Allowed (Grace)** | Minutes allowed after Start Time. | Penalty is calculated as: `(Punch In - Start Time) - Grace`. |
| **Full Day Hr** | Minimum hours for "Present" status. | If `Logged Hours >= Full Day Hr`, status = **Present**. |
| **Half Day Hr** | Minimum hours for "Half Day" status. | If `Full Day Hr > Logged Hours >= Half Day Hr`, status = **Half Day**. |
| **Extended Hr** | Maximum hours allowed beyond End Time. | Capping = `End Time + Extended Hr`. |
| **Week Offs** | Array of non-working days (e.g., Sunday). | Employees are exempt from Worklogs on these days unless they punch in. |

---

## 3. Attendance Calculation Logic

Attendance is not just a punch record; it is a calculated state based on movements and shift policy.

### 3.1 Work Hour Calculation (The Capping Formula)
To ensure compliance and prevent excessive overtime tracking, work hours are capped:
1.  **Raw Start**: The first `office` or `field` "In" movement of the day.
2.  **Raw End**: The last `office` or `field` "Out" movement of the day.
3.  **Policy Start**: `MAX(Raw Start, Shift Start)`.
4.  **Policy End**: `MIN(Raw End, Shift End + Extended Hours)`.
5.  **Total Hours**: `Policy End - Policy Start - Total Break Time`.

### 3.2 Attendance Status Matrix
| Condition | Derived Status | Summary Metric |
| :--- | :--- | :--- |
| Hours >= Shift Full Day Hr | **Present** | Counted as 1 day worked. |
| Shift Full Day Hr > Hours >= Shift Half Day Hr | **Half Day** | Counted as 0.5 days worked. |
| Hours < Shift Half Day Hr | **Absent** | Counted as 0 days worked. |
| No Punch + Weekly Off | **Weekly Off** | Exempt. |
| No Punch + Holiday Master Match | **Holiday** | Exempt. |
| No Punch + Approved Leave | **Leave** | Status based on Leave Type (SL/RH/L). |
| Punch exists on Weekly Off/Holiday | **W.O Working / Holiday Working** | Hours calculated with Shift rules. |

---

## 4. Worklog Management System

Worklogs ensure that time spent matches time punched.

### 4.1 Submission Constraints
*   **Chronological Order**: Users **MUST** fill worklogs in order. If `Monday` is missing, the system blocks `Tuesday` entries.
*   **Punch-Out Dependency**: For the current day, a user cannot submit a worklog until they have performed a **Punch Out** or **Field Out**.
*   **Total Time Validation**: The sum of all task durations for a day must be >= the `Working Hours` defined for the selected `Entry Type`.

### 4.2 Gap & Missing Date Logic
A date is considered "Missing" if:
1.  There is an **Attendance Record** for that date.
2.  There is **No Worklog** submitted.
3.  The date is in the past.
*Note: Approved Leave or Holidays without attendance do NOT require worklogs.*

---

## 5. Leave Management

### 5.1 Leave Types & Status
*   **Full Day Leave**: Marks the day as `L`. No worklog required.
*   **Half Day Leave**: Marks the day as `P2`.
*   **Short Leave (SL)**: Used for late arrivals or early departures (usually 1-2 hours).
*   **Restricted Holiday (RH)**: Optional holidays that require approval.

### 5.2 Conflict Resolution
*   **Leave + Worklog**: If an employee has a Half Day Leave but works 6 hours, the system prioritizes the Work hours for the status (Present) but keeps the Leave record for audit.
*   **Leave + Punch**: If an employee punches in on a day they have a Full Day Leave, the status becomes "Leave Working".

---

## 6. Approval & Review Workflow

### 6.1 Worklog Approvals
1.  **Pending**: Default state on submission.
2.  **Review (Manager)**: Manager reviews descriptions and hours.
3.  **Outcome**:
    *   **Approved**: Entry is locked.
    *   **Rejected**: Entry returns to the user with a `Reject Reason`. User must edit and resubmit.

### 6.2 Late Reason Reviews
*   When an employee punches in late (beyond grace), they must provide a **Late Reason**.
*   Managers review these reasons. If rejected, the late minutes may be deducted from the monthly late allowance or salary.

---

## 7. Reporting Logic

### 7.1 Daily Report
*   **Filters**: Branch, Department, Date.
*   **Display**: First In, Last Out, Total Hours, Status (calculated via Section 3).

### 7.2 Monthly Grid Report
*   **Structure**: Users as rows, days of the month as columns.
*   **Color Coding**: 
    *   `Green (P)`: Present.
    *   `Yellow (P2)`: Half Day.
    *   `Red (-)`: Absent.
    *   `Blue (S/H)`: Weekly Off / Holiday.

### 7.3 CSV Export
*   Exports all calculated fields (Total Working Days, Total Present, Total Half Days, Late Count, Avg Hours/Day).

---

## 8. Edge Cases & Special Scenarios

| Edge Case | System Treatment |
| :--- | :--- |
| **Missing Logout** | The system uses `Carbon::now()` or `Shift End` as the default logout time for calculation purposes but flags the entry for admin review. |
| **Zero Hour Attendance** | If a user punches IN and OUT within 5 minutes, status is **Absent**. |
| **Punch on Sunday** | Sunday becomes a working day; hours are calculated; status is `S/W`. |
| **Late Punch (Grace Period)** | If Shift starts at 10:00 AM with 15m grace and user punches at 10:16 AM, `Late Minutes = 1`. If they punch at 10:15 AM, `Late Minutes = 0`. |
| **Multiple Punches** | Only the **Absolute First IN** and **Absolute Last OUT** define the work duration boundary. |
