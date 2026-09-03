# Attendance Report Logic & Scenario Documentation

The attendance reporting system has been unified into a **Single Source of Truth** using `AttendanceReportService`. It strictly depends on the `computed_status` column in the database (which is set when employees punch out or when the system cron runs), ensuring reports load fast and consistently without redundant real-time math.

---

## 1. Core Logic Overview

### A. How the Database is Updated (`computeAndSaveDailyStatus`)
Whenever an action occurs (a user punches out, or the daily cron job runs), the `AttendanceReportService` calculates the total hours worked and compares it against shift thresholds (taking grace into account). 
The final result (e.g. `Present`, `Halfday`, `Absent`) is permanently saved to the `computed_status` column in the `attendances` table. 

> [!IMPORTANT]
> The reports **do not recalculate this status**. They simply read the `computed_status` column. If you change a shift policy today, past report statuses remain unchanged unless you explicitly recalculate the past days via a background command.

### B. How the Reports Read the Data (`generateDailyBreakdown`)
All reports (Monthly, User-wise, Date-wise) across both the Web Application and the API hit a single function: `generateDailyBreakdown`.

This function takes an array of attendances, loops through the required date range, and determines what to display for each day using the following strict priority logic:

1. **Has Attendance in Database?**
   - If yes, it reads `computed_status` (e.g., "present", "halfday", "absent", "weekly off working", "holiday working").
   - It maps these text statuses to short UI codes (`P`, `P2`, `A`, `W/O-W`, `H/W`).
   - *WFH check*: If the attendance row has `is_wfh = 1`, it appends a `wfh` subscript (e.g. `P` becomes `P2` (halfday) if strictly instructed, or `H/W` becomes `H/W (wfh)`).

2. **Is it a Holiday?**
   - If no attendance row exists, but the date is listed in the `holidays` table, it assigns code `H` (Holiday).

3. **Is it a Weekly Off (Sunday/Custom)?**
   - If no attendance, and not a holiday, it checks the user's active shift rules.
   - If the day matches the shift's `week_offs` array (e.g., Sunday), it assigns code `S` (Weekly Off).

4. **Is the User on an Approved Leave?**
   - If none of the above apply, but the user has an approved `LeaveRequest` spanning this date, it assigns a specific leave code:
     - `RH`: Restricted Holiday
     - `SL`: Short Leave
     - `LWP`: Leave Without Pay
     - `HD`: Half Day
     - `L`: Standard Paid Leave

5. **Fallback to NA (Not Available)**
   - If the date has no attendance, is not a holiday, is not a weekly off, and the user has no approved leave, the system assigns **`NA`** (`text-secondary`).
   - *Why `NA` and not `Absent`?* If a user forgets to punch in, or a new month starts and days are empty, the system waits for the cron job to officially mark them as `Absent` and insert a row into the database. Until then, empty days are strictly `NA`.

---

## 2. Summary Calculation (`calculateMonthlySummary`)

The top tiles on the report views (e.g., "Total Working Days", "Total Present") are generated using `calculateMonthlySummary`. 
This function now piggy-backs on the exact same `generateDailyBreakdown` logic. It iterates through the exact mapped data and counts the occurrences of each code:

- **Working Days**: Every day that is NOT a holiday and NOT a weekly off.
- **Present**: Count of `P` and `P2` (Halfday).
- **Absent**: Count of `A`. *(Note: `NA` days are NOT counted as absent here. Only official `A` codes are counted).*
- **On Leave**: Count of `L`, `RH`, `HD`.
- **Unpaid Leaves**: Count of `LWP`.

---

## 3. Scenarios & Expected Outcomes

### Scenario 1: A user works a normal day and punches out.
1. System calculates they worked 9 hours.
2. `computed_status` is saved as `present`.
3. The report displays **`P`**.

### Scenario 2: A user is completely missing on a Tuesday, and the cron job has NOT run yet.
1. No row exists in `attendances` for Tuesday.
2. Not a holiday, not a weekly off, no leave.
3. The report displays **`NA`**.

### Scenario 3: A user is missing on a Tuesday, and the cron job HAS run.
1. The cron job forces a calculation, sees 0 hours, and creates an `attendances` row with `computed_status = 'absent'`.
2. The report sees the row and displays **`A`**.

### Scenario 4: A user works on a Sunday (Weekly Off).
1. They punch in and out.
2. System detects it's a weekly off but sees they worked. `computed_status` is saved as `weekly off working`.
3. The report displays **`W/O-W`**.

### Scenario 5: Future dates in the current month.
1. Since future dates have no attendance and the cron hasn't run, they fall through to the default logic.
2. If it's a Sunday, they show **`S`**.
3. If it's a regular Wednesday, they show **`NA`**.

---

## 4. Where is the Code Located?

- **The Brain**: `app/Services/AttendanceReportService.php`
  - `generateDailyBreakdown()` -> The mapper.
  - `calculateMonthlySummary()` -> The counter.
- **The Delivery**: 
  - `app/Http/Controllers/AttendanceController.php` (Web Views)
  - `app/Http/Controllers/Api/AttendanceReportApiController.php` (Mobile API)
  - Both controllers do nothing but query the basic raw data (Attendance, Leaves, Holidays, Users) and pass it to the Service to do the mapping.
