# Leave Management API Documentation

This document outlines the API endpoints for managing leaves. These endpoints allow the mobile application to list, create, update, and delete leave requests.

## Base URL
`http://your-domain.com/api` (Replace with actual server URL)

## Authentication & Headers
All requests to the Leave API **must** include the following headers:

| Header | Value | Description |
| :--- | :--- | :--- |
| `Authorization` | `Bearer <your_token>` | Using the token received from the `/api/login` endpoint. |
| `X-Tenant-ID` | `<tenant_id>` | The Tenant ID received from the `/api/login` endpoint. |
| `Content-Type` | `application/json` | Required for POST/PUT requests. |
| `Accept` | `application/json` | Ensures responses are in JSON format. |

---

## Endpoints

### 1. Get All Leaves
Fetches a history of all leaves applied by the authenticated user.

*   **Endpoint:** `/leave`
*   **Method:** `GET`

**Success Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 15,
            "user_id": 102,
            "leave_type_id": 1,
            "date": "2024-02-20",
            "reason": "Family function",
            "status": "approved",
            "created_at": "2024-02-10T08:30:00.000000Z",
            "updated_at": "2024-02-10T08:30:00.000000Z",
            "leave_type": {
                "id": 1,
                "name": "Casual Leave",
                "working_hours": 0
            }
        },
        {
            "id": 12,
            "user_id": 102,
            "leave_type_id": 2,
            "date": "2024-01-15",
            "reason": "Sick",
            "status": "approved",
            "created_at": "2024-01-14T09:00:00.000000Z",
            "updated_at": "2024-01-14T09:00:00.000000Z",
            "leave_type": {
                "id": 2,
                "name": "Sick Leave",
                "working_hours": 0
            }
        }
    ]
}
```

---

### 2. Get Leave Types
Fetches available leave types (Entry Types with 0 working hours) to populate dropdowns.

*   **Endpoint:** `/leave/types`
*   **Method:** `GET`

**Success Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Casual Leave",
            "working_hours": 0,
            "created_at": "...",
            "updated_at": "..."
        },
        {
            "id": 2,
            "name": "Sick Leave",
            "working_hours": 0,
            "created_at": "...",
            "updated_at": "..."
        }
    ]
}
```

---

### 3. Apply for Leave
Creates a new leave request.
*Note: Leaves are automatically approved upon creation.*

*   **Endpoint:** `/leave`
*   **Method:** `POST`

**Request Body:**
```json
{
    "date": "2024-03-01",          // Required: YYYY-MM-DD
    "leave_type_id": 1,            // Required: ID from /leave/types
    "reason": "Going to doctor"    // Optional: String (max 1000 chars)
}
```

**Success Response (201 Created):**
```json
{
    "success": true,
    "message": "Leave applied successfully.",
    "data": {
        "user_id": 102,
        "date": "2024-03-01",
        "leave_type_id": 1,
        "reason": "Going to doctor",
        "status": "approved",
        "updated_at": "2024-02-17T12:00:00.000000Z",
        "created_at": "2024-02-17T12:00:00.000000Z",
        "id": 16
    }
}
```

**Error Response (422 Unprocessable Entity):**
*   If data validation fails (e.g., date required)
*   If leave already exists for that date
*   If worklog already exists for that date
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "date": ["The date field is required."]
    }
}
```
**OR**
```json
{
    "success": false,
    "message": "Leave already exists for this date."
}
```

---

### 4. Update Leave
Updates an existing leave request.

*   **Endpoint:** `/leave/{id}`
*   **Method:** `PUT`
*   **URL Parameter:** `{id}` - The ID of the leave record.

**Request Body:**
```json
{
    "date": "2024-03-02",          // Required
    "leave_type_id": 1,            // Required
    "reason": "Rescheduled appointment" // Optional
}
```

**Success Response (200 OK):**
```json
{
    "success": true,
    "message": "Leave updated successfully.",
    "data": {
        "id": 16,
        "user_id": 102,
        "leave_type_id": 1,
        "date": "2024-03-02",
        "reason": "Rescheduled appointment",
        "status": "approved",
        "created_at": "...",
        "updated_at": "..."
    }
}
```

**Error Response (404 Not Found):**
```json
{
    "success": false,
    "message": "Leave not found."
}
```

---

### 5. Delete Leave
Deletes a leave request.

*   **Endpoint:** `/leave/{id}`
*   **Method:** `DELETE`
*   **URL Parameter:** `{id}` - The ID of the leave record.

**Success Response (200 OK):**
```json
{
    "success": true,
    "message": "Leave deleted successfully."
}
```

**Error Response (404 Not Found):**
```json
{
    "success": false,
    "message": "Leave not found."
}
```
