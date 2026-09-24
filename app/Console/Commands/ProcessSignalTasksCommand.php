<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\AiTaskDetectorService;
use Carbon\Carbon;

class ProcessSignalTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signal:process-ai-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process unprocessed Signal WhatsApp messages using AI to detect tasks';

    /**
     * Execute the console command.
     */
    public function handle(AiTaskDetectorService $aiService)
    {
        $success = $aiService->processPendingMessages($this);
        return $success ? 0 : 1;
    }
}
