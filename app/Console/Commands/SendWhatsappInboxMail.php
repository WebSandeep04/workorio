<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Models\WhatsappInbox;
use App\Mail\WhatsappInboxMailReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendWhatsappInboxMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-inbox-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a daily summary of the WhatsApp Inbox (All section) to Admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting WhatsApp Inbox Mail generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('WhatsApp Inbox email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Send to users who have admin role (role_id = 1)
            $recipientUsers = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })->where('role_id', 1)->get();

            $validRecipients = [];
            foreach ($recipientUsers as $user) {
                if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $validRecipients[] = [
                        'email' => $user->email,
                        'name' => $user->name
                    ];
                }
            }

            if (empty($validRecipients)) {
                $this->info("  ⚠️ No active valid admin recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            // Fetch messages like WhatsappInboxController::fetch (fetching a reasonable amount to group)
            $messages = WhatsappInbox::orderBy('received_at', 'desc')->limit(500)->get();

            if ($messages->isEmpty()) {
                $this->info("  ℹ️ No messages found for {$tenant->tenant_name}.");
                return;
            }

            // Group logic just like the frontend JS
            $groupedMessages = [];

            foreach ($messages as $msg) {
                $isReply = in_array($msg->message_type, ['reply', 'image_reply', 'document_reply']);
                $contactNumber = $isReply ? $msg->receiver_number : $msg->sender_number;
                
                if (!$contactNumber) continue;

                if (!isset($groupedMessages[$contactNumber])) {
                    $groupedMessages[$contactNumber] = $msg->toArray();
                    $groupedMessages[$contactNumber]['contact_number'] = $contactNumber;
                    $groupedMessages[$contactNumber]['unread_count'] = 0;
                    
                    if (!$isReply && $msg->is_read == 0) {
                        $groupedMessages[$contactNumber]['unread_count'] = 1;
                    }
                } else {
                    if (!$isReply && $msg->is_read == 0) {
                        $groupedMessages[$contactNumber]['unread_count']++;
                    }
                    
                    $currentName = $groupedMessages[$contactNumber]['sender_name'];
                    $newName = $msg->sender_name;
                    $bestName = ($newName && !$isReply) ? $newName : ($currentName ? $currentName : $newName);
                    $isLead = $groupedMessages[$contactNumber]['is_lead'] || $msg->is_lead;

                    $existingDate = new Carbon($groupedMessages[$contactNumber]['received_at']);
                    $newDate = new Carbon($msg->received_at);

                    if ($newDate->gt($existingDate)) {
                        $oldUnread = $groupedMessages[$contactNumber]['unread_count'];
                        $groupedMessages[$contactNumber] = $msg->toArray();
                        $groupedMessages[$contactNumber]['contact_number'] = $contactNumber;
                        $groupedMessages[$contactNumber]['unread_count'] = $oldUnread;
                        $groupedMessages[$contactNumber]['sender_name'] = $bestName;
                        $groupedMessages[$contactNumber]['is_lead'] = $isLead;
                    } else {
                        $groupedMessages[$contactNumber]['sender_name'] = $bestName;
                        $groupedMessages[$contactNumber]['is_lead'] = $isLead;
                    }
                }
            }

            // Filter for the "All" section (frontend "All" logic currently only shows unread and unconverted leads)
            $uniqueMessages = [];
            foreach ($groupedMessages as $msg) {
                if (!$msg['is_lead'] && $msg['unread_count'] > 0) {
                    $uniqueMessages[] = $msg;
                }
            }

            if (empty($uniqueMessages)) {
                $this->info("  ℹ️ No unconverted messages found for {$tenant->tenant_name}.");
                return;
            }

            // Sort descending by received_at
            usort($uniqueMessages, function($a, $b) {
                return strtotime($b['received_at']) < strtotime($a['received_at']) ? 1 : -1;
            });

            // If we want to limit the mail list to 20 or 50 so it's not huge
            $uniqueMessages = array_slice(array_reverse($uniqueMessages), 0, 50);

            foreach ($validRecipients as $recipient) {
                $payload = [
                    'alert_prefix' => $this->option('alert'),
                    'recipient_name' => $recipient['name'],
                    'messages' => $uniqueMessages,
                    'dateDisplay' => Carbon::now('Asia/Kolkata')->format('F j, Y'),
                    'year' => Carbon::now('Asia/Kolkata')->format('Y')
                ];

                try {
                    Mail::to($recipient['email'], $recipient['name'])->send(new WhatsappInboxMailReport($payload));
                    $this->info("  ✅ Mail sent to {$recipient['email']}");
                } catch (\Exception $e) {
                    $this->error("  ❌ Message could not be sent to {$recipient['email']}. Mailer Error: " . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
