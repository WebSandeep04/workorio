# Leads Management Workflow & API Documentation

## 1. Executive Summary
This document provides a comprehensive analysis and technical reference for the "Prospect to Lead" workflow in the SaaS application. It is designed for both **Business Stakeholders** (to understand the "Why") and **Developers** (to understand the "How").

### Core Philosophy: "Entity vs. Opportunity"
The system utilizes a **Two-Stage Architecture**:
1.  **Prospect (The Entity):** Represents the static identity of a potential client (Company Name, Contact Person, Address). These details rarely change.
2.  **Lead (The Opportunity):** Represents a specific sales opportunity associated with that Prospect at a point in time. This includes dynamic data like Status, Next Follow-up, Remarks, and Interest Level.

**Business Benefit:** This separation allows a single Prospect (e.g., "ABC Corp") to have multiple Leads over time (e.g., "Jan 2024 Inquiry", "Aug 2024 Up-sell") without duplicating the core company data. It ensures data integrity and simplified reporting.

---

## 2. End-to-End Workflow Analysis

### Step 1: Prospect Creation (The Foundation)
**Logic:** Before a sales interaction can be tracked, the entity must exist in the system.
-   **Action:** User creates a Prospect.
-   **Data Captured:** Organization Name, Contact Person, Phone, Email, Location (City/State), Business Type.
-   **Why:** Capturing this first ensures we have a "Master Record" to link future activities to.

### Step 2: Initiation of a Lead (The Conversion)
**Logic:** A sales representative decides to pursue an opportunity with an existing Prospect.
-   **Action:** User selects "Add Lead" and searches for a Prospect.
-   **System Behavior:**
    -   FETCHES the Prospect's Master Data.
    -   AUTO-FILLS the Lead form (Name, Contact, Address).
    -   **UX Win:** Eliminates manual data entry, preventing typos and saving ~30-60 seconds per lead.

### Step 3: Transactional Data Entry
**Logic:** The user validates the auto-filled data and adds context specific to *this* interaction.
-   **Manual Fields:**
    -   **Status:** Current stage (e.g., New, Interested, Negotiation).
    -   **Next Follow-up:** Crucial for the "tickler file" system to remind sales reps.
    -   **Remarks:** Qualitative context (e.g., "Client asked for 10% discount").
    -   **Products:** What are they interested in?

### Step 4: Submission & Storage
**Logic:** The system links the new Lead to the immutable Prospect ID.
-   **Backend:** A new record is created in `sales_records` (Leads table) containing the transactional data and a foreign key `prospectus_id`.
-   **Data Flow:** `Frontend JSON` → `Validation Layer` → `SalesRecord Model` → `Database`.

---

## 3. Technical Architecture & Data Strategy

### Database Relationship
-   **Relationship Type:** One-to-Many (1:N)
-   **Prospect Model (`Prospectus`):** Parent Table. Holds `id`, `prospectus_name`, `contact_details`.
-   **Lead Model (`SalesRecord`):** Child Table. Holds `id`, `prospectus_id`, `status_id`, `remark`, `follow_up_date`.

### Data Consistency & Integrity
-   **Validation:** The Lead creation endpoint (`store`) strictly enforces the existence of `prospectus_id`. It allows "shadowing" of contact details (saving a snapshot of contact info at the time of the lead) while keeping the link to the master record.
-   **Duplicate Prevention:** By forcing Prospect selection first, we reduce the chance of having "ABC Corp", "ABC Corporation", and "A.B.C. Corp" as separate unconnected entities.

---

## 4. API Reference Implementation

### Base URL
`http://<your-domain>/api`

### Authentication
All endpoints require headers:
-   `Authorization`: `Bearer <ACCESS_TOKEN>`
-   `Accept`: `application/json`
-   `X-Tenant-ID`: `<TENANT_ID>`

### A. Prospect Management API

#### 1. Search & Fetch Prospects
Essential for the "Step 2" auto-fill functionality.
-   **Endpoint:** `GET /prospects`
-   **Parameters:** `search` (string), `per_page` (int)
-   **Usage:** Call this when the user types in the "Select Prospect" box.

#### 2. Create Prospect
-   **Endpoint:** `POST /prospects`
-   **Body:**
    ```json
    {
        "prospectus_name": "Acme Industries",
        "contact_person": "Jane Doe",
        "contact_number": "9876543210",
        "email": "jane@acme.com",
        "state_id": 5,
        "city_id": 23,
        "business_type_id": 2
    }
    ```
-   **Returns:** New Prospect Object (ID is critical for next step).

#### 3. Get Single Prospect
-   **Endpoint:** `GET /prospects/{id}`
-   **Usage:** detailed view to ensure specific fields are correct before Lead creation.

---

### B. Lead Management API

#### 1. Create Lead (The Conversion Event)
This is where the Prospect connects to the Sales Process.

-   **Endpoint:** `/leads/add`
-   **Method:** `POST`
-   **Critical Logic:** You **MUST** provide `prospectus_id`.
-   **Body:**
    ```json
    {
        "prospectus_id": 101,  // The ID from the Prospect API
        "status_id": 1,        // e.g., 'New'
        "next_follow_up_date": "2024-12-01",
        "remark": "Initial discussion positive. Send catalog.",
        "products_id": 5,      // Optional linkage to product
        // Optional Overrides (if contact changed for this specific deal)
        "leads_name": "Acme Industries",
        "contact_person": "John Smith" 
    }
    ```

#### 2. Fetch My Leads
-   **Endpoint:** `/leads/my-leads`
-   **Method:** `GET`
-   **Response:** Returns Leads *with* nested Prospect data.
    ```json
    {
        "data": [
            {
                "id": 505,
                "status_name": "Under Process",
                "prospectus": {
                    "id": 101,
                    "prospectus_name": "Acme Industries"
                }
            }
        ]
    }
    ```

#### 3. Assign Lead
Reassign ownership of a lead to another team member.
-   **Endpoint:** `/leads/assign`
-   **Body:** `{ "lead_id": 505, "new_user_id": 12 }`

#### 4. Filter Options (Helpers)
Fetch all dropdown data (Statuses, Sources, Business Types) in one call.
-   **Endpoint:** `/leads/filter-options`
-   **Method:** `GET`

---

### C. User Management API

### C. User Management API

#### 1. Fetch & Search Users
Retrieve a list of users, useful for population of dropdowns (e.g., "Assign To") or user directories.

-   **Endpoint:** `/users`
-   **Method:** `GET`
-   **Parameters:** 
    -   `search` (string, optional): Search term to filter by Name or Email.
-   **Usage:** Call this endpoint to list all users or search for specific team members.
-   **Response:**
    ```json
    {
        "success": true,
        "data": [
            {
                "id": 1,
                "name": "John Admin",
                "email": "admin@example.com"
            },
            {
                "id": 2,
                "name": "Sarah Sales",
                "email": "sarah@example.com"
            }
        ]
    }
    ```

---

## 5. Future Recommendations & Roadmap
1.  **Prospect Deduplication:** Implement fuzzy matching on `prospectus_name` during creation to warn users if a similar company exists.
2.  **Contact Management:** Abstract `ContactPerson` into its own table (Many-to-Many with Prospect) to handle large clients with multiple stakeholders.
3.  **Automated Enrichment:** Use an API to auto-fill address/industry data based on the Company Name.

---

## 6. Frontend Workflow Analysis (My Leads -> Remarks)

### A. Navigation Flow
1.  **Dashboard**: User views `/myleads`.
2.  **Action**: Clicking a lead/remark navigates to `/remark?sales_record_id={id}`.
    -   **Code Reference**: `myleads.blade.php` generates `<a href="/remark?sales_record_id=${record.id}">`.

### B. Remark Page (`/remark`) Analysis
This page (`resources/views/remark.blade.php`) handles the complete interaction lifecycle.

#### 1. Page Architecture
-   **Left Pane (Context)**:
    -   Displays Lead & Prospect details.
    -   **Edit Prospect**: Updates master data via `POST /updateprospectus`. **Note:** This is a global update affecting all leads for this prospect.
    -   **Latest Quote**: Integration to view/revise usage. Includes WhatsApp sharing logic via `sendQuoteToWhatsApp()`.
-   **Center Pane (Action Form)**:
    -   **Submission**: `POST /saveremark` (RemarkController@store).
    -   **Logic**: Uses `updateOrCreate` based on **[sales_record_id, remark_date]**.
    -   **Constraint**: Only **one remark per date** is allowed.
    -   **Side Effects**: Submitting a remark *always* updates the Lead's current Status, Ticket Value, and Follow-up Date to the values in the form.
-   **Right Pane (History)**:
    -   Lists historical remarks (Newest First).
    -   **Edit Feature**: Clicking "Edit" fills the form with the old remark's text and date.
    -   **Caveat**: Submitting an edited remark performs an Upsert on that date. It does not "move" the remark.

### C. Backend Logic (`RemarkController`)
-   **Store Method**:
    -   Validates `sales_record_id`.
    -   Updates parent `SalesRecord` (`ticket_value`, `status_id`, `next_follow_up_date`).
    -   Upserts `Remark` entry.

---

## 7. Lead Details & Remarks API
New endpoints to retrieve full lead details and manage remarks via REST API.

### 1. Get Single Lead
Retrieves full details including Prospect, Current Status, and Remarks.
- **Endpoint:** `/leads/{id}`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "success": true,
      "data": {
          "id": 5377,
          "leads_name": "Acme Corp",
          "ticket_value": "50000",
          "next_follow_up_date": "2024-02-15",
          "prospectus": { "prospectus_name": "Acme Inc", "email": "..." },
          "status": { "id": 1, "status_name": "Hot" },
          "remarks": [ 
              { "id": 101, "remark": "Meeting notes...", "remark_date": "2024-02-01" } 
          ]
      }
  }
  ```

### 2. Manage Remarks (Upsert)
Add or Update a remark. Uses **Upsert Logic** based on Date.
- **Endpoint:** `/remarks`
- **Method:** `POST`
- **Body:**
  ```json
  {
      "sales_record_id": 5377,
      "remark_date": "2024-02-01",
      "remark": "Meeting went well.",
      "ticket_value": 55000,
      "status_id": 2, // ID from filter-options
      "next_follow_up_date": "2024-02-20"
  }
  ```
- **Note:** Updates parent Lead's Status, Ticket Value, and Follow-Up Date automatically.

### 3. Update Prospect Details
Update the master prospect record.
- **Endpoint:** `/prospects/{id}`
- **Method:** `PUT`
- **Body:** 
  ```json
  { 
      "prospectus_name": "Acme Inc. Updated", 
      "email": "contact@acme.com",
      "contact_person": "John Doe",
      "address": "123 Business Rd"
  }
  ```

---

## 8. Dashboard & Filtering API
Endpoints to power the interactive dashboard (Summary Cards, Status Filters).

### 1. Get Summary Stats (Top Row)
Fetches the counts for "Today's Follow Ups", "Under Process", etc.
- **Endpoint:** `/leads/stats`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "success": true,
      "data": {
          "today_followups": 5,
          "under_process": 12,
          "today_completed": 3,
          "today_pending": 1,
          "today_new": 2
      }
  }
  ```
- **UX Action:** Clicking one of these cards (e.g., "Today's Follow Ups") should trigger the list API (below) with the corresponding `filter_type`.

### 2. Get Status Counts
Fetches the breakdown of leads by specific status (Cold, Warm, Hot, etc.).
- **Endpoint:** `/leads/status-counts`
- **Method:** `GET`
- **Response:**
  ```json
  {
      "success": true,
      "data": [
          { "id": 1, "status_name": "Cold", "count": 10 },
          { "id": 2, "status_name": "Warm", "count": 5 }
      ]
  }
  ```
- **UX Action:** Clicking a status card (e.g., "ID 1 Cold") should trigger the list API with `status_id=1`.

### 3. Filtered Lead List (New Parameter)
The main list endpoint has been enhanced to support "Preset Filters" for the dashboard.
- **Endpoint:** `/leads/my-leads`
- **New Parameter:** `filter_type` (string, optional)
- **Allowed Values:**
    -   `today_followups`: Leads due today or past due.
    -   `under_process`: Updated today + Follow up today.
    -   `today_completed`: Updated today + Follow up future.
    -   `today_pending`: Due today or null.
    -   `today_new`: Created today.
- **Example:** `GET /leads/my-leads?filter_type=today_followups`
