<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\SignalPendingTasksReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Exception;

class SendSignalPendingTasksReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signal:send-pending-report 
                            {--email= : Send only to a specific email override for testing}
                            {--tenant_id= : Process a specific tenant ID}
                            {--alert= : Alert prefix for email subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily report of all pending/not-converted Signal tasks (AI + Manual) to Role ID 1 Admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Signal Pending Tasks Report generation...');

        $tenantId = $this->option('tenant_id');
        $query = Tenant::on('mysql');
        if ($tenantId) {
            $query->where('id', $tenantId);
        }
        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->info("No matching tenants found.");
            return 0;
        }

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Signal Pending Tasks Report processing finished.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Check if signal_ai_tasks table exists in this tenant DB
            if (!Schema::hasTable('signal_ai_tasks')) {
                $this->warn("  ⚠️ signal_ai_tasks table does not exist for tenant {$tenant->tenant_name}. Skipping.");
                return;
            }

            // Determine recipient list
            $overrideEmail = $this->option('email');
            $recipients = [];

            if ($overrideEmail) {
                $recipients[] = [
                    'email' => $overrideEmail,
                    'name' => 'Testing Admin'
                ];
            } else {
                // Fetch active users with role_id = 1
                $users = User::whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })->where('role_id', 1)->get();

                // Fallback: If no employee record linked, check active role_id = 1 directly
                if ($users->isEmpty()) {
                    $users = User::where('role_id', 1)->get();
                }

                foreach ($users as $user) {
                    if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'email' => $user->email,
                            'name' => $user->name ?: 'Admin'
                        ];
                    }
                }
            }

            if (empty($recipients)) {
                $this->warn("  ⚠️ No valid recipients with role_id = 1 found for tenant {$tenant->tenant_name}.");
                return;
            }

            // 1. Fetch AI pending tasks
            $hasMsgTable = Schema::hasTable('signal_whatsapp_msg');
            $aiQuery = DB::table('signal_ai_tasks');
            if ($hasMsgTable) {
                $aiQuery->leftJoin('signal_whatsapp_msg', 'signal_ai_tasks.message_id', '=', 'signal_whatsapp_msg.id')
                    ->select(
                        'signal_ai_tasks.*',
                        'signal_whatsapp_msg.chat as chat_name',
                        'signal_whatsapp_msg.sender as msg_sender'
                    );
            } else {
                $aiQuery->select(
                    'signal_ai_tasks.*',
                    DB::raw('NULL as chat_name'),
                    DB::raw('NULL as msg_sender')
                );
            }

            $aiTasks = $aiQuery->where(function ($q) {
                $q->where('signal_ai_tasks.status', 'pending')
                  ->orWhereNull('signal_ai_tasks.status');
            })->get()->map(function ($task) {
                return [
                    'id' => $task->id,
                    'task_type' => 'ai',
                    'title' => $task->title,
                    'description' => $task->description,
                    'chat_name' => $task->chat_name ?? 'N/A',
                    'assigned_to' => $task->ai_assigned_to ?? 'Unassigned',
                    'requested_by' => $task->ai_requested_by ?: ($task->msg_sender ?? '-'),
                    'status' => $task->status ?: 'pending',
                    'created_at' => $task->created_at,
                    'created_at_formatted' => $task->created_at ? Carbon::parse($task->created_at)->timezone('Asia/Kolkata')->format('d M Y') : 'N/A',
                ];
            });

            // 2. Fetch Manual / Immediate pending tasks
            $manualTasks = collect([]);
            if (Schema::hasTable('immediate_tasks')) {
                $manualTasks = DB::table('immediate_tasks')
                    ->where(function ($q) {
                        $q->where('status', 'pending')
                          ->orWhereNull('status');
                    })
                    ->get()
                    ->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'task_type' => 'manual',
                            'title' => $task->title,
                            'description' => $task->description,
                            'chat_name' => 'Manual Entry',
                            'assigned_to' => 'Unassigned',
                            'requested_by' => '-',
                            'status' => $task->status ?: 'pending',
                            'created_at' => $task->created_at,
                            'created_at_formatted' => $task->created_at ? Carbon::parse($task->created_at)->timezone('Asia/Kolkata')->format('d M Y') : 'N/A',
                        ];
                    });
            }

            // Combine and sort by newest first
            $allTasks = $aiTasks->concat($manualTasks)->sortByDesc('created_at')->values();

            $payload = [
                'tenant_name' => $tenant->tenant_name,
                'dateDisplay' => Carbon::now()->timezone('Asia/Kolkata')->format('d M Y, h:i A'),
                'alert_prefix' => $this->option('alert'),
                'total_pending' => $allTasks->count(),
                'ai_count' => $aiTasks->count(),
                'manual_count' => $manualTasks->count(),
                'tasks' => $allTasks->toArray(),
                'app_url' => url('/signal-task'),
            ];

            // Send email to each recipient
            foreach ($recipients as $recipient) {
                $recipientPayload = array_merge($payload, [
                    'recipient_name' => $recipient['name'],
                ]);

                try {
                    Mail::to($recipient['email'])->send(new SignalPendingTasksReportMail($recipientPayload));
                    $this->info("  ✓ Report sent successfully to: {$recipient['email']}");
                } catch (Exception $mailEx) {
                    $this->error("  ✗ Failed to send to {$recipient['email']}: " . $mailEx->getMessage());
                }
            }
        } catch (Exception $e) {
            $this->error("  ✗ Error processing tenant {$tenant->tenant_name}: " . $e->getMessage());
        }
    }
}

