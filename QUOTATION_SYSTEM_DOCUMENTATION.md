# Quotation System Documentation

## Overview
The Quotation System is a robust module within the Workorio platform designed to manage the end-to-end lifecycle of sales quotations. It allows sales teams to create professional, branded PDF quotations, track multiple revisions, and manage client product interests dynamically.

---

## Technical Stack
- **Backend:** Laravel (PHP)
- **Frontend:** jQuery, Bootstrap 5, Blade Templates
- **PDF Generation:** [jsPDF](https://github.com/parallax/jsPDF) and [jsPDF-AutoTable](https://github.com/simonbengtsson/jspdf-autotable) (Client-side generation)
- **Database:** MySQL

---

## Core Workflow

### 1. Configuration (Setup)
Before generating quotes, administrators configure company identity via the **Quotation Setup** page.
- **Table:** `quotation_settings`
- **Fields:** Company Name, Address, Mission/Vision, Services (JSON), Logo, GSTIN, PAN, and Bank Details.
- **Usage:** These settings are injected into every generated PDF to ensure consistent branding.

### 2. Quotation Creation
- **Endpoint:** `/quotation/create`
- **Logic:**
    - The user selects a **Customer Type** (Existing Customer or New Prospect).
    - **Dynamic Rows:** Products are added dynamically. Each row fetches real-time pricing from the `sales_products` table.
    - **Calculations:** Discounts can be applied as a percentage (%) or flat amount (₹). Tax and Total calculations are performed in real-time.

### 3. PDF Generation (Client-Side)
Unlike traditional server-side rendering, this system generates PDFs in the browser:
- **Phase 1:** Collects form data and fetches company settings.
- **Phase 2:** Builds a multi-page document:
    - **Page 1:** "About Us" section using Mission/Vision settings.
    - **Page 2:** "Our Services" section listing predefined company services.
    - **Page 3+:** The specific Itemized Quote table.
- **Phase 3:** Converts the binary PDF to a Base64 string.

### 4. Storage & Processing
Once generated, the system performs a dual-save:
1. **File System:** The PDF is uploaded to `storage/app/public/quotations/`.
2. **Database:** Metadata (Customer ID, Total Amount, File Path) is saved in the `quotations` table.

### 5. Revision Management
If a quotation needs changes:
- Clicking **"Revise"** clones the previous data into a new form.
- The system increments the version number.
- Old versions are preserved in the `quotation_revisions` table, allowing users to view or download historical versions at any time.

---

## Database Schema Highlights

### `quotations` Table
| Column | Type | Description |
| --- | --- | --- |
| `quotation_number` | String | Unique Identifier (e.g., QT-2024-001) |
| `customer_type` | Enum | 'customer' or 'prospect' |
| `total_amount` | Decimal | Final value including discounts/taxes |
| `file_path` | String | Path to the stored PDF |
| `status` | String | Current state (New, Revised, etc.) |

### `quotation_revisions` Table
| Column | Type | Description |
| --- | --- | --- |
| `quotation_id` | Foreign Key | Reference to parent quotation |
| `version` | Integer | Version count (1, 2, 3...) |
| `file_url` | String | Path to that specific version's PDF |

---

## Key Components

### Controllers
- `QuotationController.php`: Manages listing, storage, and revision logic.
- `QuotationSetupController.php`: Manages company branding and service defaults.

### Views
- `quotation/index.blade.php`: The dashboard featuring summary metrics (Total Value, Total Projects) and the searchable quotation list.
- `quotation/create.blade.php`: The "Builder" interface containing the `jsPDF` logic.
- `quotation/setup.blade.php`: The branding configuration interface.

---

## How to Use

1. **Brand the System:** Navigate to `Quotation > Setup` and fill in your company details.
2. **Create a Quote:** Go to `Quotation > Add +`. Select your client, add products, and click "Save Quotation."
3. **Track History:** From the main list, click the **Clock Icon (History)** to see every revision sent to a client.
4. **Resend/Revise:** Use the **Arrow Icon (Revise)** to update an existing quote without losing the original.
