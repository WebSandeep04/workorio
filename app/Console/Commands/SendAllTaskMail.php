<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\AllTaskMailReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendAllTaskMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:send-all-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily summary of all pending tasks grouped by user.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting All Tasks mail generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('All Tasks email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Determine recipient emails.
            $hasIsTask = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_task');
            
            $recipientEmails = [];
            if ($hasIsTask) {
                $recipientEmails = User::whereHas('employee', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('is_task', 1)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
            }

            $recipientEmails = array_filter($recipientEmails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            if (empty($recipientEmails)) {
                $this->info("  ⚠️ No active recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            $sql_tasks = "
            SELECT 
                u.id AS user_id,
                u.name AS user_name,
                u.email AS user_email,
                t.id AS task_id,
                t.task_name,
                t.task_type,
                c.company_name AS customer_company,
                ts.name AS status_name,
                ts.color AS status_color,
                tp.name AS priority_name,
                tp.color AS priority_color,
                t.created_at,
                creator.name AS created_by_name,
                (
                    SELECT r.remark
                    FROM task_remarks r
                    WHERE r.task_id = t.id
                    ORDER BY r.id DESC
                    LIMIT 1
                ) AS latest_remark,
                (
                    SELECT r.created_at
                    FROM task_remarks r
                    WHERE r.task_id = t.id
                    ORDER BY r.id DESC
                    LIMIT 1
                ) AS latest_remark_date
            FROM tasks t
            INNER JOIN users u ON t.user_id = u.id
            JOIN employees e ON u.employee_id = e.id AND e.status = 'active'
            LEFT JOIN customers c ON t.customer_id = c.id
            LEFT JOIN task_statuses ts ON t.task_status_id = ts.id
            LEFT JOIN task_priorities tp ON t.task_priority_id = tp.id
            LEFT JOIN users creator ON t.created_by = creator.id
            WHERE t.is_done = 0
            ORDER BY u.name ASC, t.created_at ASC";

            $results = DB::select($sql_tasks);

            if (empty($results)) {
                $this->info("  No pending tasks found for {$tenant->tenant_name}.");
                return;
            }

            $tasksByUser = [];
            $totalTasks = 0;

            foreach ($results as $row) {
                $row = (array) $row;
                $uid = $row['user_id'];
                if (!isset($tasksByUser[$uid])) {
                    $tasksByUser[$uid] = [
                        'user_name'  => $row['user_name'],
                        'user_email' => $row['user_email'],
                        'tasks'      => []
                    ];
                }
                $tasksByUser[$uid]['tasks'][] = $row;
                $totalTasks++;
            }

            $totalUsers = count($tasksByUser);

            $payload = [
                'alert_prefix' => $this->option('alert'),
                'tasksByUser' => $tasksByUser,
                'totalTasks' => $totalTasks,
                'totalUsers' => $totalUsers,
                'dateDisplay' => Carbon::today('Asia/Kolkata')->format('F j, Y'),
                'timeDisplay' => Carbon::now('Asia/Kolkata')->format('F j, Y - g:i A'),
            ];

            try {
                Mail::to($recipientEmails)->send(new AllTaskMailReport($payload));
                $this->info("  ✅ All tasks report sent to " . count($recipientEmails) . " users.");
            } catch (\Exception $e) {
                $this->error("  ❌ Mailer Error: " . $e->getMessage());
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
