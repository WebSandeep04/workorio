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
