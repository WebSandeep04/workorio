# Attendance API Documentation

This document outlines the flows, rules, and API endpoints for the Attendance System. It is designed for the frontend team (React Native) to implement attendance features correctly.

## 1. Core Logic & Rules

The attendance system enforces several strict validation rules. Frontend implementations **must** handle these specific error scenarios.

### A. Worklog Compliance Rule (Blocker)
*   **Rule**: Users cannot punch in, punch out, or start breaks if they have pending worklogs for previous working days.
*   **Check**: This is checked before *any* attendance action.
*   **Response**: `403 Forbidden`
*   **Message**: "You must complete your worklog entry..."
*   **UI Behavior**: If this error occurs, disable all attendance buttons and redirect/prompt the user to the Worklog screen.

### B. First Punch-in of the Day Logic
The **First Punch In** (Office or Field) of the day triggers two special checks:

#### 1. Late Validation
*   **Logic**: Checks if the user is punching in after their assigned shift start time + grace period.
*   **Response**: `422 Unprocessable Entity`
*   **Data**: Returns `{ require_late_reason: true, late_reasons: [...] }`.
*   **UI Behavior**:
    *   Do **not** show a generic error.
    *   Open a modal asking the user to select a reason.
    *   Resend the `punch-in` request with the selected `late_reason`.

#### 2. Location Validation (Geo-fencing)
*   **Logic**: If the user has `is_place_allowed = true`, they **MUST** be within the assigned radius of one of their allowed places.
*   **Requirement**: You **must** capture `latitude` and `longitude` from the device and send it with the **first** punch-in request.
*   **Response**: `403 Forbidden`
*   **Message**: "You are not within the allowed radius. Closest: [Place Name] ([Distance]m away, allowed [Radius]m)."
*   **UI Behavior**: Show the specific error message to the user so they know they are too far away.

---

## 2. API Endpoints

**Base URL**: `/api/attendance`
**Headers**:
*   `Authorization`: `Bearer <token>`
*   `Accept`: `application/json`

### 1. Check Worklog Validation
Call this on screen load to see if the user is blocked.

*   **Endpoint**: `GET /check-worklog-validation`
*   **Response**:
    ```json
    {
        "can_perform_attendance": false,
        "message": "You must complete your worklog entry...",
        "user_has_worklog_access": true
    }
    ```

### 2. Get Today's Status
Call this to load the dashboard state (buttons, timers, history).

*   **Endpoint**: `GET /today-status`
*   **Response**:
    ```json
    {
        "attendance": { ... },
        "status": {
            "office": {
                "status": "Ready for New Cycle",
                "can_start": true,
                "can_end": false,
                "badge_class": "badge-secondary"
            },
            "field": { ... },
            "break": { ... }
        },
        "movements": { "office": [...], "field": [...] },
        "cycles": { "office_cycles": 1, ... }
    }
    ```

### 3. Punch In
Used for both **Office** and **Field** entry.

*   **Endpoint**: `POST /punch-in`
*   **Payload**:
    ```json
    {
        "movement_type": "office" | "field",
        "latitude": 26.4521,    // REQUIRED for first punch
        "longitude": 80.3321,   // REQUIRED for first punch
        "late_reason": "Traffic" // OPTIONAL (send if server requested it)
    }
    ```
*   **Distance Error Response (403)**:
    ```json
    {
        "success": false,
        "message": "You are not within the allowed radius. Closest: Branch A (500m away, allowed 50m)."
    }
    ```
*   **Late Validation Error Response (422)**:
    ```json
    {
        "success": false,
        "require_late_reason": true,
        "message": "Please provide a reason for late punch-in.",
        "late_reasons": [ { "id": 1, "reason": "Traffic" }, ... ]
    }
    ```
*   **Success Response (200)**:
    ```json
    {
        "success": true,
        "message": "Punched in for office",
        "movement": { ... },
        "show_task_reminder": false
    }
    ```

### 4. Punch Out
*   **Endpoint**: `POST /punch-out`
*   **Payload**:
    ```json
    {
        "movement_type": "office" | "field"
    }
    ```
*   **Task Blocker Error (422)**:
    *   If user has pending tasks not updated today, request will fail.
    *   **Message**: "You have X pending task(s) that were not updated today..."

### 5. Start Break
*   **Endpoint**: `POST /start-break`

### 6. End Break
*   **Endpoint**: `POST /end-break`

### 7. Attendance History
*   **Endpoint**: `GET /history`
*   **Params**: `month` (1-12), `year` (2024), `per_page` (number)

---

## 3. Implementation Flow for React Native

1.  **On Screen Load**:
    *   Call `GET /check-worklog-validation`.
    *   If `can_perform_attendance` is **false**: Lock UI, show Alert, provide button to Worklog.
    *   Else: Call `GET /today-status` to render current state (In/Out buttons).

2.  **On Punch In Press**:
    *   **Step 1**: Get Device Location (`HighAccuracy`).
    *   **Step 2**: specific API call to `POST /punch-in` with `lat` / `long`.
    *   **Step 3**:
        *   **Success**: Update UI.
        *   **Error 403 (Location)**: Show Alert with the distance message from server.
        *   **Error 422 (Late)**: If response has `require_late_reason: true`, open **Modal** with dropdown of `late_reasons`.
            *   User selects reason -> Call `POST /punch-in` again with `late_reason` + `lat` + `long`.

3.  **On Punch Out Press**:
    *   Call `POST /punch-out`.
    *   **Error 422 (Tasks)**: Show Alert telling user to update their tasks first.

4.  **Auto-Switching**:
    *   Note that switching from **Office -> Field** (or vice versa) is handled automatically on the server. The user just needs to punch IN to the new mode; the server will auto-out the previous mode.
