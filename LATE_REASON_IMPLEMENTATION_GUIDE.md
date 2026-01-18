# Late Reason Implementation Guide for Frontend

## Overview
When a user attempts to **Punch In** (Office or Field) after their scheduled shift start time (plus any allowed grace period), the API checks if they are "late".

If they are late and have **not provided a reason**, the API rejects the request with a `422 Unprocessable Entity` error. This error response now contains the **list of valid late reasons** configured in the backend.

The frontend is responsible for:
1. Detecting this specific error case.
2. Displaying a modal with the provided options.
3. Resubmitting the Punch In request with the selected reason.

---

## The Workflow

### 1. Initial Punch Attempt
The user taps "Punch In". The frontend sends the standard request:

**Request:**
`POST /api/attendance/punch-in`
```json
{
    "movement_type": "office"
}
```

### 2. API Response (If Late)
The backend calculates the shift timing. If the user is late, it returns a **422** status code.

**Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "require_late_reason": true,
    "message": "Please provide a reason for late punch-in.",
    "late_reasons": [
        {
            "id": 1,
            "reason": "Traffic / Public Transport Delay"
        },
        {
            "id": 2,
            "reason": "Vehicle Breakdown"
        },
        {
            "id": 3,
            "reason": "Family Emergency"
        },
        {
            "id": 4,
            "reason": "Health Issue"
        }
    ]
}
```

### 3. Frontend Logic (UI Step)
1.  Check if `response.status === 422`.
2.  Check if `response.data.require_late_reason === true`.
3.  **Action**: Open a **"Late Reason" Modal** or Bottom Sheet.
4.  **Content**: 
    - Display the `message` from the response ("Please provide a reason...").
    - Render a list of radio buttons or a dropdown using the `response.data.late_reasons` array.
    - (Optional) You may add an "Other" option if you want to allow free text, but strictly speaking, the backend just expects a string.
5.  **User Action**: User selects a reason (e.g., "Vehicle Breakdown") and taps "Submit".

### 4. Retry Punch Attempt
Resend the **exact same request** as step 1, but append the `late_reason` field with the **text** of the selected reason.

**Request:**
`POST /api/attendance/punch-in`
```json
{
    "movement_type": "office",
    "late_reason": "Vehicle Breakdown" 
}
```
*(Note: Send the `reason` string value, not the `id`)*

### 5. Success
If the reason is provided, the API will accept the punch-in and record the reason in the movement description.

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Punched in for office (Cycle 1)",
    ...
}
```

---

## Summary of Changes Required

1.  **Attendance Services/API Call**:
    - Update your error handling to parse `require_late_reason` and `late_reasons` from the error response.
2.  **Attendance Screen**:
    - Add a state variable `showLateReasonModal` (boolean).
    - Add a state variable `lateReasonOptions` (array).
    - Add a `LateReasonModal` component that:
        - Takes the options list as a prop.
        - Returns the selected string on submit.
3.  **Integration**:
    - When the initial punch fails with the specific flag, open the modal with the data from the response.
    - When the modal submits, call the punch API again with the extra parameter.
