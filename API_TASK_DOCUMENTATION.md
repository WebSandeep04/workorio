# Task Management API Documentation

## Authentication
All endpoints require the `Authorization` header with a valid Bearer token.
Header: `Authorization: Bearer <token>`
Header: `X-Tenant-ID: <tenant_id>` (Required for tenant database selection)

## Base URL
`/api`

---

## 1. Get Form Data (Dropdowns)
Fetch all necessary data for creating/filtering tasks in one call.

**Endpoint:** `GET /tasks/form-data`

**Response:**
```json
{
    "success": true,
    "customers": [
        { "id": 1, "name": "Acme Corp" }
    ],
    "users": [
        { "id": 5, "name": "John Doe" }
    ],
    "statuses": [
        { "id": 1, "name": "Pending", "color": "#ff0000" }
    ],
    "priorities": [
        { "id": 1, "name": "High", "color": "#ff0000" }
    ]
}
```

---

## 2. Get Created Tasks
Fetch tasks created by the logged-in user.

**Endpoint:** `GET /tasks/created`

**Response:**
```json
{
    "success": true,
    "tasks": [
        {
            "id": 101,
            "task_name": "Fix Bug #123",
            "task": "Detailed description...",
            "is_done": false,
            "created_at": "2024-01-15T10:00:00.000000Z",
            "status": { "name": "In Progress" },
            "assignedUsers": [ ... ]
        }
    ]
}
```

---

## 3. Get Assigned Tasks (My Tasks)
Fetch tasks assigned to the logged-in user.

**Endpoint:** `GET /tasks/assigned`

**Response:** Same structure as Created Tasks.

---

## 4. Create Task
Create a new task with assignees and optional attachments.

**Endpoint:** `POST /tasks`
**Content-Type:** `multipart/form-data`

**Body Params:**
| Field | Type | Required | Description |
|---|---|---|---|
| `customer_id` | Integer | Yes | ID of the customer |
| `user_ids[]` | Array/Int | Yes | Array of user IDs or single ID |
| `task_name` | String | Yes | Title of the task |
| `task` | String | Yes | Description |
| `task_type` | String | No | 'task' (default), 'qc', 'cp' |
| `task_priority_id` | Integer | No | ID of priority |
| `task_status_id` | Integer | No | ID of status (default 1) |
| `due_date` | Date | No | YYYY-MM-DD |
| `is_recurring` | Boolean | No | 0 or 1 |
| `recurrence_type` | String | If Recur | 'daily', 'weekly', 'monthly', 'yearly' |
| `recurrence_interval`| Integer | If Recur | e.g. 1 (every 1 week) |
| `recurrence_days_of_week[]`| Array | If Weekly | ['mon', 'wed'] |
| `recurrence_end_date`| Date | If Recur | End date of recurrence |
| `images[]` | File | No | Array of images (jpeg, png, jpg, gif) |

---

## 5. View Task Details
**Endpoint:** `GET /tasks/{id}`

**Response:** Returns full task object with relationships.

---

## 6. Update Task
Update an existing task. Use `POST` because HTML forms/FormData do not support `PUT` for file uploads well.

**Endpoint:** `POST /tasks/{id}` (Method Spoofing not strictly needed if backend handles post, but standard Laravel might expect simple POST for files)

**Body Params:**
Same as Create Task.
Use `user_ids` to **replace** the current assignee list.

---

## 7. Update Task Status
Quickly update status (e.g., for Kanban drag-drop).

**Endpoint:** `POST /tasks/{id}/status`

**Body Params:**
- `task_status_id`: Integer (ID of new status)
- OR `status`: String (Name of status, e.g., "Done")

---

## 8. Toggle Done
Mark a task as complete/incomplete (Boolean toggle).

**Endpoint:** `POST /tasks/{id}/toggle-done`

---

## 9. Add Remark (Comment)
**Endpoint:** `POST /tasks/{id}/remarks`

**Body Params:**
- `remark`: String (Text content)

**Response:**
Returns the created remark object.

---

## 10. Delete Image
**Endpoint:** `DELETE /tasks/{task_id}/images/{image_id}`

---

## 11. Delete Task
**Endpoint:** `DELETE /tasks/{id}`
