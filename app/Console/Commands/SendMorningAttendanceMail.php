<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\TenantDatabaseService;
use App\Mail\MorningAttendanceReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendMorningAttendanceMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-morning-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send morning attendance summary email to all users across all tenants at 10:30 AM.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting morning attendance email generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Morning attendance email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');
            
            // Fetch users (exclude role_id = 1 usually means super admin without attendance in tenant)
            // But per request "Send to all users (including admins) - filter valid emails"
            // And data excludes role 1 
            // So we'll fetch data for all users mapped to an active employee where role_id != 1
            $users = User::where('role_id', '!=', 1)
                ->whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->get();

            if ($users->isEmpty()) {
                $this->info("  No active employees found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            $reportData = [];

            foreach ($users as $user) {
                // Get the first movement of today for this user
                $attendance = Attendance::with(['movements' => function ($query) {
                        $query->orderBy('time', 'ASC');
                    }])
                    ->where('user_id', $user->id)
                    ->where('date', $today)
                    ->first();

                $firstMovement = $attendance && $attendance->movements->isNotEmpty() 
                                 ? $attendance->movements->first() 
                                 : null;

                $isOnLeave = false;
                if (!$firstMovement) {
                    // Check if user is on approved leave today
                    $isOnLeave = LeaveRequest::where('user_id', $user->id)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where('status', 'approved')
                        ->exists();
                }

                $reportData[] = [
                    'user' => $user,
                    'first_movement' => $firstMovement,
                    'is_on_leave' => $isOnLeave,
                ];
            }

            // Who gets the email? "Send to all active users - filter valid emails"
            $recipientEmails = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })->whereNotNull('email')->pluck('email')->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })->toArray();

            if (empty($recipientEmails)) {
                $this->info("  No valid emails to send the report to in {$tenant->tenant_name}");
                return;
            }

            try {
                Mail::to($recipientEmails)->send(new MorningAttendanceReport($reportData, $today, $this->option('alert')));
                $this->info("  ✅ Morning attendance report sent to " . count($recipientEmails) . " users.");
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
