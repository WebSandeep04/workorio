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
    protected $signature = 'ai:fetch-tasks {--lookback= : Lookback window in minutes}';

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
        $lookbackMinutes = $this->option('lookback');
        if (!$lookbackMinutes) {
            $this->error('Lookback option is required.');
            return 1;
        }

        $endTime = Carbon::now('Asia/Kolkata');
        $startTime = $endTime->copy()->subMinutes((int)$lookbackMinutes);

        $this->info("Fetching AI Tasks from {$startTime->toDateTimeString()} to {$endTime->toDateTimeString()} (IST)");
        Log::info("FetchAiTasksCommand started", ['start_time' => $startTime->toDateTimeString(), 'end_time' => $endTime->toDateTimeString()]);

        try {
            $this->info('Triggering AI Task Detection...');
            
            $aiService = app(\App\Services\AiTaskDetectorService::class);
            $aiService->processPendingMessages($this);
            
            $this->info('Successfully processed tasks.');
            Log::info("FetchAiTasksCommand success");
        } catch (\Exception $e) {
            $this->error("Error processing AI tasks: " . $e->getMessage());
            Log::error("FetchAiTasksCommand exception", ['error' => $e->getMessage()]);
        }

        return 0;
    }
}
