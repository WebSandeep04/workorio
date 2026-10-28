# WhatsApp Template & Multi-Document Integration Architecture & Plan

## 1. Executive Summary
This document outlines the architecture, tenant-isolation mechanisms, and the phased implementation plan to enhance WhatsApp messaging within the Lead Management system. Specifically, it addresses:
1. **Tenant-wise storage and database isolation** across all tenants.
2. Extending `/whatsapp-template` management to associate **multiple documents** with a template.
3. Upgrading the **"Send WhatsApp"** modal on the Calling Remarks page (`/calling/{id}/remarks`) to present a pre-written message text along with a **multi-select document checklist** with live preview and delivery.

---

## 2. In-Depth Tenant Isolation Breakdown

The application uses a **hybrid multi-tenant model**: separate database schemas per tenant combined with folder-isolated file storage on the public disk.

```
                           Incoming Request
                                  │
                                  ▼
               ┌──────────────────────────────────────┐
               │ SetTenantDatabase Middleware         │
               │ - Detects tenant_id from Session/API │
               └──────────────────┬───────────────────┘
                                  │
                  ┌───────────────┴───────────────┐
                  ▼                               ▼
     ┌────────────────────────┐      ┌────────────────────────┐
     │ Dynamic DB Connection  │      │ TenantAwareStorage     │
     │ - Switches default DB  │      │ - Isolates directories │
     │   to `tenant_{id}`     │      │   `tenants/{tenant_id}`│
     └────────────────────────┘      └────────────────────────┘
```

### A. Database Isolation
- **Mechanism**: `App\Http\Middleware\SetTenantDatabase`
- **Session/Header Check**: Identifies the tenant via `session('tenant_id')` or `X-Tenant-ID` HTTP header.
- **Switching Logic**: Uses `TenantDatabaseService::setDefaultConnection($tenant->id)` to change Laravel's default connection to the tenant-specific MySQL database.
- **Impact**: All tenant-scoped Eloquent models—including `WhatsappTemplate`, `Document`, and `Calling`—query strictly within that tenant's database schema.

### B. File Storage Isolation
- **Mechanism**: `App\Traits\TenantAwareStorage`
- **Base Root**: `storage/app/public/` (accessible via public URL symlink `public/storage`).
- **Isolation Formula**:
  ```php
  protected function getTenantPath(string $path, $tenantId = null): string
  {
      $tenantId = $tenantId ?? session('tenant_id', 1);
      return "tenants/{$tenantId}/" . ltrim($path, '/');
  }
  ```
- **File Uploads**: Files uploaded via `DocumentController::store` are placed into `storage/app/public/tenants/{tenant_id}/documents/{timestamp}_{random}_{slug}.ext`.
- **Public URL Resolution**: Handled via `Storage::url($document->file_path)`, producing `https://{domain}/storage/tenants/{tenant_id}/documents/...`.

---

## 3. Current State Analysis

### Current WhatsApp Template (`/whatsapp-template`)
- **Controller**: `App\Http\Controllers\WhatsappTemplateController`
- **Table**: `whatsapp_templates` with fields: `id`, `name`, `text`, `created_at`, `updated_at`.
- **Current Limitation**: Only stores title and text string; no document linking capability.

### Current "Send WhatsApp" Flow (`/calling/{id}/remarks`)
- **View**: `resources/views/calling/remarks.blade.php`
- **Current Behavior**:
  1. User changes lead status to "Sent Details".
  2. The "Send WhatsApp" button reveals `#whatsappTemplateModal`.
  3. Clicking a template title immediately executes `window.open('https://wa.me/{phone}?text={templateText}')`.
  4. There is no checklist of documents, no preview, and no multi-document attachment capability.

---

## 4. Proposed Solution & Architecture

```
┌────────────────────────────────────────────────────────────────────────┐
│                        User clicks "Send WhatsApp"                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                      Enhanced WhatsApp Modal                           │
│ ┌────────────────────────────────────────────────────────────────────┐ │
│ │ 1. Choose Template: [ Welcome Kit / Brochure ▼ ]                  │ │
│ ├────────────────────────────────────────────────────────────────────┤ │
│ │ 2. Pre-written Text (Live Editable):                               │ │
│ │    "Hello John, here are the requested documents for our services:"│ │
│ ├────────────────────────────────────────────────────────────────────┤ │
│ │ 3. Multi-Select Documents (Checkbox list from tenant storage):     │ │
│ │    [x] Company Brochure (PDF)                                      │ │
│ │    [x] Pricing Sheet 2026 (PDF)                                    │ │
│ │    [ ] Case Study (DOCX)                                           │ │
│ ├────────────────────────────────────────────────────────────────────┤ │
│ │ 4. Final Message Preview with Generated Links:                     │ │
│ │    "Hello John...                                                  │ │
│ │     📎 Documents:                                                  │ │
│ │     • Company Brochure: https://.../tenants/1/documents/brochure.pdf│ │
│ │     • Pricing Sheet: https://.../tenants/1/documents/pricing.pdf"  │ │
│ └────────────────────────────────────────────────────────────────────┘ │
│                [ Copy Message ]       [ Send via WhatsApp 🚀 ]         │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Step-by-Step Implementation Plan

### Phase 1: Database Migration & Model Layer (Tenant DBs)
1. **Migration for Tenant Schema**:
   Create a migration targeting tenant database connections for the pivot table:
   ```php
   Schema::create('whatsapp_template_documents', function (Blueprint $table) {
       $table->id();
       $table->foreignId('whatsapp_template_id')->constrained('whatsapp_templates')->onDelete('cascade');
       $table->foreignId('document_id')->constrained('documents')->onDelete('cascade');
       $table->timestamps();
   });
   ```
2. **Model Association**:
   - In `App\Models\WhatsappTemplate`:
     ```php
     public function documents(): BelongsToMany
     {
         return $this->belongsToMany(Document::class, 'whatsapp_template_documents');
     }
     ```
   - In `App\Models\Document`:
     ```php
     public function whatsappTemplates(): BelongsToMany
     {
         return $this->belongsToMany(WhatsappTemplate::class, 'whatsapp_template_documents');
     }
     ```

### Phase 2: WhatsApp Template Management Upgrade (`/whatsapp-template`)
1. **Controller Updates** (`WhatsappTemplateController.php`):
   - Update `fetch()` to load associated documents (`with('documents')`).
   - Update `store()` and `update()` to accept `document_ids` array and sync relations:
     ```php
     $template->documents()->sync($request->input('document_ids', []));
     ```
   - Provide an endpoint or pass available tenant documents to the view for modal selection.
2. **UI Updates** (`resources/views/setup/whatsapp-template.blade.php`):
   - In the "Add/Edit Template" modal, add a document multi-select picker (with search and select-all).
   - In the data table, display badges/counts of linked documents for each template.

### Phase 3: "Send WhatsApp" Modal on Calling Remarks Page
1. **Controller Query** (`CallingController.php`):
   - Fetch templates with their linked documents:
     ```php
     $whatsappTemplates = WhatsappTemplate::with('documents')->orderBy('name')->get();
     $allDocuments = Document::active()->orderBy('title')->get();
     ```
   - Pass `$allDocuments` and `$whatsappTemplates` to `calling.remarks`.
2. **Modal Redesign** (`remarks.blade.php`):
   - Upgrade `#whatsappTemplateModal` to a comprehensive composer dialog:
     - **Template Selector**: Selecting a template populates the message textarea and automatically checks all documents default-associated with that template.
     - **Editable Message Box**: Allows the user to customize the note before sending.
     - **Document Checklist**: Checkbox list showing title, file extension (PDF, DOCX, etc.), and file size.
     - **Live Message Preview Box**: Displays the text combined with formatted document links in real time.
3. **Dispatch Logic**:
   - Construct URLs using the tenant-isolated public path (`Storage::disk('public')->url($doc->file_path)`).
   - Launch `https://wa.me/{phone}?text={encodedCombinedText}`.
   - Automatically log the dispatched remark into the interaction history.

---

## 6. Safety & Backward Compatibility Checklist
- **Zero Code Disturbance during planning**: No operational code changed during analysis.
- **Tenant Separation**: Document links and templates will strictly remain scoped to the active tenant DB and storage path `tenants/{tenant_id}/`.
- **Fallback Handling**: If a template has no documents linked, it will send standard plain text just like the current implementation.
