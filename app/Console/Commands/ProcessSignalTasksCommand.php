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
        $this->info('Starting AI Task Detection...');

        // 1. Fetch NEW unprocessed messages
        $newMessagesRaw = DB::table('signal_whatsapp_msg')
            ->where('is_ai_processed', false)
            ->orderBy('id', 'asc')
            ->limit(50) // process in batches
            ->get();

        if ($newMessagesRaw->isEmpty()) {
            $this->info('No new messages to process.');
            return 0;
        }

        $newMessagesIds = $newMessagesRaw->pluck('id')->toArray();
        $newMessages = $newMessagesRaw->map(function ($m) {
            return [
                'id' => $m->id,
                'sender' => $m->sender,
                'created_at' => $m->created_at,
                'text' => $m->message_text,
            ];
        })->toArray();

        // 2. Fetch CONTEXT messages (e.g., last 20 processed messages)
        $contextMessagesRaw = DB::table('signal_whatsapp_msg')
            ->where('is_ai_processed', true)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

        $contextMessages = $contextMessagesRaw->map(function ($m) {
            return [
                'id' => $m->id,
                'sender' => $m->sender,
                'created_at' => $m->created_at,
                'text' => $m->message_text,
            ];
        })->toArray();

        // 3. Fetch OPEN_TASKS (currently pending AI tasks)
        // Here we could also fetch actual tasks, but since we are inserting into signal_ai_tasks,
        // it makes sense to let AI know about its own previously detected open tasks.
        $openTasksRaw = DB::table('signal_ai_tasks')
            ->where('status', 'pending')
            ->get();
            
        $openTasks = $openTasksRaw->map(function ($t) {
            return [
                'id' => $t->id,
                'text' => $t->title . ' - ' . $t->description,
                'assignee' => $t->ai_assigned_to,
                'created_at' => $t->created_at,
            ];
        })->toArray();

        // Directories for resolving mentions
        $staffDirectory = [];
        $clientDirectory = [];

        // Build prompt
        $messages = $aiService->buildPrompt($contextMessages, $newMessages, $openTasks, null, $staffDirectory, $clientDirectory);

        $this->info('Sending prompt to OpenAI...');
        
        // Detect tasks
        $response = $aiService->detectTasks($messages);

        if (empty($response['results'])) {
            $this->warn('No results returned from AI or error occurred.');
            
            // Mark as processed anyway to avoid infinite loop on bad messages?
            // Optional: DB::table('signal_whatsapp_msg')->whereIn('id', $newMessagesIds)->update(['is_ai_processed' => true]);
            return 1;
        }

        $this->info('Received ' . count($response['results']) . ' decisions.');

        // Process decisions
        foreach ($response['results'] as $result) {
            $msgId = $result['message_id'] ?? null;
            $decision = $result['decision'] ?? 'ignore';

            if ($decision === 'new_task') {
                DB::table('signal_ai_tasks')->insert([
                    'message_id' => $msgId,
                    'title' => $result['summary'] ?? 'Detected Task',
                    'description' => json_encode($result), // store full AI JSON context in description or handle properly
                    'ai_assigned_to' => $result['assigned_to'] ?? null,
                    'ai_requested_by' => $result['requested_by'] ?? null,
                    'status' => 'pending',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $this->info("Created new AI task for message {$msgId}");
            } elseif ($decision === 'enrich' || $decision === 'followup' || $decision === 'looks_done') {
                // Future enhancement: append to existing signal_ai_tasks description
                $this->info("Action {$decision} for message {$msgId} referencing task {$result['task_id_ref']}");
            }
        }

        // Mark processed
        DB::table('signal_whatsapp_msg')
            ->whereIn('id', $newMessagesIds)
            ->update(['is_ai_processed' => true]);

        $this->info('Processing complete.');
        return 0;
    }
}
