# Mobile Employee Location Tracking API Guide

## Overview
This guide documents the API endpoint for tracking employee live location and provides implementation details for the React Native mobile team. The goal is to send the employee's GPS coordinates to the server every **5 seconds**.

---

## 1. API Endpoint Details 

### **Endpoint**
`POST /api/employee/location`

### **Headers**
| Key | Value | Description |
|---|---|---|
| `Content-Type` | `application/json` | Standard JSON content type |
| `Accept` | `application/json` | Expect JSON response |
| `Authorization` | `Bearer <access_token>` | Valid User Auth Token |
| `X-Tenant-ID` | `<tenant_id>` | The current Tenant ID |

### **Request Body**
```json
{
    "employee_id": 1,
    "latitude": 28.5355, 
    "longitude": 77.3910,
    "tracked_at": "2026-02-03 10:30:00" 
}
```
*   `employee_id` (Required): Integer. The ID of the employee being tracked.
*   `latitude` (Required): Numeric, Decimal (e.g., 28.6139).
*   `longitude` (Required): Numeric, Decimal (e.g., 77.2090).
*   `tracked_at` (Optional): String, Date format (Y-m-d H:i:s). Defaults to server time if not provided.

### **Success Response (201 Created)**
```json
{
    "success": true,
    "message": "Location tracked successfully.",
    "data": {
        "employee_id": 123,
        "latitude": 28.5355,
        "longitude": 77.3910,
        "tracked_at": "2026-02-03T10:30:00.000000Z",
        "updated_at": "2026-02-03T10:30:00.000000Z",
        "created_at": "2026-02-03T10:30:00.000000Z",
        "id": 1
    }
}
```

### **Error Response (422 Unprocessable Entity)**
```json
{
    "message": "The latitude field is required.",
    "errors": {
        "latitude": ["The latitude field is required."]
    }
}
```

---

## 2. React Native Implementation Guide

### **Prerequisites**
You will likely use one of the following libraries:
*   `expo-location` (For Expo projects)
*   `react-native-geolocation-service` (For bare React Native CLI)

### **Implementation Logic**
1.  **Permissions**: Ensure you request `LOCATION_FOREGROUND` (and `LOCATION_BACKGROUND` if tracking while minimized) permissions.
2.  **Interval**: Use `setInterval` or a library-specific `watchPosition` method.
3.  **Optimization**: Since 5 seconds is very frequent, ensure the API call is lightweight.

### **Example Code (Generic Approach)**

```javascript
import React, { useEffect, useRef } from 'react';
import { AppState } from 'react-native';
import * as Location from 'expo-location'; // Example using Expo
import axios from 'axios';

const TRACKING_INTERVAL_MS = 5000; // 5 Seconds

const LocationTracker = () => {
  const timerRef = useRef(null);

  useEffect(() => {
    // 1. Request Permissions
    (async () => {
      let { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        console.error('Permission to access location was denied');
        return;
      }

      // 2. Start Tracking Loop
      startTracking();
    })();

    // Cleanup on unmount
    return () => stopTracking();
  }, []);

  const startTracking = () => {
    // Stop any existing timer
    if (timerRef.current) clearInterval(timerRef.current);

    timerRef.current = setInterval(async () => {
      try {
        // A. Get Current Position
        let location = await Location.getCurrentPositionAsync({
          accuracy: Location.Accuracy.Balanced, // Use Balanced to save some battery
        });

        const { latitude, longitude } = location.coords;

        // B. Send to API
        await sendLocationToBackend(latitude, longitude);

      } catch (error) {
        console.error("Error fetching/sending location:", error);
      }
    }, TRACKING_INTERVAL_MS);
  };

  const stopTracking = () => {
    if (timerRef.current) {
      clearInterval(timerRef.current);
      timerRef.current = null;
    }
  };

  const sendLocationToBackend = async (latitude, longitude) => {
    try {
      // Replace with your actual API Wrapper/Axios instance
      const response = await axios.post(
        'https://your-api-url.com/api/employee/location',
        {
          latitude,
          longitude,
          // tracked_at: new Date().toISOString() // Optional
        },
        {
          headers: {
            'Authorization': 'Bearer YOUR_AUTH_TOKEN',
            'X-Tenant-ID': 'YOUR_TENANT_ID',
            'Accept': 'application/json'
          }
        }
      );
      console.log('Location synced:', response.data.message);
    } catch (apiError) {
      console.error('API Sync Error:', apiError.response?.data || apiError.message);
    }
  };

  return null; // This component handles logic only, no UI
};

export default LocationTracker;
```

### **Battery & Performance Considerations**
*   **Battery Drain**: Sending network requests every 5 seconds will consume significant battery.
*   **Background Mode**: If this needs to run when the app is minimized (in pocket), you strictly need **Background Location Permissions** and must use a background task runner (like `TaskManager` in Expo or `Headless JS` in React Native CLI). The simple `setInterval` above **will pause** when the app goes to the background on iOS and some Android versions.
*   **Error Handling**: If the network fails, you might want to queue the locations locally (SQLite/AsyncStorage) and batch sync them when the connection returns, rather than losing data.
