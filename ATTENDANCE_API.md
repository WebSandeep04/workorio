# Attendance API Documentation

The Attendance API allows users to check their status, punch in/out, and manage breaks.

## Authentication

All requests must be authenticated using a **Bearer Token**.
- **Header:** `Authorization: Bearer <your-token>`
- **Token:** Obtained via the `/api/login` endpoint.

## Tenant Context

**CRITICAL:** All attendance requests **MUST** include the `X-Tenant-ID` header to verify the correct organization database.
- **Header:** `X-Tenant-ID: <tenant_id>`
- **Source:** The `tenant_id` is returned in the response of the `/api/login` endpoint.

---

## Endpoints

### 1. Get Today's Status
Returns the current attendance state (punched in, on break, etc.), cycle counts, and worklog validation status.

- **URL:** `/api/attendance/today-status`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "attendance": { ... },
      "movements": { "office": [...], "field": [...], "break": [...] },
      "status": {
          "office": { "status": "Punched In", "can_start": false, "can_end": true, ... },
          "field": { ... },
          "break": { ... }
      },
      "cycles": { "office_cycles": 1, ... },
      "worklog_validation": { "can_perform_attendance": true, "message": "" }
  }
  ```

### 2. Punch In
Punch in for Office or Field work.

- **URL:** `/api/attendance/punch-in`
- **Method:** `POST`
- **Body:**
  ```json
  {
      "movement_type": "office" // or "field",
      "late_reason": "Traffic" // Required if late for first punch-in
  }
  ```
- **Note:** Automatically switches (punches out) if switching between Office/Field.

### 3. Punch Out
Punch out from Office or Field work.

- **URL:** `/api/attendance/punch-out`
- **Method:** `POST`
- **Body:**
  ```json
  {
      "movement_type": "office" // or "field"
  }
  ```

### 4. Start Break
Start a break (pauses work).

- **URL:** `/api/attendance/break/start`
- **Method:** `POST`

### 5. End Break
End a break (resumes ability to work).

- **URL:** `/api/attendance/break/end`
- **Method:** `POST`

### 6. Check Worklog Validation
Check if the user is allowed to perform attendance actions (i.e., has completed past worklogs).

- **URL:** `/api/attendance/check-validation`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "can_perform_attendance": true, // or false
      "message": "You must complete your worklog entry..." // if false
  }
  ```
