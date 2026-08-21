# Make Uploads Available by Default

We need to simplify the cloth uploading process so that sellers do not have to manage availability dates right away. By default, newly listed clothes will be considered "Always Available".

## User Review Required

Please review the proposed changes below. Once approved, I will implement them.

## Open Questions

> [!IMPORTANT]
> Since we are removing the ability to add blocked dates *during* upload, we need a place for sellers to manage their blocked dates *after* upload. Do you already have a "Manage Listings" or "Edit Cloth" page where sellers can block dates, or should I create a new interface for this later?

## Proposed Changes

### `resources/views/sell.blade.php`
- **[MODIFY]** Remove the entire `<div class="availability-section border-top pt-3">` section from the upload form. This will make the form cleaner and shorter.

### `public/js/sell.js`
- **[MODIFY]** Remove the JavaScript functions (`addAvailabilityBlock`, `removeAvailabilityBlock`, date change handlers) that manage the dynamic availability blocks on the upload page.
- **[MODIFY]** Remove the validation check for availability blocks (`availability-error`).

### `app/Http/Controllers/ClothController.php`
- **[MODIFY]** In the `store` method, remove the validation rules for `availability_blocks`.
- **[MODIFY]** Remove the logic that attempts to save `availabilityBlocks()` to the database upon initial creation. (The `is_available` flag is already set to `true` by default when a cloth is created).

## Verification Plan

### Manual Verification
- Go to the `localhost:8000/sell` page and ensure the "Availability Management" section is no longer visible.
- Upload a new outfit and verify that it saves successfully without requiring availability dates.
- Check the database to confirm the cloth is saved and has `is_available` set to `1` (true) by default.
