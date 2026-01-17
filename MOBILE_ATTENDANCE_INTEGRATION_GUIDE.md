# Attendance System - Mobile Integration Guide

This document provides a complete reference for implementing the Attendance System in the mobile application. It covers authentication, business logic, API endpoints, and UI state management.

---

## 1. Core Principles

The attendance system is **movement-based**, meaning we track specific actions (`in`, `out`, `start`, `end`) rather than just a simple daily clock-in.

### Key Concepts
- **Movements**: There are 3 types of movements:
  - **Office**: Working from the office.
  - **Field**: Working outside (client visits, etc.).
  - **Break**: Taking a break.
- **Cycles**: A complete session (e.g., Punch In -> Punch Out) is one cycle. Users can have multiple cycles per day.
- **Auto-Switching**: The backend handles switching between Office and Field automatically.
  - *Example:* If a user is "Office In" and punches "Field In", the system automatically marks "Office Out" first. The app does **not** need to make two API calls.

---

## 2. Authentication & Headers

### Step 1: Login
First, authenticate the user to get their token and tenant ID.
- **Endpoint**: `POST /api/login`
- **Body**: `{ "email": "...", "password": "..." }`
- **Response**:
  ```json
  {
      "success": true,
      "data": {
          "token": "10|...",
          "tenant_id": 1,
          ...
      }
  }
  ```
> **Important**: Save the `token` and `tenant_id` from this response. You will need them for ALL subsequent requests.

### Step 2: Authenticated Requests
Every subsequent API request (e.g., Attendance) **MUST** include these **two headers**:

| Header | Value | Description |
| :--- | :--- | :--- |
| `Authorization` | `Bearer <token>` | The token from login response. |
| `X-Tenant-ID` | `<tenant_id>` | **REQUIRED**. The `tenant_id` from login response. |

> **Critical Note:** If you omit `X-Tenant-ID`, the API will default to the wrong database or fail with `401 Unauthorized` / `Tenant not found`.

---

## 3. Workflow & Validations (Frontend Logic)

### A. Pre-Check (The "Worklog Block")
Before allowing *any* action, the system checks if the user has pending worklogs from previous days.
- **API Source**: `GET /api/attendance/today-status` -> `worklog_validation` object.
- **UI Logic**:
  - If `worklog_validation.can_perform_attendance` is `false`:
    - **DISABLE** all Punch In/Out/Break buttons.
    - **SHOW** the error message: `worklog_validation.message`.
    - Redirect user to the Worklog screen if possible.

### B. Punch In Logic (Office & Field)
- **Late Check (First Punch Only)**:
  - If it's the user's *first* punch of the day and they are late (based on shift), the API will return a `422 Unprocessable Entity` error.
  - **Handling**:
    1. Check if response body has `require_late_reason: true`.
    2. If yes, **Show a Modal** asking for a "Late Reason".
    3. **Retry** the API call, sending the reason in the `late_reason` body field.

### C. Punch Out Logic (The "Task Block")
- **Task Validation**:
  - The system prevents punching out if there are pending tasks (due today or older) that haven't been updated today.
  - **Handling**:
    - If API returns `422` with a message about "pending tasks", **Show an Alert**.
    - Do not allow punch out until they update those tasks.

---

## 4. API Reference

### 1. Get Today's Status (Initial Load)
Call this on screen load to determine which buttons to show.

- **Endpoint**: `GET /api/attendance/today-status`
- **Response Analysis for UI**:
  The response specifically sends a `status` object for `office`, `field`, and `break`. Use this to toggle buttons.

  ```json
  {
      "status": {
          "office": {
              "status": "Punched In",     // Display Text
              "badge_class": "badge-success",
              "can_start": false,         // Hide "Punch In" button
              "can_end": true,            // Show "Punch Out" button
              "last_action_time": "..."
          },
          "field": { ... },
          "break": { ... }
      },
      "worklog_validation": {
          "can_perform_attendance": true, // If false, BLOCK EVERYTHING
          "message": "..."
      }
  }
  ```

### 2. Punch In (Office or Field)
- **Endpoint**: `POST /api/attendance/punch-in`
- **Body**:
  ```json
  {
      "movement_type": "office", // or "field"
      "late_reason": "Traffic jam" // Optional, send only if requested
  }
  ```
- **Mobile Flow**:
  1. User Taps "Punch In".
  2. Call API.
  3. **Success (200)**: Refresh Status.
  4. **Error (422)**: Check if `require_late_reason` is true.
     - **Yes**: Show Input Modal -> User enters reason -> Call API again with `late_reason`.
     - **No**: Show error message toast.

### 3. Punch Out (Office or Field)
- **Endpoint**: `POST /api/attendance/punch-out`
- **Body**:
  ```json
  {
      "movement_type": "office" // or "field"
  }
  ```
- **Mobile Flow**:
  1. User Taps "Punch Out".
  2. Call API.
  3. **Error (422)**: If message says "pending task(s)", show Alert: *"Please update your pending tasks before leaving."*

### 4. Break Management
There is no "late" or "task" check for breaks, but you cannot start a break if one is already active.

- **Start Break**: `POST /api/attendance/break/start`
- **End Break**: `POST /api/attendance/break/end`
- **UI Rule**: When a Break is active (`status.break.can_end == true`), **Disable** Office and Field buttons. User *must* end the break first.

### 5. Attendance History
Fetch user's attendance records with summary stats.

- **Endpoint**: `GET /api/attendance/history`
- **Query Params**: `page`, `per_page`, `month`, `year`
- **Response**:
  ```json
  {
      "data": [
          {
              "id": 105,
              "date": "2024-01-17",
              "display_date": "Wed, Jan 17, 2024",
              "status": "Present",
              "punch_in": "09:00 AM",
              "punch_out": "06:00 PM",
              "field_in": "-",
              "field_out": "-",
              "total_office_time": "09:00 hrs",
              "formatted_hours": {
                  "office": "9h 00m",
                  "field": "-",
                  "total": "9h 00m"
              },
              "cycles": { "office": 1, "field": 0, "break": 1 }
          }
      ],
      "current_page": 1,
      "last_page": 5
  }
  ```

### 6. Active Employees Birthdays
Fetch list of all active employees with their names and dates of birth.

- **Endpoint**: `GET /api/employees/birthdays`
- **Headers**: Same as other requests (`Authorization`, `X-Tenant-ID`)
- **Response**:
  ```json
  {
      "success": true,
      "count": 10,
      "data": [
          {
              "name": "Alice Smith",
              "dob": "1992-05-15",
              "employee_code": "EMP001"
          },
          {
              "name": "Bob Jones",
              "dob": "1988-11-23",
              "employee_code": "EMP042"
          }
      ]
  }
  ```

---

## 5. UI Implementation Checklist

1.  [ ] **Global State**: On load, check `worklog_validation`. If valid, render buttons. If invalid, show blocking error overlay.
2.  [ ] **Button State**:
    - **Office**: Show "Punch In" if `status.office.can_start` is true. Else show "Punch Out".
    - **Field**: Same logic as Office.
    - **Break**: Show "Start Break" if `status.break.can_start` is true. Else show "End Break".
3.  [ ] **Break Interaction**:
    - If `status.break.status` == "On Break", then **Disable** Office/Field buttons.
    - *Tip:* Add a visual indicator (e.g., "You are on break").
4.  [ ] **Late Handling**: Implement the "Reason" modal popup for the 422 Late error.
5.  [ ] **Error Handling**: Display standard toast messages for other errors (e.g., "Already punched in").

## 6. Example Error Responses

**Late Error (Requires Action):**
```json
{
    "success": false,
    "require_late_reason": true,
    "message": "Please provide a reason for late punch-in."
}
```

**Task Blocker Error (Requires Action):**
```json
{
    "success": false,
    "message": "You have 3 pending task(s) that were not updated today..."
}
```

**Worklog Blocker (`today-status` response):**
```json
{
   "worklog_validation": {
       "can_perform_attendance": false,
       "message": "You must complete your worklog entry for Friday, Jan 12..."
   }
}
```
