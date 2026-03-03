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
    protected $signature = 'sales:send-follow-up-report';

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
            
            $users = User::where('is_sales', 1)->whereNotNull('email')->get();

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

                try {
                    Mail::to($user->email)->send(new FollowUpReport($user, $leads, $today));
                    $this->info("  ✅ Follow-up email sent to {$user->name} ({$user->email})");
                } catch (\Exception $e) {
                    $this->error("  ❌ Mailer Error for {$user->name}: " . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
