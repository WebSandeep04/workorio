# WhatsApp Inbox Reply Feature

This plan outlines the steps to add a "Reply" feature to the WhatsApp Inbox, allowing you to respond to incoming messages directly from the application within the 24-hour session window using the MSG91 API.

## Proposed Changes

### 1. Backend: Routes & Controller
We will add a new endpoint to handle the reply logic.

#### [MODIFY] [web.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/routes/web.php)
- Add a new `POST` route: `Route::post('whatsapp-inbox/reply', [WhatsappInboxController::class, 'reply'])->name('whatsapp-inbox.reply');`

#### [MODIFY] [WhatsappInboxController.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/app/Http/Controllers/WhatsappInboxController.php)
- Add a `reply(Request $request)` method.
- This method will fetch the MSG91 credentials from `Msg91Setting::first()`.
- It will send a `POST` request to the MSG91 `whatsapp-outbound-message/` endpoint with the payload containing the text message.
- Upon successful sending, it will log the sent message into the `WhatsappInbox` database table (so it shows up in the chat history) with `sender_number` set to "You" or your WhatsApp number, and `received_at` set to the current time.

### 2. Frontend: Inbox UI
We will add the UI elements required to send a reply.

#### [MODIFY] [whatsapp_inbox/index.blade.php](file:///d:/DontDelete/laravel/leadmanagement (akrati ui work)/resources/views/whatsapp_inbox/index.blade.php)
- **Chat History Modal**: Add a text input field and a "Send Reply" button at the bottom of the existing Chat History modal.
- **AJAX Logic**: Add a JavaScript function to capture the typed message, send it to the new `whatsapp-inbox.reply` route, and dynamically append the sent message bubble to the chat history.
- **UI Tweaks**: Style the chat bubbles so that incoming messages appear on the left (gray/white) and your replies appear on the right (green/blue), similar to WhatsApp.

## Decisions Made

> [!NOTE]
> **Saving Outbound Messages:** When you reply, the system will manually save your reply into the `whatsapp_inbox` table with `message_type = 'reply'` so it shows up seamlessly in the chat history.

> [!IMPORTANT]
> **24-Hour Restriction & UI Hide:** The MSG91 API requires replies to be sent within 24 hours of the last customer message. To prevent failures, the "Reply" UI will be automatically **hidden** if the last received message is older than 23 hours and 30 minutes (providing a 30-minute safety buffer before the 24-hour limit expires).

## Verification Plan

### Manual Verification
1. Open the WhatsApp Inbox and click "View" on a recent message (received within 23.5 hours).
2. Type a message in the new reply input field and click "Send".
3. Verify that the message bubble appears on the right side of the chat history.
4. Verify on the actual WhatsApp device that the customer received the message.
5. Click "View" on an older message (> 23.5 hours) and verify the reply option is completely hidden.
