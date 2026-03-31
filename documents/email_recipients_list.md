# Web Application - Email Recipients & Involvement List

This document lists all the automated and manual email notifications sent by the system, identifying their primary recipients and the parties involved or whose data is included in the emails.

## 1. Automated Reports (Console Commands)

| Report/Email Name | Recipients | Parties Involved | Description |
| :--- | :--- | :--- | :--- |
| **Morning Attendance Report** | All users with valid emails in the tenant | Active Employees | Daily summary of the first punch-in/movement of employees. |
| **Night Attendance Report** | All active employees | Active Employees | Daily evening summary including monthly attendance breakdown and hours worked. |
| **Worklog Today Report** | Users with `is_worklog = 1` or `role_id = 1` (Admins) | All users with worklog entries | Daily summary of worklog submissions for the current day. |
| **Worklog Yesterday Report** | Users with `is_worklog = 1` or `role_id = 1` (Admins) | All users with worklog entries | Summary of worklog submissions from the previous day. |
| **Admin Follow-Up Report** | Tenant Admins (`role_id = 1`) or specific emails if Tenant ID is 1 | Sales Users & Leads | Consolidated daily report of pending follow-ups and new leads. |
| **User Follow-Up Report** | Users with `is_sales = 1` | Sales Users & their assigned Leads | Individual reminders for pending sales follow-ups. |
| **Subscription Report** | Users with `is_subscription = 1` | Customers & System Admins | Daily summary of subscription statuses, renewals, and overdue payments. |
| **Self Task Report** | Individual users with `is_task = 1` | The assigned User & Customers | Individual reminders for the user's personal pending tasks. |
| **All Tasks Report** | Users with `is_task = 1` | All users with pending tasks | Consolidated summary of all tasks across the system, grouped by user. |
| **Calendar Report** | Users with `is_calendar` or `is_calander = 1` | Calendar Clients & Events | Summary of events and birthdays for the range/month. |

## 2. Dynamic Notifications (Event/Action Driven)

| Notification Name | Recipients | Parties Involved | Triggering Action |
| :--- | :--- | :--- | :--- |
| **Password Reset OTP** | The user requesting reset | The individual User | User requests a password reset via the "Forgot Password" page. |
| **Attendance Rejection** | The user whose attendance was rejected | The User & the Rejecting Admin/Manager | An admin or manager rejects a pending attendance record. |
| **Leave Status (Approve/Reject)** | The user who requested leave | The User & the Approving Manager/Admin | A manager or admin updates the status of a leave request. |
| **Task Poke Reminder** | All users assigned to the task | Assigned User, Creator, & Customer | A user "pokes" a task to remind the assignee via the Task Management UI. |
| **New Lead Notification (API/Web)** | All sales users (`is_sales = 1`) | The new lead, Creator (User), & Sales team | A new prospect or sales record is created in the system. |
| **IndiaMART Lead Notification** | All sales users (`is_sales = 1`) | IndiaMART Lead, Customer, & Sales team | A new lead is received from IndiaMART via webhook. |

## 3. Recipient & Involvement Logic (Updated)

*   **Active Employee Scope**: All automated reports and dynamic notifications are now filtered to include only **Active Employees** (where `status = 'active'` in the `employees` table).
    *   **Recipients**: Emails are only dispatched to users identified as active employees.
    *   **Data Involvement**: Report data (Worklogs, Tasks, Sales Leads, etc.) only includes entries associated with active employees.
*   **Admins (`role_id = 1`)**: Receive system-wide reports provided they are active employees.
*   **Sales Users (`is_sales = 1`)**: Receive lead-related notifications if active.
*   **Active Employees**: Receive attendance and status updates; inactive employees are excluded from all communication flows.
*   **Task Assignees**: Receive personal task details; tasks from inactive users are excluded from the "All Tasks" summary.
*   **Specific Tenant 1 (Triserv) Admins**: Hardcoded for specific reports (e.g., sandeep@triserv360.com, shamshad@triserv360.com, etc.); status check is bypassed for these specific internal addresses.
