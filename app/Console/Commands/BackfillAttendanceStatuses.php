<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Attendance;
use App\Services\TenantDatabaseService;
use App\Services\AttendanceReportService;

class BackfillAttendanceStatuses extends Command
{
    protected $signature = 'attendance:backfill {--tenant= : Specific tenant ID to process} {--month= : Specific month (1-12) to backfill} {--year= : Specific year to backfill} {--force : Recalculate even if computed_status is already set}';
    protected $description = 'Backfills or recalculates computed_status for historical attendance records';

    public function handle(AttendanceReportService $reportService)
    {
        $tenantId = $this->option('tenant');
        
        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::where('status', 'active')->get();
        
        $this->info("Starting backfill for " . $tenants->count() . " tenant(s).");
        
        foreach ($tenants as $tenant) {
            $this->info("Processing Tenant: {$tenant->name} ({$tenant->id})");
            
            try {
                TenantDatabaseService::setDefaultConnection($tenant->id);
                
                $query = Attendance::query();
                
                if ($this->option('month')) {
                    $query->whereMonth('date', $this->option('month'));
                }
                
                if ($this->option('year')) {
                    $query->whereYear('date', $this->option('year'));
                }
                
                if (!$this->option('force')) {
                    $query->whereNull('computed_status');
                }
                
                $attendances = $query->get();
                $count = $attendances->count();
                
                if ($count === 0) {
                    $this->info("  No pending records to backfill.");
                    continue;
                }
                
                $this->info("  Found {$count} records to calculate.");
                
                $bar = $this->output->createProgressBar($count);
                
                foreach ($attendances as $attendance) {
                    try {
                        $reportService->computeAndSaveDailyStatus($attendance);
                    } catch (\Exception $e) {
                        $this->error("  Failed for Attendance ID {$attendance->id}: " . $e->getMessage());
                    }
                    $bar->advance();
                }
                
                $bar->finish();
                $this->line("");
                
            } catch (\Exception $e) {
                $this->error("Error processing tenant {$tenant->id}: " . $e->getMessage());
            }
        }
        
        // Reset to default connection
        config(['database.default' => 'mysql']);
        \DB::reconnect('mysql');
        
        $this->info("Backfill completed!");
    }
}
