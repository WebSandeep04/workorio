<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchAiTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:fetch-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch AI tasks from the signal server based on the lookback window.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = \App\Models\Tenant::on('mysql')->get();

        foreach ($tenants as $tenant) {
            try {
                \App\Services\TenantDatabaseService::setDefaultConnection($tenant->id);
                
                if (!\Illuminate\Support\Facades\Schema::hasTable('ai_scheduler_configs')) {
                    continue; // Skip if tables don't exist yet for this tenant
                }

                $config = \App\Models\AiSchedulerConfig::first();
                $lookbackMinutes = $this->determineLookbackAndIfShouldRun($config);

                if ($lookbackMinutes !== false) {
                    $this->info("Running for tenant {$tenant->tenant_name} with lookback {$lookbackMinutes}");

                    $aiService = app(\App\Services\AiTaskDetectorService::class);
                    $aiService->processPendingMessages($this);
                }

            } catch (\Exception $e) {
                $this->error("Error processing tenant {$tenant->tenant_name}: " . $e->getMessage());
            } finally {
                \Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
            }
        }

        return 0;
    }

    private function determineLookbackAndIfShouldRun($config) 
    {
        if (!$config) return false;

        $now = Carbon::now('Asia/Kolkata');
        $currentHi = $now->format('H:i');
        $currentMinute = (int) $now->format('i');

        // Check fixed passes first
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_scheduler_fixed_passes')) {
            $passes = \App\Models\AiSchedulerFixedPass::where('is_active', true)->get();
            foreach ($passes as $pass) {
                if (substr($pass->run_at, 0, 5) === $currentHi) {
                    return $pass->lookback_minutes;
                }
            }
        }

        $allDays = [0, 1, 2, 3, 4, 5, 6]; // 0 is Sunday
        $offDays = is_array($config->week_offs) ? $config->week_offs : json_decode($config->week_offs, true) ?? [];
        $workingDays = array_diff($allDays, $offDays);
        
        $currentDay = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');
        
        $isWorkingDay = in_array($currentDay, $workingDays);
        $isOfficeHours = $currentTime >= $config->office_start_time && $currentTime <= $config->office_end_time;

        if ($isWorkingDay && $isOfficeHours) {
            $freq = (int) $config->office_day_frequency;
            if ($freq > 0 && $currentMinute % $freq === 0) {
                return $config->office_day_lookback;
            }
        } else {
            // Off hours or Off day
            $freq = (int) $config->off_day_frequency;
            if ($freq > 0 && $currentMinute % $freq === 0) {
                return $config->off_day_lookback;
            }
        }

        return false;
    }
}
