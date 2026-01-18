# Mobile Worklog API Integration Guide

This guide details the API endpoints for implementing the Worklog (Timesheet) module in the React Native mobile application.

## Base URL & Authentication

**Base URL**: `https://<your-domain>/api`

**Headers Required**:
- `Content-Type`: `application/json`
- `Accept`: `application/json`
- `Authorization`: `Bearer <user_token>`
- `X-Tenant-ID`: `<tenant_id>` (Required for all requests)

---

## 1. Form Data & Cascading Dropdowns

To build the worklog form, you need to fetch data in a specific order:
1. Fetch Entry Types & Customers (On Load)
2. Select Customer -> Fetch Projects/Services
3. Select Service -> Fetch Modules

### A. Get Initial Form Data
Fetches the list of available Entry Types and Customers.

- **Endpoint**: `GET /worklog/form-data`
- **Response**:
  ```json
  {
    "success": true,
    "entry_types": [
      { "id": 1, "name": "Development", "working_hours": 8 },
      { "id": 2, "name": "Half Day", "working_hours": 4 }
    ],
    "customers": [
      { "id": 101, "name": "Acme Corp" }
    ]
  }
  ```

### B. Get Projects (By Customer)
*Note: In some flows, you might pick a Project directly.*

- **Endpoint**: `GET /worklog/projects/{customerId}`
- **Response**:
  ```json
  {
    "success": true,
    "projects": [
      { "name": "Website Redesign" }
    ]
  }
  ```

### C. Get Services (By Customer & Optional Project)
This is the primary second step. If a user selected a project name above, pass it as a query param.

- **Endpoint**: `GET /worklog/services/{customerId}?project_name={optional_name}`
- **Response**:
  ```json
  {
    "success": true,
    "services": [
      { "id": 50, "name": "Backend API" }
    ]
  }
  ```

### D. Get Modules (By Service)
Final dropdown step.

- **Endpoint**: `GET /worklog/modules/{serviceId}`
- **Response**:
  ```json
  {
    "success": true,
    "modules": [
      { "id": 5, "name": "Authentication" }
    ]
  }
  ```

---

## 2. Validation Checks

Before allowing the user to fill out the form for a specific date, validate it.

### Validate Date
Checks if the selected date is valid (chronological order, no gaps).

- **Endpoint**: `POST /worklog/validate-date`
- **Body**:
  ```json
  {
    "date": "2023-10-27"
  }
  ```
- **Response (Success)**:
  ```json
  {
    "success": true, 
    "message": "Date is valid."
  }
  ```
- **Response (Error)**:
  ```json
  {
    "success": false, 
    "message": "Please complete missing worklog for 2023-10-26 first."
  }
  ```

---

## 3. Submission

The worklog submission allows sending multiple entries for a single day at once.

### Submit Worklog
- **Endpoint**: `POST /worklog/submit`
- **Validation Rules**:
  - User MUST be **Punched Out** or **Field Out** for the day if submitting for Today.
  - Total time (hours + minutes) across all entries must meet the `working_hours` of the selected `entry_type_id`.
  - Date must be valid (no missing previous days).

- **Body**:
  ```json
  {
    "work_date": "2023-10-27",
    "entries": [
      {
        "entry_type_id": 1, 
        "customer_id": 101,
        "service_id": 50,
        "module_id": 5,
        "hours": 4,
        "minutes": 30,
        "description": "Fixed login bug"
      },
      {
        "entry_type_id": 1,
        "customer_id": 101,
        "service_id": 51,
        "module_id": 6,
        "hours": 3,
        "minutes": 30,
        "description": "Meeting"
      }
    ]
  }
  ```

- **Response (Success)**:
  ```json
  {
    "success": true,
    "message": "Worklog submitted successfully"
  }
  ```

- **Response (Error - Time Mismatch)**:
  ```json
  {
    "success": false,
    "message": "Total logged time (5h 0m) is less than Development requirement (8h)."
  }
  ```

---

## 4. History & Management

### Get Worklog History
Fetch past worklogs with pagination.

- **Endpoint**: `GET /worklog/history`
- **Query Params**:
  - `page`: Page number (default 1)
  - `per_page`: Items per page (default 20)
  - `month`: Filter by month (1-12)
  - `year`: Filter by year (e.g., 2023)

- **Response**:
  ```json
  {
    "current_page": 1,
    "data": [
      {
        "id": 150,
        "work_date": "2023-10-27",
        "hours": 4,
        "minutes": 30,
        "description": "Fixed login bug",
        "status": "pending", // pending, approved, rejected
        "customer": { "name": "Acme Corp" },
        "entry_type": { "name": "Development" }
      }
    ],
    "total": 50
  }
  ```

### Delete Entry
Only `pending` entries can be deleted.

- **Endpoint**: `DELETE /worklog/{id}`
- **Response**:
  ```json
  {
    "success": true,
    "message": "Entry deleted"
  }
  ```
