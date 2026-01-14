<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $authKey;
    protected $integratedNumber;
    protected $templateNamespace;
    protected $templateName;
    protected $apiUrl = 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/';

    public function __construct()
    {
        // Load from config or environment
        $this->authKey = env('MSG91_AUTH_KEY', 'YOUR_MSG91_AUTH_KEY');
        $this->integratedNumber = env('MSG91_WHATSAPP_NUMBER', '919451820817');
        $this->templateNamespace = env('MSG91_TEMPLATE_NAMESPACE', '78b03afb_64e5_429f_813d_68bad14fe1cd');
        $this->templateName = env('MSG91_INDIAMART_TEMPLATE', 'india_mart_leads_greeting');
    }

    /**
     * Send WhatsApp message to IndiaMART lead
     * 
     * @param array $leadData Lead data from IndiaMART
     * @return bool Success status
     */
    public function sendLeadGreeting($leadData)
    {
        // Skip if WhatsApp is not configured
        if ($this->authKey === 'YOUR_MSG91_AUTH_KEY') {
            Log::info('WhatsApp Service: Skipped - MSG91_AUTH_KEY not configured');
            return false;
        }

        // Get phone number - try sender_mobile first, then alternatives
        $phoneNumber = $leadData['sender_mobile'] 
            ?? $leadData['sender_mobile_alt'] 
            ?? $leadData['sender_other_mobile'] 
            ?? null;

        if (empty($phoneNumber)) {
            Log::info('WhatsApp Service: No phone number available for lead');
            return false;
        }

        // Format phone number
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);

        if (!$phoneNumber) {
            Log::warning('WhatsApp Service: Invalid phone number format');
            return false;
        }

        // Get sender name and company for template variables
        $senderName = $leadData['sender_name'] ?? 'Customer';
        $companyName = $leadData['sender_company'] ?? $leadData['company_name'] ?? 'your company';

        return $this->sendTemplateMessage($phoneNumber, $senderName, $companyName);
    }

    /**
     * Format phone number for MSG91 API
     * 
     * @param string $phoneNumber Raw phone number
     * @return string|null Formatted phone number or null if invalid
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Clean phone number (remove spaces, hyphens, etc.)
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (empty($phoneNumber)) {
            return null;
        }

        // Ensure phone number starts with country code
        if (substr($phoneNumber, 0, 1) !== '+') {
            if (substr($phoneNumber, 0, 2) !== '91') {
                $phoneNumber = '91' . ltrim($phoneNumber, '0');
            }
        }

        // Remove + if present for API
        $phoneNumber = ltrim($phoneNumber, '+');

        // Validate length (Indian numbers should be 12 digits with country code)
        if (strlen($phoneNumber) < 10 || strlen($phoneNumber) > 15) {
            return null;
        }

        return $phoneNumber;
    }

    /**
     * Send template message via MSG91 WhatsApp API
     * 
     * @param string $phoneNumber Formatted phone number
     * @param string $body1 First template variable (sender name)
     * @param string $body2 Second template variable (company name)
     * @return bool Success status
     */
    protected function sendTemplateMessage($phoneNumber, $body1, $body2)
    {
        $payload = [
            'integrated_number' => $this->integratedNumber,
            'content_type' => 'template',
            'payload' => [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                'template' => [
                    'name' => $this->templateName,
                    'language' => [
                        'code' => 'en',
                        'policy' => 'deterministic'
                    ],
                    'namespace' => $this->templateNamespace,
                    'to_and_components' => [
                        [
                            'to' => [$phoneNumber],
                            'components' => [
                                'body_1' => [
                                    'type' => 'text',
                                    'value' => $body1
                                ],
                                'body_2' => [
                                    'type' => 'text',
                                    'value' => $body2
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'authkey' => $this->authKey
                ])
                ->post($this->apiUrl, $payload);

            if ($response->successful()) {
                Log::info("WhatsApp Service: Message sent successfully to {$phoneNumber}");
                return true;
            } else {
                Log::warning("WhatsApp Service: Failed to send message. Status: {$response->status()}, Response: {$response->body()}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Service: Exception - " . $e->getMessage());
            return false;
        }
    }
}

