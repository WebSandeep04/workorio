<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\IndiaMartLeadNotification;

class TestIndiaMartEmail extends Command
{
    protected $signature = 'indiamart:test-email {email?}';
    protected $description = 'Test IndiaMART lead notification email';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Sample lead data for testing
        $leadData = [
            'unique_query_id' => 'TEST-' . time(),
            'query_type' => 'B',
            'query_time' => now()->format('Y-m-d H:i:s'),
            'sender_name' => 'Test Customer',
            'sender_company' => 'Test Company Ltd',
            'sender_mobile' => '9876543210',
            'sender_email' => 'test@example.com',
            'sender_city' => 'Mumbai',
            'sender_state' => 'Maharashtra',
            'query_product_name' => 'Test Product',
            'query_message' => 'This is a test lead from IndiaMART webhook testing.',
        ];

        if ($email) {
            // Send to specific email
            $this->info("Sending test email to: {$email}");
            try {
                Mail::to($email)->send(new IndiaMartLeadNotification($leadData));
                $this->info("✅ Email sent successfully!");
                $this->info("Check your inbox at: {$email}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send email: " . $e->getMessage());
            }
        } else {
            // Send to all sales users
            $salesUsers = User::where('is_sales', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();

            if ($salesUsers->isEmpty()) {
                $this->warn('⚠️  No sales users found with is_sales = 1 and valid email');
                return 1;
            }

            $this->info("Found {$salesUsers->count()} sales user(s):");
            foreach ($salesUsers as $user) {
                $this->line("  - {$user->name} ({$user->email})");
            }

            if ($this->confirm('Send test email to all sales users?', true)) {
                $sentCount = 0;
                foreach ($salesUsers as $user) {
                    try {
                        Mail::to($user->email)->send(new IndiaMartLeadNotification($leadData));
                        $this->info("  ✅ Sent to {$user->email}");
                        $sentCount++;
                    } catch (\Exception $e) {
                        $this->error("  ❌ Failed to send to {$user->email}: " . $e->getMessage());
                    }
                }
                $this->info("📧 Sent {$sentCount} email(s) successfully!");
            }
        }

        // Show mail configuration
        $this->newLine();
        $this->info('Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );

        return 0;
    }
}

