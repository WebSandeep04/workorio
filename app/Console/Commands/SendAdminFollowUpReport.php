<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SalesRecord;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\AdminFollowUpReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendAdminFollowUpReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:send-admin-follow-up-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send consolidated daily follow-up reports to tenant admins/managers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting consolidated admin follow-up report generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found with sales enabled.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Admin log follow-up generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $today = Carbon::today()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            
            // Get admins to send to
            // Using the requested fixed emails if this is the main tenant, 
            // otherwise falling back to role_id=1 for true multitenant scale.
            
            // For TRISERV specifically, it is ID 1 based on previous bash output
            if ($tenant->id == 1) {
                $recipientEmails = [
                    'sandeep@triserv360.com',
                    'shamshad@triserv360.com',
                    'areesha@triserv360.com',
                    'anupriya@triserv360.com'
                ];
            } else {
                $recipientEmails = User::where('role_id', 1)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
            }

            if (empty($recipientEmails)) {
                $this->info("  No admin recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            // 1. Fetch Follow Ups
            $records = SalesRecord::with(['user', 'state', 'city', 'businessType', 'leadSource', 'status', 'product'])
                ->whereDate('next_follow_up_date', '<=', $today)
                ->whereNotIn('status_id', [1, 2, 15, 20])
                ->get()
                ->sortBy([
                    fn ($a, $b) => ($a->user->name ?? 'Unknown') <=> ($b->user->name ?? 'Unknown'),
                    fn ($a, $b) => $b->next_follow_up_date <=> $a->next_follow_up_date,
                ]);

            if ($records->isEmpty()) {
                $this->info("  No pending follow ups found for {$tenant->tenant_name}");
                // We should still proceed because maybe there are new leads
            }

            // 2. Fetch New Leads
            $newLeads = SalesRecord::with(['user', 'city', 'status', 'product'])
                ->whereDate('createdat', '>=', $yesterday)
                ->orderBy('createdat', 'DESC')
                ->get();
                
            if ($records->isEmpty() && $newLeads->isEmpty()) {
                $this->info("  No data to email for {$tenant->tenant_name}");
                return;
            }

            // 3. Build Summary Count
            $summary = [];
            foreach ($records as $rec) {
                $user = $rec->user ? $rec->user->name : 'Unknown';
                if (!isset($summary[$user])) {
                    $summary[$user] = 0;
                }
                $summary[$user]++;
            }

            try {
                Mail::to($recipientEmails)->send(new AdminFollowUpReport($records, $newLeads, $today, $summary));
                $this->info("  ✅ Admin follow-up report sent to: " . implode(', ', $recipientEmails));
            } catch (\Exception $e) {
                $this->error("  ❌ Mailer Error: " . $e->getMessage());
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
