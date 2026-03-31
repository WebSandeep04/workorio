<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\SelfTaskMailReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendSelfTaskMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:send-self-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily task reminders to individual users for their pending tasks.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Self Tasks mail generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Self Tasks email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Determine users with notifications enabled
            $hasIsTask = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_task');
            
            $users = [];
            if ($hasIsTask) {
                $users = User::whereHas('employee', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('is_task', 1)
                    ->whereNotNull('email')
                    ->get();
            }

            if ($users->isEmpty()) {
                $this->info("  ⚠️ No users with is_task=1 found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            foreach ($users as $user) {
                $userEmail = $user->email;
                $userName = $user->name;

                if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->line("  ⚠️ Invalid email for user: {$userName}. Skipping.");
                    continue;
                }

                $sql_tasks = "
                    SELECT 
                        t.id, 
                        t.task_name,
                        t.task, 
                        c.name as customer_name,
                        c.company_name as customer_company,
                        ts.name as status_name,
                        ts.color as status_color,
                        tp.name as priority_name,
                        tp.color as priority_color,
                        t.created_at
                    FROM tasks t
                    LEFT JOIN customers c ON t.customer_id = c.id
                    LEFT JOIN task_statuses ts ON t.task_status_id = ts.id
                    LEFT JOIN task_priorities tp ON t.task_priority_id = tp.id
                    WHERE t.user_id = ? AND t.is_done = 0
                    ORDER BY t.created_at ASC
                ";

                $tasks = DB::select($sql_tasks, [$user->id]);

                if (!empty($tasks)) {
                    // Convert stdClass array to associative array
                    $tasksData = array_map(function($item) {
                        return (array) $item;
                    }, $tasks);

                    $payload = [
                'alert_prefix' => $this->option('alert'),
                        'userName' => $userName,
                        'totalTasks' => count($tasksData),
                        'tasks' => $tasksData,
                        'dateDisplay' => Carbon::today('Asia/Kolkata')->format('F j, Y'),
                        'year' => Carbon::now('Asia/Kolkata')->format('Y')
                    ];

                    try {
                        Mail::to($userEmail)->send(new SelfTaskMailReport($payload));
                        $this->info("  ✅ Mail sent successfully to {$userEmail} ({$userName}) - " . count($tasksData) . " task(s).");
                    } catch (\Exception $e) {
                        $this->error("  ❌ Message could not be sent to {$userEmail}. Mailer Error: " . $e->getMessage());
                    }
                } else {
                    $this->info("  ℹ️ No pending tasks for {$userName} ({$userEmail})");
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
