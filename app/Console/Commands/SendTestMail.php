<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to sandeep@triserv360.com to verify SMTP configuration.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting test mail dispatch...');
        
        $recipient = 'sandeep@triserv360.com';
        
        try {
            Mail::to($recipient)->send(new TestMail());
            $this->info("Successfully sent test mail to {$recipient}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send test mail: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
