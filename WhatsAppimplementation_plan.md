# MSG91 WhatsApp Campaign Sending Feature

This plan outlines the implementation of the WhatsApp Campaign sending functionality using the MSG91 API. The feature will allow users to select an approved WhatsApp template and send it to all members of a specific campaign.

> [!NOTE]
> **Resolved Requirements:**
> 1. We will create a new `Msg91Setting` database table/model within the tenant's database to store API credentials (`auth_key`, `whatsapp_number`, `namespace`). This allows each tenant to have their own separate MSG91 API integration instead of relying on a global `.env` file.
> 2. We will fetch approved templates directly from the MSG91 API (`https://control.msg91.com/api/v5/whatsapp/get-template-client/:number`) to populate the selection dropdown in real-time. This eliminates the need to manually sync them to the local database.
> 3. Dynamic variables (e.g., `{{name}}`) inside templates will be supported. When formulating the API request, we will automatically map variables (like `name` and `phone_number`) from the `WhatsappCampaignMember` records to the template parameters payload.

## Proposed Changes

---

### Database & Models

#### [NEW] [create_msg91_settings_table.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/database/migrations)
- Create a migration for a `msg91_settings` table to store `auth_key`, `whatsapp_number`, and `whatsapp_namespace` for each tenant.

#### [NEW] [Msg91Setting.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/app/Models/Msg91Setting.php)
- Create the model for the new settings table.

---

### UI & Frontend (Campaign Index View)

#### [MODIFY] [whatsapp_campaigns/index.blade.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/resources/views/whatsapp_campaigns/index.blade.php)
- Add a "Send" button (perhaps a paper plane icon) in the Action column next to View/Edit/Delete.
- Add a Bootstrap Modal to the page:
  - Contains a dropdown to select a template (populated dynamically from the MSG91 API).
  - A "Send Now" button.
- Add JavaScript (AJAX) to:
  - Fetch available approved templates from our backend when the modal opens.
  - Submit the selected template details and campaign ID to the backend for sending.

#### [MODIFY] [whatsapp_campaigns/show.blade.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/resources/views/whatsapp_campaigns/show.blade.php)
- We will also add a "Send Campaign" button on the campaign details page for easy access, reusing the same modal logic.

---

### Backend (Controllers & Routes)

#### [MODIFY] [web.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/routes/web.php)
- Add a new POST route: `Route::post('/whatsapp-campaigns/{id}/send', [WhatsappCampaignController::class, 'sendCampaign']);`

#### [MODIFY] [WhatsappCampaignController.php](file:///d:/DontDelete/laravel/leadmanagement%20(akrati%20ui%20work)/app/Http/Controllers/WhatsappCampaignController.php)
- Add a `fetchMsg91Templates()` method to proxy the MSG91 Get Templates API (`https://control.msg91.com/api/v5/whatsapp/get-template-client/{number}`) and return only `approved` templates to the frontend.
- Add a `sendCampaign(Request $request, $id)` method.
- **Logic:**
  1. Validate that `template_name` and `template_language` are provided.
  2. Retrieve the Campaign and all its `members` where `status` is not already sent (or just all members).
  3. Format the payload for the MSG91 WhatsApp API (`https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/`).
  4. Include dynamic template variables in the `components` section of the payload, dynamically matching template parameters with `WhatsappCampaignMember` data.
  5. Fetch the tenant's MSG91 configuration from the `Msg91Setting` model. If not configured, return an error asking them to setup their integration.
  6. Use Laravel's `Http` facade to dispatch the request to MSG91 using the credentials from the database (`auth_key`, `whatsapp_number`, `whatsapp_namespace`).
  7. On successful API response, update the Campaign status to `Completed` and update the `status` of all involved `WhatsappCampaignMember` records to `Sent`.
  8. Return a success response to the frontend.

## Verification Plan

### Automated Tests
- No automated tests required for this UI addition, but we will ensure the HTTP request is formatted correctly according to MSG91 specs.

### Manual Verification
1. Open the WhatsApp Campaigns list.
2. Click the new "Send" button on a draft campaign.
3. Verify the modal opens and lists available templates.
4. Select a template and click "Send Now".
5. Verify that the AJAX call succeeds, the campaign status updates to "Completed", and the members are marked as "Sent".
6. (Requires valid MSG91 credentials to fully test message delivery).
