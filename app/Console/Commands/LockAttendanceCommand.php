<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LockAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:lock-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically locks attendance records from previous days across all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting multi-tenant attendance locking...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No active tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Multi-tenant attendance locking completed.');
        return 0;
    }

    /**
     * Process locking for a specific tenant
     */
    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            // Switch to tenant database
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $today = Carbon::today('Asia/Kolkata')->toDateString();
            
            // Find all attendance records before today that aren't locked yet
            $affectedCount = Attendance::where('date', '<', $today)
                ->where('is_locked', 0)
                ->update(['is_locked' => 1]);
                
            if ($affectedCount > 0) {
                $this->info("  ✅ Locked {$affectedCount} attendance records for {$tenant->tenant_name}.");
                Log::info("Attendance Auto-Lock ({$tenant->tenant_name}): Locked {$affectedCount} records dated before {$today}.");
            } else {
                $this->line("  No records found to lock for {$tenant->tenant_name}.");
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Error processing tenant {$tenant->tenant_name}: " . $e->getMessage());
            Log::error("Attendance Auto-Lock Error ({$tenant->tenant_name}): " . $e->getMessage());
        } finally {
            // Restore main connection
            DB::setDefaultConnection('mysql');
        }
    }
}
