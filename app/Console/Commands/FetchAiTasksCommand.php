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
            $response = Http::timeout(30)->get('http://localhost:8000/signal-task', [
                'start_time' => $startTime->toISOString(),
                'end_time' => $endTime->toISOString(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Successfully fetched tasks. Count: ' . (is_array($data) ? count($data) : 'Unknown'));
                Log::info("FetchAiTasksCommand success", ['data' => $data]);
                // Here you would process the $data and save it to the DB if required.
                // Depending on the exact response structure of http://localhost:8000/signal-task
            } else {
                $this->error("Failed to fetch tasks. Status: " . $response->status());
                Log::error("FetchAiTasksCommand failed", ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error("Error connecting to signal server: " . $e->getMessage());
            Log::error("FetchAiTasksCommand exception", ['error' => $e->getMessage()]);
        }

        return 0;
    }
}
