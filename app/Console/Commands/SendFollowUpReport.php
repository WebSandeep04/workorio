<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SalesRecord;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\FollowUpReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendFollowUpReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:send-follow-up-report {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send follow-up due reports to sales users across all tenants.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting follow-up report generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found with sales enabled.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Follow-up report generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $today = Carbon::today()->format('Y-m-d');
            
            // Fetch users with SALES_USER_FOLLOWUP alert enabled strictly via EmailRecipientService
            $allowedEmails = \App\Services\EmailRecipientService::getRecipients('SALES_USER_FOLLOWUP');

            if (empty($allowedEmails)) {
                $this->info("  ⚠️ No users mapped in database for SALES_USER_FOLLOWUP in {$tenant->tenant_name}. Skipping.");
                return;
            }

            $users = User::whereIn('email', $allowedEmails)
                ->whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->get();

            foreach ($users as $user) {
                $leads = SalesRecord::with(['state', 'city', 'businessType', 'leadSource', 'status', 'product'])
                    ->where('user_id', $user->id)
                    ->whereDate('next_follow_up_date', '<=', $today)
                    ->whereNotIn('status_id', [1, 2, 15, 20])
                    ->orderBy('next_follow_up_date', 'DESC')
                    ->get();

                if ($leads->isEmpty()) {
                    continue; // No leads to follow up on
                }

                $sent = \App\Services\EmailRecipientService::sendAndLog(
                    'SALES_USER_FOLLOWUP',
                    $user->email,
                    new FollowUpReport($user, $leads, $today, $this->option('alert')),
                    ['leads_count' => $leads->count()]
                );
                if ($sent) {
                    $this->info("  ✅ Follow-up email sent to {$user->name} ({$user->email})");
                } else {
                    $this->error("  ❌ Failed to send follow-up email to {$user->name} (logged in audit table).");
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
