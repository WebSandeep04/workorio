# Enable Incoming WhatsApp Replies via MSG91 Webhook

Yes, it is entirely possible to receive and view replies from your customers. MSG91 provides a Webhook feature that sends real-time HTTP POST requests to your server whenever a user replies to your WhatsApp number.

To implement this in your CRM, we need to set up a few things:

## 1. Database Storage
We will create a new database table called `whatsapp_inbox` (or similar) to store incoming messages.
- Fields: `sender_number`, `receiver_number`, `message_text`, `media_url`, `message_type` (text, image, document), `received_at`, and an `is_read` flag.

## 2. Webhook API Endpoint
We will create a public API route in Laravel (e.g., `POST /api/msg91/whatsapp-webhook`).
- This route will not have CSRF or Auth middleware, as MSG91 needs to access it publicly.
- A new controller (`WhatsappWebhookController`) will catch the MSG91 payload, extract the sender's phone number and message content, and save it to the database.

## 3. User Interface (UI)
We need a place in the CRM to view these replies. I propose creating a **WhatsApp Inbox** page.
- A page listing all incoming messages, grouped by phone number or displayed chronologically.
- A notification badge to show unread incoming messages (optional).

## 4. MSG91 Dashboard Configuration (Manual Step)
Once the code is deployed, you will need to log in to your MSG91 dashboard and paste your Webhook URL into the WhatsApp settings.

> [!IMPORTANT]
> **Local Testing Note:** Since you are running the project on `localhost:8000`, MSG91 cannot send webhook requests to it directly over the internet. To test this locally, we will need to use a tool like **ngrok** to create a public URL for your localhost, or wait until this is deployed to a live server.

---

## User Review Required
Does this plan sound good? Should I proceed with writing the database migration, webhook controller, and the basic Inbox UI?
