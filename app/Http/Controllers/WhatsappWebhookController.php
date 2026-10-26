<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\WhatsappInbox;
use App\Models\WhatsappCampaign;
use App\Models\WhatsappCampaignMember;
use App\Models\Customer;
use App\Models\SalesRecord;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsappWebhookController extends Controller
{
    /**
     * Resolve the tenant context from route identifier (ID or tenant_code).
     */
    protected function resolveTenant($tenantIdentifier): ?Tenant
    {
        if (is_numeric($tenantIdentifier)) {
            return Tenant::on('mysql')->find($tenantIdentifier);
        }
        return Tenant::on('mysql')->where('tenant_code', $tenantIdentifier)->first();
    }

    /**
     * Switch Laravel default database connection to the given tenant.
     */
    protected function switchTenantConnection(Tenant $tenant): void
    {
        TenantDatabaseService::setDefaultConnection($tenant->id);
    }

    /**
     * Handle MSG91 Inbound Webhook (Customer sent a message).
     *
     * URL: POST /api/msg91/webhook/{tenant}/inbound
     */
    public function handleInbound(Request $request, $tenantIdentifier)
    {
        $tenant = $this->resolveTenant($tenantIdentifier);
        if (!$tenant) {
            Log::warning("MSG91 Inbound Webhook: Unknown tenant [{$tenantIdentifier}]");
            return response()->json(['status' => 'ignored', 'message' => 'tenant_not_found'], 404);
        }

        $this->switchTenantConnection($tenant);

        $payload = $request->all();
        Log::info("MSG91 Inbound Webhook received for Tenant [{$tenant->id} - {$tenant->tenant_name}]:", $payload);

        // Extract sender and receiver
        $sender = $payload['customerNumber'] ?? $payload['sender'] ?? $payload['from'] ?? null;
        $receiver = $payload['integratedNumber'] ?? $payload['receiver'] ?? $payload['to'] ?? null;

        $messageText = $payload['text'] ?? null;
        $messageArray = $payload['message'] ?? null;

        // Parse nested messages if present
        if (isset($payload['messages']) && is_string($payload['messages'])) {
            $decoded = json_decode($payload['messages'], true);
            if (is_array($decoded) && !empty($decoded)) {
                $messageArray = $decoded[0];
            }
        }

        // If structure is nested inside 'data'
        if (!$sender && isset($payload['data']) && is_array($payload['data'])) {
            $data = $payload['data'];
            $sender = $data['customerNumber'] ?? $data['sender'] ?? null;
            $receiver = $data['integratedNumber'] ?? $data['receiver'] ?? null;
            $messageText = $data['text'] ?? null;
            $messageArray = $data['message'] ?? null;
        }

        if (!$sender) {
            return response()->json(['status' => 'ignored', 'message' => 'missing_sender']);
        }

        // Handle MSG91 test run
        if ($sender === '{{customerNumber}}' || $sender === 'customerNumber') {
            return response()->json(['status' => 'success', 'message' => 'webhook_test_successful']);
        }

        $messageType = $payload['contentType'] ?? $payload['messageType'] ?? 'text';
        $mediaUrl = $payload['url'] ?? null;

        // Parse complex message payloads
        if (!$messageText && is_array($messageArray)) {
            $msgType = $messageArray['type'] ?? 'text';
            if ($msgType === 'text') {
                $messageText = $messageArray['text']['body'] ?? ($messageArray['text'] ?? '');
            } elseif (in_array($msgType, ['image', 'document', 'audio', 'video'])) {
                $mediaUrl = $messageArray['media_url'] ?? ($messageArray[$msgType]['link'] ?? null);
                $messageText = $messageArray['caption'] ?? null;
            }
            $messageType = $msgType;
        }

        // Lookup sender contact name across Customer, Sales Record, or Campaign Members
        $senderName = null;
        $numWithout91 = preg_replace('/^91/', '', $sender);

        // 1. Check Customers
        $customer = Customer::where('phone', $sender)->orWhere('phone', $numWithout91)->first();
        if ($customer) {
            $senderName = $customer->name;
        }

        // 2. Check Sales Records
        if (!$senderName) {
            $lead = SalesRecord::where('contact_number', $sender)->orWhere('contact_number', $numWithout91)->first();
            if ($lead) {
                $senderName = $lead->leads_name ?: $lead->contact_person;
            }
        }

        // 3. Check Campaign Members
        if (!$senderName) {
            $member = WhatsappCampaignMember::where('phone_number', $sender)->orWhere('phone_number', $numWithout91)->first();
            if ($member) {
                $senderName = $member->name;
            }
        }

        // Save incoming message in tenant's inbox
        $inboxMessage = WhatsappInbox::create([
            'sender_number' => $sender,
            'sender_name' => $senderName,
            'receiver_number' => $receiver,
            'message_text' => is_string($messageText) ? $messageText : json_encode($messageText),
            'media_url' => $mediaUrl,
            'message_type' => $messageType,
            'msg91_message_id' => $payload['uuid'] ?? $payload['message_id'] ?? $payload['id'] ?? null,
            'is_read' => false,
            'received_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'message_saved',
            'inbox_id' => $inboxMessage->id
        ]);
    }

    /**
     * Handle MSG91 Outbound Webhook (Delivery reports: Sent, Delivered, Read, Failed).
     *
     * URL: POST /api/msg91/webhook/{tenant}/outbound
     */
    public function handleOutbound(Request $request, $tenantIdentifier)
    {
        $tenant = $this->resolveTenant($tenantIdentifier);
        if (!$tenant) {
            Log::warning("MSG91 Outbound Webhook: Unknown tenant [{$tenantIdentifier}]");
            return response()->json(['status' => 'ignored', 'message' => 'tenant_not_found'], 404);
        }

        $this->switchTenantConnection($tenant);

        $payload = $request->all();
        Log::info("MSG91 Outbound Webhook received for Tenant [{$tenant->id} - {$tenant->tenant_name}]:", $payload);

        $requestId = $payload['requestId'] ?? $payload['request_id'] ?? null;
        $customerNumber = $payload['customerNumber'] ?? $payload['mobile'] ?? $payload['recipient'] ?? null;
        $status = $payload['status'] ?? $payload['eventName'] ?? null;
        $failureReason = $payload['failureReason'] ?? $payload['reason'] ?? null;

        // Nested data check
        if (!$requestId && isset($payload['data']) && is_array($payload['data'])) {
            $data = $payload['data'];
            $requestId = $data['requestId'] ?? $data['request_id'] ?? null;
            $customerNumber = $data['customerNumber'] ?? $data['mobile'] ?? null;
            $status = $data['status'] ?? $data['eventName'] ?? null;
            $failureReason = $data['failureReason'] ?? $data['reason'] ?? null;
        }

        if (!$requestId || !$customerNumber) {
            return response()->json(['status' => 'ignored', 'message' => 'missing_request_id_or_customer_number']);
        }

        // Test run check
        if ($requestId === '{{requestId}}' || $customerNumber === '{{customerNumber}}') {
            return response()->json(['status' => 'success', 'message' => 'webhook_test_successful']);
        }

        $standardStatus = $status ? ucfirst(strtolower($status)) : 'Unknown';
        $numWithout91 = preg_replace('/^91/', '', $customerNumber);

        // Find campaign by request_id
        $campaign = WhatsappCampaign::where('request_id', $requestId)->first();
        if ($campaign) {
            $updated = WhatsappCampaignMember::where('whatsapp_campaign_id', $campaign->id)
                ->where(function ($query) use ($customerNumber, $numWithout91) {
                    $query->where('phone_number', $customerNumber)
                          ->orWhere('phone_number', 'like', "%{$numWithout91}");
                })
                ->update([
                    'status' => $standardStatus,
                    'error_message' => $failureReason,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'member_status_updated',
                'updated_count' => $updated
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'campaign_not_found_but_webhook_is_working'
        ]);
    }
}
