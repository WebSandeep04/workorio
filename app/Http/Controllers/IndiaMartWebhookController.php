<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantDatabaseService;
use App\Services\WhatsAppService;
use App\Notifications\IndiaMartLeadNotification;
use App\Mail\IndiaMartLeadNotification as IndiaMartLeadMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use DateTime;
use DateTimeZone;

class IndiaMartWebhookController extends Controller
{
    /**
     * Show IndiaMART Push API settings view under Software Setup > Sales Setup.
     */
    public function settingsView()
    {
        $tenantId = session('tenant_id') ?? (auth()->check() ? auth()->user()->tenant_id : 1);
        $tenant = Tenant::on('mysql')->find($tenantId);
        $tenantSlug = $tenant ? ($tenant->tenant_code ?: $tenant->id) : 1;
        $webhookUrl = url("/api/indiamart/webhook/{$tenantSlug}");

        return view('software-setup.indiamart-settings.index', compact('tenant', 'webhookUrl'));
    }

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
     * Handle IndiaMART Push API Webhook.
     *
     * URL: POST /api/indiamart/webhook/{tenant}
     */
    public function handle(Request $request, $tenantIdentifier)
    {
        // Support optional GET for health checks / ping
        if ($request->isMethod('GET')) {
            return response()->json(['success' => true, 'message' => 'IndiaMART Webhook Ready', 'tenant' => $tenantIdentifier]);
        }

        $tenant = $this->resolveTenant($tenantIdentifier);
        if (!$tenant) {
            Log::warning("IndiaMART Webhook: Unknown tenant [{$tenantIdentifier}]");
            return response()->json(['success' => false, 'message' => 'tenant_not_found'], 404);
        }

        // Switch to the target tenant's database
        TenantDatabaseService::setDefaultConnection($tenant->id);

        $raw = $request->getContent();
        Log::info("IndiaMART Webhook received for Tenant [{$tenant->id} - {$tenant->tenant_name}]:", [$raw]);

        $payload = json_decode($raw, true);
        if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
            $payload = $request->all();
        }

        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'empty_payload'], 400);
        }

        // IndiaMART pushes either a list or wrapped object
        $leads = [];
        if (is_array($payload)) {
            if (isset($payload['RESPONSE']) && is_array($payload['RESPONSE'])) {
                $leads = $payload['RESPONSE'];
            } elseif (isset($payload[0]) && is_array($payload[0])) {
                $leads = $payload;
            } elseif (isset($payload['UNIQUE_QUERY_ID']) || isset($payload['unique_query_id'])) {
                $leads = [$payload];
            } else {
                $leads = [$payload];
            }
        }

        if (empty($leads)) {
            return response()->json(['success' => false, 'message' => 'no_leads_found'], 400);
        }

        $saved = 0;
        $duplicates = 0;
        $errors = 0;

        foreach ($leads as $leadRaw) {
            $mapped = $this->mapLead($leadRaw);
            $queryId = $mapped['unique_query_id'] ?? null;

            if (empty($queryId)) {
                $errors++;
                continue;
            }

            // Check duplicate
            $exists = DB::table('indiamartleads')->where('unique_query_id', $queryId)->exists();
            if ($exists) {
                $duplicates++;
                continue;
            }

            try {
                DB::table('indiamartleads')->insert($mapped);
                $saved++;

                // Trigger tenant-scoped notifications & greetings
                $this->processPostLeadActions($mapped);
            } catch (\Exception $e) {
                Log::error("IndiaMART Webhook insert error: " . $e->getMessage(), ['lead' => $mapped]);
                $errors++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'leads_processed',
            'saved' => $saved,
            'duplicates' => $duplicates,
            'errors' => $errors,
        ]);
    }

    /**
     * Parse query time to standard UTC datetime format.
     */
    protected function parseQueryTime($value): ?string
    {
        if (empty($value)) return null;

        $formats = [
            'Y-m-d H:i:s',
            'd-M-Y H:i:s',
            'd-M-YH:i:s',
            'd-m-Y H:i:s',
            'Y/m/d H:i:s',
        ];

        foreach ($formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $value, new DateTimeZone('Asia/Kolkata'));
            if ($dt instanceof DateTime) {
                $dt->setTimezone(new DateTimeZone('UTC'));
                return $dt->format('Y-m-d H:i:s');
            }
        }
        return null;
    }

    /**
     * Map raw IndiaMART payload to indiamartleads database schema.
     */
    protected function mapLead(array $lead): array
    {
        $now = gmdate('Y-m-d H:i:s');

        return [
            'unique_query_id'          => $lead['UNIQUE_QUERY_ID'] ?? $lead['unique_query_id'] ?? null,
            'query_type'               => $lead['QUERY_TYPE'] ?? $lead['query_type'] ?? null,
            'query_time'               => $this->parseQueryTime($lead['QUERY_TIME'] ?? $lead['query_time'] ?? null),
            'query_product_name'       => $lead['QUERY_PRODUCT_NAME'] ?? $lead['query_product_name'] ?? ($lead['PRODUCT_NAME'] ?? $lead['product_name'] ?? null),
            'query_message'            => $lead['QUERY_MESSAGE'] ?? $lead['query_message'] ?? null,
            'query_mcat_name'          => $lead['QUERY_MCAT_NAME'] ?? $lead['query_mcat_name'] ?? null,
            'sender_name'              => $lead['SENDER_NAME'] ?? $lead['sender_name'] ?? null,
            'sender_mobile'            => $lead['SENDER_MOBILE'] ?? $lead['sender_mobile'] ?? null,
            'sender_mobile_alt'        => $lead['SENDER_MOBILE_ALT'] ?? $lead['sender_mobile_alt'] ?? null,
            'sender_email'             => $lead['SENDER_EMAIL'] ?? $lead['sender_email'] ?? null,
            'sender_email_alt'         => $lead['SENDER_EMAIL_ALT'] ?? $lead['sender_email_alt'] ?? null,
            'sender_company'           => $lead['SENDER_COMPANY'] ?? $lead['sender_company'] ?? null,
            'sender_address'           => $lead['SENDER_ADDRESS'] ?? $lead['sender_address'] ?? null,
            'sender_city'              => $lead['SENDER_CITY'] ?? $lead['sender_city'] ?? null,
            'sender_state'             => $lead['SENDER_STATE'] ?? $lead['sender_state'] ?? null,
            'sender_pincode'           => $lead['SENDER_PINCODE'] ?? $lead['sender_pincode'] ?? null,
            'sender_country_iso'       => $lead['SENDER_COUNTRY_ISO'] ?? $lead['sender_country_iso'] ?? null,
            'sender_other_mobile'      => $lead['SENDER_OTHER_MOBILE'] ?? $lead['sender_other_mobile'] ?? null,
            'sender_phone'             => $lead['SENDER_PHONE'] ?? $lead['sender_phone'] ?? null,
            'sender_phone_alt'         => $lead['SENDER_PHONE_ALT'] ?? $lead['sender_phone_alt'] ?? null,
            'receiver_mobile'          => $lead['RECEIVER_MOBILE'] ?? $lead['receiver_mobile'] ?? null,
            'call_duration'            => $lead['CALL_DURATION'] ?? $lead['call_duration'] ?? null,
            'subject'                  => $lead['SUBJECT'] ?? $lead['subject'] ?? null,
            'company_name'             => $lead['SENDER_COMPANY'] ?? ($lead['COMPANY_NAME'] ?? $lead['company_name'] ?? null),
            'product_name'             => $lead['QUERY_PRODUCT_NAME'] ?? ($lead['PRODUCT_NAME'] ?? $lead['product_name'] ?? null),
            'district'                 => $lead['DISTRICT'] ?? $lead['district'] ?? null,
            'org_sender_glusr_usr_id'  => $lead['ORG_SENDER_GLUSR_USR_ID'] ?? $lead['org_sender_glusr_usr_id'] ?? null,
            'enrichment_id'            => $lead['ENRICHMENT_ID'] ?? $lead['enrichment_id'] ?? null,
            'im_member_since'          => $lead['IM_MEMBER_SINCE'] ?? $lead['im_member_since'] ?? null,
            'enq_id'                   => $lead['ENQ_ID'] ?? $lead['enq_id'] ?? null,
            'enq_receiver_name'        => $lead['ENQ_RECEIVER_NAME'] ?? $lead['enq_receiver_name'] ?? null,
            'enq_receiver_email'       => $lead['ENQ_RECEIVER_EMAIL'] ?? $lead['enq_receiver_email'] ?? null,
            'enq_receiver_mobile'      => $lead['ENQ_RECEIVER_MOBILE'] ?? $lead['enq_receiver_mobile'] ?? null,
            'status'                   => 'new',
            'is_processed'             => '0',
            'raw_data'                 => json_encode($lead, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'fetched_at'               => $now,
            'notes'                    => null,
            'created_at'               => $now,
            'updated_at'               => $now,
        ];
    }

    /**
     * Send in-app notification, email to sales users, and WhatsApp greeting to lead.
     */
    protected function processPostLeadActions(array $leadData): void
    {
        // 1. Send in-app and email notifications to sales users
        try {
            $salesUsers = User::whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->where('is_sales', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            foreach ($salesUsers as $user) {
                try {
                    // In-app notification
                    $user->notify(new IndiaMartLeadNotification($leadData));

                    // Email notification
                    if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        Mail::to($user->email)->send(new IndiaMartLeadMail($leadData));
                    }
                } catch (\Exception $e) {
                    Log::error("IndiaMART Webhook: User notify error [{$user->email}]: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("IndiaMART Webhook: Failed fetching sales users: " . $e->getMessage());
        }

        // 2. Send tenant-isolated WhatsApp greeting to the customer
        try {
            $whatsappService = new WhatsAppService();
            $whatsappService->sendLeadGreeting($leadData);
        } catch (\Exception $e) {
            Log::error("IndiaMART Webhook: WhatsApp greeting error: " . $e->getMessage());
        }
    }
}
