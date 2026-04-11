<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Exception;

class FixPostpaidDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:fix-postpaid-due-dates {--dry-run : Only show what would be fixed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Postpaid subscription due dates that were incorrectly set to period_end instead of cycle transition day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE: No changes will be saved.');
        }

        $this->info('Starting Postpaid due date correction...');

        $tenants = Tenant::on('mysql')->get();

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant, $dryRun);
        }

        $this->info('Correction completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant, $dryRun)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            // Find records where billing_type is Postpaid AND due_date = period_end
            $query = DB::table('subscription_histories')
                ->join('subscriptions', 'subscription_histories.subscription_id', '=', 'subscriptions.id')
                ->where('subscriptions.billing_type', 'Postpaid')
                ->whereColumn('subscription_histories.due_date', 'subscription_histories.period_end');

            $count = $query->count();

            if ($count === 0) {
                $this->info("  ✅ No incorrect dates found.");
                return;
            }

            if ($dryRun) {
                $this->info("  🔍 Found {$count} records to fix.");
            } else {
                $affected = DB::table('subscription_histories')
                    ->join('subscriptions', 'subscription_histories.subscription_id', '=', 'subscriptions.id')
                    ->where('subscriptions.billing_type', 'Postpaid')
                    ->whereColumn('subscription_histories.due_date', 'subscription_histories.period_end')
                    ->update([
                        'subscription_histories.due_date' => DB::raw('DATE_ADD(subscription_histories.period_end, INTERVAL 1 DAY)')
                    ]);

                $this->info("  ✨ Fixed {$affected} records.");
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
