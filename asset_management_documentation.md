# Asset Management System Documentation

## Overview
The Asset Management system is a comprehensive module designed to track physical assets within the organization, manage their allocation to employees, and maintain a detailed history of their movement and condition.

---

## 1. Database Architecture & Models

### Core Models
- **`Asset`**: The central model representing an individual inventory item.
  - **Relationships**:
    - `belongsTo(AssetCategory)`: Groups assets into categories.
    - `belongsTo(AssetType)`: Defines the type of asset (e.g., Laptop, Mobile).
    - `belongsTo(Supplier)`: Tracks where the asset was purchased.
    - `hasMany(AssetAssignment)`: Tracks all past and current allocations.
    - `hasOne(currentAssignment)`: Helper to get the active assignment record.
  - **Key Features**: Supports `custom_fields_data` (JSON) to store category-specific information like RAM, Processor, or IMEI numbers.

- **`AssetAssignment`**: Manages the link between an Asset and an Employee.
  - **Attributes**: `assigned_date`, `return_date`, `status` (assigned/returned), and `description`.
  - **Logic**: When a new assignment is created, the system automatically marks any previous active assignment for that asset as "returned".

- **`AssetCategory` & `AssetCategoryField`**:
  - Allows administrators to define dynamic fields for different types of assets.
  - For example, the "Laptop" category can have fields like "Processor" and "RAM", while "Furniture" might have "Material".

- **`AssetAssignmentLog`**:
  - Automatically records changes when an asset is reassigned from one employee to another without being formally checked back into inventory.

---

## 2. Controller Logic

### `AssetController`
Manages the inventory lifecycle:
- **`fetch()`**: Returns a paginated list of assets with support for complex filtering (Search, Category, Employee, Date Range).
- **`store()` / `update()`**: Validates and saves asset details, including the dynamic JSON data for custom fields.
- **`history($id)`**: Aggregates all assignment records and reassignment logs for a specific asset to provide a full audit trail.

### `AssetManagementController`
Manages the allocation and dashboard:
- **`index()`**: Initializes the main view with necessary dropdown data (Employees, Categories, Types).
- **`getSummaryStats()`**: Calculates the four KPI cards: **Total Assets**, **Available Assets**, **Assigned Assets**, and **Return Due**.
- **`fetch()`**: Supports two distinct data formats:
  - **Plain List**: For the "Assignments" table.
  - **Group By User**: For the "Assignments By User" view, showing counts per employee.
- **`getAssetsByCategory()`**: Used to populate dropdowns with only available assets for quicker assignment.

---

## 3. User Interface & Experience (`index.blade.php`)

The interface is built as a dynamic dashboard using **Blade, Bootstrap, and jQuery/AJAX**.

### View Modes
1.  **Assignments View**: A chronological list of all asset movements.
2.  **Assignments By User**: A summary list showing each employee and the total number of assets currently in their possession.
3.  **All Assets List**: An accordion-style view grouped by category. This provides a clear high-level view of the entire inventory.

### Key UI Features
- **Dynamic Custom Fields**: When creating or editing an asset, selecting a category instantly fetches and renders the appropriate input fields for that category's specific attributes.
- **Filtering System**: Global filters (Category, Employee, Date Range) apply to all view modes and the summary KPI cards simultaneously.
- **Modals**: 
  - **Create Asset**: Traditional form with dynamic fields.
  - **Assign Asset**: Quick allocation with auto-filtering for available items.
  - **Asset History**: Opens a timeline of who had the asset and when.
  - **User Assets**: Detailed list of everything currently assigned to a specific employee.

---

## 4. Key Workflows

### Creating an Asset
1. Admin clicks "Create Asset".
2. Admin selects a category (e.g., Laptop).
3. System fetches fields (RAM, CPU) via AJAX and adds them to the form.
4. Admin submits; data is saved in `assets` table with specific fields in `custom_fields_data`.

### Assigning an Asset
1. Admin clicks "Create Assignment".
2. Admin selects a Category, then an Asset from that category.
3. Admin selects an Employee and Date.
4. System:
   - Updates Asset status to "Assigned".
   - Creates an `AssetAssignment` record.
   - Closes any "open" assignments for that asset if they existed.

### Returning an Asset
1. Admin edits an active assignment.
2. Sets status to "Returned" and enters a Return Date.
3. System:
   - Updates Asset status to "Available".
   - Updates `AssetAssignment` record with the return date and status.

---

## 5. Asset Status Lifecycle

The system carefully synchronizes the status of the physical `Asset` with the status of its current `AssetAssignment`.

### Asset Table Statuses
- **`Available`**: The default state. The asset is in the office/inventory and can be assigned to anyone.
- **`Assigned`**: The asset is currently with an employee. It cannot be assigned to another person until it is checked back in.
- **`Lost`/`Damaged`/`Under Repair`**: Manual statuses that can be set in the "All Assets" view to prevent an item from being assigned while it's unavailable for use.

### Assignment Statuses
- **`assigned`**: An active link between an employee and an asset. This state triggers the "Assigned" status on the Asset itself.
- **`returned`**: The assignment is closed. This state triggers the "Available" status on the Asset itself, allowing it to be reused.

### Automatic Status Triggers
1. **On Assign**: Creating a new assignment records automatically switches the Asset status from `Available` to `Assigned`.
2. **On Reassign**: If an asset is assigned directly from one user to another, the system creates an `AssetAssignmentLog` to track the change and keeps the Asset status as `Assigned`.
3. **On Return**: Marking an assignment as `returned` (via the Edit modal or Check-In button) resets the Asset status to `Available`.
4. **On Deletion**: If an assignment record is deleted, the system safely reverts the Asset status to `Available` as a fallback.

---

## 6. Routes Summary
- `/asset-management`: Main dashboard.
- `/asset-management/fetch`: Data endpoint for assignments (list or user-grouped).
- `/asset-management/stats`: KPI data.
- `/assets`: Inventory management.
- `/assets/{id}/history`: Audit trail for a specific item.
- `/asset-category/{id}`: Metadata for dynamic fields.
