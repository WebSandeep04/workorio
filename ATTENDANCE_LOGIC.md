# Attendance System Logic & Flow

This document outlines the logic behind the attendance system, including movements, cycles, validations, and restrictions.

## 1. Core Concepts

### Movements
The system tracks **Movements** rather than simple "In/Out" timestamps.
- **Types:** `office`, `field`, `break`
- **Actions:**
  - `in` / `out` (for Office and Field)
  - `start` / `end` (for Break)

### Cycles
- A **Cycle** is a completed pair of actions (e.g., `in` -> `out`).
- Users can have multiple cycles per day (e.g., Punch In -> Punch Out -> Punch In -> Punch Out).
- The system calculates daily hours by summing up the duration of all completed cycles.

### Daily Attendance Record
- All movements for a specific user on a specific date are grouped under a single `Attendance` record.

---

## 2. Pre-Requisites (Worklog Validation)

Before **ANY** attendance action (Punch In, Out, or Break) is allowed, the system performs a strict validation:

**Rule:** Users must have completed their **Worklogs** or applied for **Leave** for all previous working days.
- **Scope:** Checks chronologically from the user's account creation date up to yesterday.
- **Exclusions:** Sundays and Holidays are skipped.
- **Consequence:** If a past worklog is missing, **all attendance actions are blocked** with an error message instructing the user to complete the missing entry first.

---

## 3. Punch In Flow (Office / Field)

When a user attempts to **Punch In** for Office or Field:

1.  **Break Check:**
    - Checks if the user is currently on an active Break.
    - **Restriction:** Cannot punch in if a break is active. Must "End Break" first.

2.  **Late Attendance Check (First Punch of the Day):**
    - This logic applies **only** to the very first Office or Field punch-in of the day.
    - It compares the current time against: `Shift Start Time` + `Allowed Late Minutes`.
    - **Condition:** If `Current Time > (Shift Start + Late Min)`, the user is marked as **Late**.
    - **Requirement:** Late users **must** provide a valid `late_reason`. The system will prompt for this check before creating the punch.

3.  **Auto-Switching Logic:**
    - **Office Punch:** If currently marked as "Field In", the system automatically creates a "Field Out" movement before punching into Office.
    - **Field Punch:** If currently marked as "Office In", the system automatically creates an "Office Out" movement before punching into Field.

4.  **Creation:**
    - Creates a new movement with action `in`.

---

## 4. Punch Out Flow (Office / Field)

When a user attempts to **Punch Out**:

1.  **Task Validation (BLOCKER):**
    - The system checks for **Pending Tasks** assigned to the user.
    - **Criteria:**
        - Task Status is NOT completed.
        - Task Due Date is today or in the past.
        - Task `updated_at` timestamp is **before today** (i.e., not updated today).
    - **Restriction:** If such tasks exist, the Punch Out is **BLOCKED**.
    - **Message:** "You have X pending task(s) that were not updated today..."
    - **Resolution:** User must update the status or add remarks to these tasks to proceed.

2.  **Break Check:**
    - Cannot punch out if currently on a break.

3.  **Creation:**
    - Creates a new movement with action `out`.

---

## 5. Break System

### Start Break
- **Action:** Creates a movement with type `break` and action `start`.
- **Restriction:** Cannot start a break if one is already active.
- **Effect:** Locks Office and Field punch actions until ended.

### End Break
- **Action:** Creates a movement with type `break` and action `end`.
- **Effect:** Unlocks Office and Field punch actions.

---

## 6. Summary of Restrictions

| Action | Restriction | Reason |
| :--- | :--- | :--- |
| **Any Action** | **Worklog Pending** | Previous days' worklogs must be completed. |
| **Punch In** | **Active Break** | Must end break first. |
| **Punch In** | **Late (First Punch)** | Must provide a reason if past grace period. |
| **Punch Out** | **Active Break** | Must end break first. |
| **Punch Out** | **Stale Pending Tasks** | Tasks due today/older must be updated today. |
| **Start Break** | **Active Break** | Cannot stack breaks. |

---

## 7. Code Reference
- **Controller:** `App\Http\Controllers\AttendanceController`
- **Models:** `Attendance`, `Movement`, `Task`, `Worklog`
- **Key Methods:**
    - `canPerformAttendanceAction()`: Worklog validation.
    - `punchIn()`: Late check & auto-switching.
    - `punchOut()`: Task blocking logic.
