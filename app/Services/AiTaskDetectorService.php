<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTaskDetectorService
{
    /**
     * The core system prompt adapted directly from the Node.js signal project.
     */
    const SYSTEM_PROMPT = <<<EOT
You help a small India-based business (Triserv 360) identify real, actionable tasks from WhatsApp group chat messages. Messages are often in English, Hindi, or Hinglish (a Hindi-English mix) — read them the way a fluent bilingual Indian colleague would.

You'll receive three sections:
- OPEN_TASKS: tasks already tracked for this chat (from an earlier automatic scan or a previous AI pass). Check every NEW message against this list before deciding anything.
- CONTEXT_MESSAGES: earlier messages in the same conversation, for background only — do not produce a decision for these, they're just there so you understand what NEW_MESSAGES are actually about.
- NEW_MESSAGES: the messages you need to classify. Every one needs exactly one decision.

For each message in NEW_MESSAGES, choose exactly one decision:
- "ignore" — not a task. Ordinary conversation, small talk, an FYI with nothing to act on.
- "new_task" — a clear, actionable request. IMPORTANT: Treat requests for review ("please check this"), status updates ("any update?"), and follow-ups as entirely NEW tasks. Do NOT ignore them or merge them into existing tasks.
- "enrich" — this message describes the SAME underlying task as something already in OPEN_TASKS (e.g. it's a reply, confirmation, or restatement of an existing ask) — reference that task's id.
- "looks_done" — this message suggests a task in OPEN_TASKS may now be complete (e.g. "sent it", "done boss", "delivered"). You are never certain from a single message, so this only ever suggests it for human confirmation — never assume. Reference the task's id.
- "possible" — you genuinely can't tell whether this is a task or not, even after considering the context. Don't guess either way.

NEW_MESSAGES can also be follow-ups or confirmations of EACH OTHER, not only of tasks already in OPEN_TASKS — this matters specifically because every message in a batch is being decided together, in one pass. A message you decide "new_task" doesn't have a real task id yet (it's only created after your response comes back), so nothing later in this same batch can reference it the normal way. When two or more NEW_MESSAGES clearly form one conversational exchange about a single task — e.g. a request, immediately followed shortly after by someone's reply confirming, accepting, or restating that exact same request — do not create a separate task for each one. Instead: pick whichever single message best represents the complete task (usually the original request), decide "new_task" for that message only, folding in any extra detail mentioned in the other message(s) of that exchange (a due date first mentioned in the reply, for instance, still belongs in this one summary/due_date), and decide "ignore" for the rest of that exchange, since they don't need their own separate entry once the real one already captures them.

When the decision is "new_task" or "enrich", also include:
- "summary": a short, clean, plain-language rewrite of what actually needs to be done — not a translation of the raw text, a genuine rewrite a busy manager could read in two seconds. Example: raw "haan bhai kal tak bhej dunga usko" → summary "Send the requested document to the client by tomorrow."
- "requested_by": the name of whoever is actually asking for this — look at the surrounding conversation, since it may not be whoever sent this specific message.
- "assigned_to": the name of whoever is responsible for doing it.
- "due_date": your best-guess date in YYYY-MM-DD format if one is stated or clearly implied, otherwise null. Use the message's own timestamp as "today" when resolving relative phrases like "tomorrow" or "by Friday".
- "due_date_text": a short human-readable version of the due date as the message phrased it (e.g. "tomorrow", "by Friday"), or null.

Whether something is a task and who it's for are two separate questions — never let uncertainty about the second one leak into the first. A message can clearly describe an action to take even when the person it's aimed at can't be confidently identified (e.g. it mentions a raw phone number or ID with no name attached, because WhatsApp's own mention data didn't resolve to one — a STAFF_DIRECTORY section may be included below specifically to help with this, but even without a match, an unclear "who" is not a reason to doubt the "what"). If the action itself is clear, classify it as "new_task" or "enrich" as normal, and set "assigned_to" to whatever's actually knowable (a resolved name if one's available, otherwise the raw mention text, or null if truly nothing is known) — don't downgrade a clear task to "possible" just because the assignee is uncertain.

Be conservative. When genuinely unsure, use "possible" rather than guessing "new_task" or "ignore" — a missed task is recoverable (a human can still see it in the raw chat), but a wrongly-invented task creates real clutter and wastes someone's time reviewing it.

Respond with ONLY valid JSON in this exact shape, nothing else:
{"results": [{"message_id": <id>, "decision": "<one of the six above>", "task_id_ref": <id or null>, "summary": <string or null>, "requested_by": <string or null>, "assigned_to": <string or null>, "due_date": <"YYYY-MM-DD" or null>, "due_date_text": <string or null>}]}
EOT;

    /**
     * Check if a list of messages contains unresolved WhatsApp mentions.
     */
    public function hasUnresolvedMention(array $messages): bool
    {
        $pattern = '/@\d{8,}/';
        foreach ($messages as $msg) {
            if (isset($msg['text']) && preg_match($pattern, $msg['text'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build the prompt for OpenAI.
     */
    public function buildPrompt(
        array $contextMessages,
        array $newMessages,
        array $openTasks,
        ?string $extraGuidance = null,
        array $staffDirectory = [],
        array $clientDirectory = []
    ): array {
        $userPayload = [
            'OPEN_TASKS' => array_map(function ($t) {
                return [
                    'id' => $t['id'],
                    'text' => $t['text'],
                    'assignee' => $t['assignee'] ?? null,
                    'open_since' => $t['created_at'] ?? null,
                ];
            }, $openTasks),
            'CONTEXT_MESSAGES' => $contextMessages,
            'NEW_MESSAGES' => $newMessages,
        ];

        $hasStaff = count($staffDirectory) > 0;
        $hasClients = count($clientDirectory) > 0;

        if ($hasStaff) {
            $userPayload['STAFF_DIRECTORY'] = array_map(function ($s) {
                return ['name' => $s['name'], 'phone' => $s['phone'], 'role' => $s['role'] ?? null];
            }, $staffDirectory);
        }

        if ($hasClients) {
            $userPayload['CLIENT_DIRECTORY'] = array_map(function ($c) {
                return ['name' => $c['name'], 'phone' => $c['phone'], 'company' => $c['company'] ?? null];
            }, $clientDirectory);
        }

        $trimmedGuidance = trim($extraGuidance ?? '');
        $systemPrompt = $trimmedGuidance
            ? self::SYSTEM_PROMPT . "\n\n---\nAdditional guidance from the admin, based on real feedback (the rules above still apply — this only adds to them, it never overrides the JSON output format or the six decision types):\n{$trimmedGuidance}"
            : self::SYSTEM_PROMPT;

        if ($hasStaff || $hasClients) {
            $sections = [];
            if ($hasStaff) $sections[] = 'a STAFF_DIRECTORY (people who do the work — use for "assigned_to")';
            if ($hasClients) $sections[] = 'a CLIENT_DIRECTORY (people who ask for things — use for "requested_by")';
            $sectionsStr = implode(" and ", $sections);
            $systemPrompt .= "\n\n---\nSome messages contain an unresolved WhatsApp mention — a raw phone number or internal ID with no name attached, because the free system couldn't match it automatically. The data below includes {$sectionsStr} specifically for this: each entry has a \"name\" field and a \"phone\" field (and, for clients, a \"company\" field). If a mentioned number matches (or very plausibly corresponds to) an entry's \"phone\" field, use that entry's \"name\" field value — the actual person's name, e.g. \"Areesha Kulsoom\", never the phone number itself, even though the phone number is what you matched on. If nothing plausibly matches in either directory, leave your answer as you normally would — don't force a match.";
        }

        return [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => json_encode($userPayload, JSON_UNESCAPED_UNICODE)],
        ];
    }

    /**
     * Call the OpenAI API to analyze the messages.
     */
    public function detectTasks(array $messages, string $model = 'gpt-5-nano'): array
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            Log::error('OpenAI API Key is missing.');
            return [];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error', ['body' => $response->body()]);
                \App\Models\AiApiLog::create([
                    'endpoint' => 'chat/completions',
                    'model' => $model,
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'total_tokens' => 0,
                    'payload' => $messages,
                    'response' => $response->json() ?? ['body' => $response->body()],
                    'error_message' => 'API Request Failed: ' . $response->status(),
                ]);
                return [];
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                Log::error('OpenAI response had no content.');
                \App\Models\AiApiLog::create([
                    'endpoint' => 'chat/completions',
                    'model' => $model,
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                    'payload' => $messages,
                    'response' => $data,
                    'error_message' => 'OpenAI response had no content.',
                ]);
                return [];
            }

            $parsed = json_decode($content, true);

            \App\Models\AiApiLog::create([
                'endpoint' => 'chat/completions',
                'model' => $model,
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
                'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                'payload' => $messages,
                'response' => $parsed,
                'error_message' => null,
            ]);

            return [
                'results' => isset($parsed['results']) && is_array($parsed['results']) ? $parsed['results'] : [],
                'promptTokens' => $data['usage']['prompt_tokens'] ?? 0,
                'completionTokens' => $data['usage']['completion_tokens'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error communicating with OpenAI: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get distinct chats that have pending messages
     */
    public function getPendingChats(): array
    {
        return \Illuminate\Support\Facades\DB::table('signal_whatsapp_msg')
            ->where('is_ai_processed', false)
            ->distinct()
            ->pluck('chat')
            ->toArray();
    }

    /**
     * Process pending messages for a single chat
     */
    public function processPendingMessagesForChat($chatName, $logger = null): bool
    {
        $chatLogName = $chatName ?? 'Direct Messages';
        if ($logger) $logger->info("Processing chat: {$chatLogName}");

        // 1. Fetch NEW unprocessed messages for THIS chat
        $queryNew = \Illuminate\Support\Facades\DB::table('signal_whatsapp_msg')
            ->where('is_ai_processed', false)
            ->orderBy('id', 'asc')
            ->limit(50); // process in batches per chat
        
        if ($chatName === null) {
            $queryNew->whereNull('chat');
        } else {
            $queryNew->where('chat', $chatName);
        }
        
        $newMessagesRaw = $queryNew->get();

        if ($newMessagesRaw->isEmpty()) return true;

        $newMessagesIds = $newMessagesRaw->pluck('id')->toArray();
        $newMessages = $newMessagesRaw->map(function ($m) {
            return [
                'id' => $m->id,
                'from' => $m->sender,
                'time' => $m->created_at,
                'text' => $m->message_text,
            ];
        })->toArray();

        // 2. Fetch CONTEXT messages for THIS chat
        $queryContext = \Illuminate\Support\Facades\DB::table('signal_whatsapp_msg')
            ->where('is_ai_processed', true)
            ->orderBy('id', 'desc')
            ->limit(20);
            
        if ($chatName === null) {
            $queryContext->whereNull('chat');
        } else {
            $queryContext->where('chat', $chatName);
        }
            
        $contextMessagesRaw = $queryContext->get()->reverse()->values();

        $contextMessages = $contextMessagesRaw->map(function ($m) {
            return [
                'id' => $m->id,
                'from' => $m->sender,
                'time' => $m->created_at,
                'text' => $m->message_text,
            ];
        })->toArray();

        // 3. Fetch OPEN_TASKS for THIS chat
        $queryTasks = \Illuminate\Support\Facades\DB::table('signal_ai_tasks')
            ->join('signal_whatsapp_msg', 'signal_ai_tasks.message_id', '=', 'signal_whatsapp_msg.id')
            ->where('signal_ai_tasks.status', 'pending')
            ->select('signal_ai_tasks.*');
            
        if ($chatName === null) {
            $queryTasks->whereNull('signal_whatsapp_msg.chat');
        } else {
            $queryTasks->where('signal_whatsapp_msg.chat', $chatName);
        }
            
        $openTasksRaw = $queryTasks->get();
            
        $openTasks = $openTasksRaw->map(function ($t) {
            return [
                'id' => $t->id,
                'text' => $t->title . ' - ' . $t->description,
                'assignee' => $t->ai_assigned_to,
                'created_at' => $t->created_at,
            ];
        })->toArray();

        $staffDirectory = [];
        $clientDirectory = [];

        $messages = $this->buildPrompt($contextMessages, $newMessages, $openTasks, null, $staffDirectory, $clientDirectory);

        if ($logger) $logger->info("Sending prompt to OpenAI for chat {$chatLogName}...");
        
        $response = $this->detectTasks($messages);

        if (empty($response['results'])) {
            if ($logger) $logger->warn("No results returned from AI or error occurred for chat {$chatLogName}.");
            return false;
        }

        if ($logger) $logger->info("Received " . count($response['results']) . " decisions for chat {$chatLogName}.");

        foreach ($response['results'] as $result) {
            $msgId = $result['message_id'] ?? null;
            $decision = $result['decision'] ?? 'ignore';

            if ($decision === 'new_task') {
                $originalMessage = collect($newMessages)->firstWhere('id', $msgId);
                $originalText = $originalMessage ? $originalMessage['text'] : '';

                \Illuminate\Support\Facades\DB::table('signal_ai_tasks')->insert([
                    'message_id' => $msgId,
                    'title' => $result['summary'] ?? 'Detected Task',
                    'description' => $originalText,
                    'ai_assigned_to' => $result['assigned_to'] ?? null,
                    'ai_requested_by' => $result['requested_by'] ?? null,
                    'status' => 'pending',
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
                if ($logger) $logger->info("Created new AI task for message {$msgId} in chat {$chatLogName}");
            } elseif (in_array($decision, ['enrich', 'followup', 'looks_done'])) {
                if ($logger) $logger->info("Action {$decision} for message {$msgId} referencing task {$result['task_id_ref']}");
                
                if (isset($result['task_id_ref']) && ($decision === 'enrich' || $decision === 'followup')) {
                    $updateData = [];
                    if (!empty($result['summary'])) $updateData['title'] = $result['summary'];
                    if (!empty($result['assigned_to'])) $updateData['ai_assigned_to'] = $result['assigned_to'];
                    if (!empty($result['requested_by'])) $updateData['ai_requested_by'] = $result['requested_by'];
                    
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = \Carbon\Carbon::now();
                        \Illuminate\Support\Facades\DB::table('signal_ai_tasks')
                            ->where('id', $result['task_id_ref'])
                            ->update($updateData);
                        if ($logger) $logger->info("Updated AI task {$result['task_id_ref']} with new info from {$decision}.");
                    }
                }
            }
        }

        \Illuminate\Support\Facades\DB::table('signal_whatsapp_msg')
            ->whereIn('id', $newMessagesIds)
            ->update(['is_ai_processed' => true]);
            
        return true;
    }

    /**
     * Process pending messages and save AI tasks (All chats)
     */
    public function processPendingMessages($logger = null): bool
    {
        if ($logger) $logger->info('Starting AI Task Detection...');

        $chatsToProcess = $this->getPendingChats();

        if (empty($chatsToProcess)) {
            if ($logger) $logger->info('No new messages to process.');
            return true;
        }

        foreach ($chatsToProcess as $chatName) {
            $this->processPendingMessagesForChat($chatName, $logger);
        }

        if ($logger) $logger->info('Processing complete.');
        return true;
    }
}
