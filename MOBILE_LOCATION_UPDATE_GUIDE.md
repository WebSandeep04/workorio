# Mobile App Location & Attendance Update Guide

## Overview
This guide details the changes required in the React Native mobile application to support location tracking and automatic place detection during attendance actions.

**Core Requirement:** All attendance-related API calls must now include the user's current GPS coordinates (`latitude` and `longitude`).

---

## 1. Updated API Endpoints

The following endpoints now accept and require location data:

1.  **Punch In**: `POST /api/attendance/punch-in`
2.  **Punch Out**: `POST /api/attendance/punch-out`
3.  **Start Break**: `POST /api/attendance/break/start`
4.  **End Break**: `POST /api/attendance/break/end`

---

## 2. Request Format (Payload)

For **ALL** request bodies sent to the endpoints above, include these two new fields:

| Field | Type | Required? | Description |
| :--- | :--- | :--- | :--- |
| `latitude` | Number (Decimal) | **YES** | The user's current latitude (e.g., `26.9124`). |
| `longitude` | Number (Decimal) | **YES** | The user's current longitude (e.g., `75.7873`). |
| `movement_type` | String | YES | Existing field (`office`, `field`, `break`). |

### Example JSON Payload (Punch In)

```json
{
    "movement_type": "office",
    "latitude": 26.9124336,
    "longitude": 75.7872709,
    "late_reason": "Traffic jam" // Optional, only if late
}
```

### Example JSON Payload (Punch Out / Break)

```json
{
    "movement_type": "break",
    "latitude": 26.9124336,
    "longitude": 75.7872709
}
```

---

## 3. Location Validation & Errors

The backend performs strict validation on the **FIRST Punch-In of the day**.

### Scenarios:

1.  **Missing Location Data (HTTP 422)**
    *   If `latitude` or `longitude` is missing in the request.
    *   **Response:**
        ```json
        {
            "success": false,
            "message": "Location access is required for attendance. Please enable location services."
        }
        ```
    *   **Action:** Ensure the app requests GPS permissions and successfully retrieves the location *before* checking the API.

2.  **Out of Range (HTTP 403)**
    *   If the user is assigned specific "Allowed Places" and is outside the allowed radius.
    *   **Response:**
        ```json
        {
            "success": false,
            "message": "You are not within the allowed radius. Closest: Office Headquarter (500m away, allowed 100m)."
        }
        ```
    *   **Action:** Show this error message to the user. Do not allow them to proceed until they are in range.

---

## 4. "Place" Logic (Backend Handled)

**You do NOT need to calculate or send the place name.**

*   **How it works:** When you send `latitude` and `longitude`, the backend automatically checks if these coordinates match any of the user's assigned "Places".
*   **Result:** If a match is found, the backend saves the Place Name in the database. If no match is found (orphaned location), it saves `null`.
*   **Mobile Responsibility:** JUST SEND ACCURATE COORDINATES.

---

## 5. Implementation Checklist

- [ ] **Permission:** Ensure `ACCESS_FINE_LOCATION` is requested and granted.
- [ ] **Accuracy:** Use high-accuracy mode for fetching location to ensure validation passes.
- [ ] **State:** Capture location immediately before the API call to prevent stale data.
- [ ] **Error Handling:** Gracefully handle the `422` (Missing Location) and `403` (Out of Range) errors by displaying the backend message to the user.
- [ ] **Updates:** Update all 4 calls (`punchIn`, `punchOut`, `startBreak`, `endBreak`) to include the new params.
