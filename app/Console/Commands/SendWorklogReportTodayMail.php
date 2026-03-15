<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\WorklogReportTodayMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendWorklogReportTodayMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worklog:send-today-mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Send today's worklog summary to HR/Admins.";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Today Worklog Report generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Worklog Report generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Fetch users with is_worklog = 1 OR role_id = 1
            $hasIsWorklog = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_worklog');
            
            $recipientUsers = [];
            if ($hasIsWorklog) {
                $recipientUsers = User::where('is_worklog', 1)
                    ->orWhere('role_id', 1)
                    ->get();
            } else {
                // Fallback to Admins only
                $recipientUsers = User::where('role_id', 1)->get();
            }

            $validRecipients = [];
            foreach ($recipientUsers as $user) {
                if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $validRecipients[] = [
                        'email' => $user->email,
                        'name' => $user->name
                    ];
                }
            }

            if (empty($validRecipients)) {
                $this->info("  ⚠️ No valid email recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');

            // Hardcode fallback just in case testing needs it
            if (empty($validRecipients) && $tenant->id == 1) {
                $validRecipients[] = ['email' => 'sandeep@triserv360.com', 'name' => 'Sandeep'];
                $validRecipients[] = ['email' => 'shamshad@triserv360.com', 'name' => 'Shamshad'];
            }

            $sql = "
            SELECT 
                u.id AS user_id,
                u.name AS user_name,
                w.id AS worklog_id,
                w.work_date,
                w.entry_type_name,
                c.company_name AS company_name,
                w.service_name,
                w.customer_project_name,
                w.module_name,
                w.hours,
                w.minutes,
                w.description,
                wa.status AS approval_status,
                wa.rating AS rating,
                wa.remark AS rating_remark
            FROM users u
            LEFT JOIN worklogs w 
                ON w.user_id = u.id 
               AND w.work_date = ?
            LEFT JOIN customers c
                ON c.id = w.customer_id
            LEFT JOIN (
                SELECT a.*
                FROM worklog_approvals a
                JOIN (
                    SELECT worklog_id, MAX(id) AS max_id
                    FROM worklog_approvals
                    GROUP BY worklog_id
                ) x ON x.worklog_id = a.worklog_id AND x.max_id = a.id
            ) wa
                ON wa.worklog_id = w.id
            WHERE u.is_worklog = 1
            ORDER BY u.name, w.created_at;
            ";

            $results = DB::select($sql, [$today]);

            $worklogs = [];
            if (!empty($results)) {
                $worklogs = array_map(function($item) {
                    return (array) $item;
                }, $results);
            }

            $payload = [
                'worklogs' => $worklogs,
                'dateDisplay' => Carbon::now('Asia/Kolkata')->format('F j, Y'),
                'year' => Carbon::now('Asia/Kolkata')->format('Y')
            ];

            foreach ($validRecipients as $recipient) {
                try {
                    Mail::to($recipient['email'], $recipient['name'])->send(new WorklogReportTodayMail($payload));
                    $this->line("  ✅ Mail sent to {$recipient['email']}");
                } catch (\Exception $e) {
                    $this->error("  ❌ Message could not be sent. Mailer Error: " . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
