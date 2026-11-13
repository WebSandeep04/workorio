<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\PettyOpeningBalance;
use App\Models\PettyCashData;
use App\Services\TenantDatabaseService;
use App\Mail\PettyCashSummaryReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendPettyCashSummaryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pettycash:send-summary-report 
                            {--email= : Send only to a specific email override for testing}
                            {--tenant_id= : Process a specific tenant ID}
                            {--days=10 : Number of past days for recent entries table}
                            {--alert= : Alert prefix for email subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send petty cash summary report (department-wise opening balance, expenses, remaining + last 10 days table) to admin role_id = 1.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Petty Cash Summary Report generation...');

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

        $this->info('Petty Cash Summary Report processing finished.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Determine recipient list
            $overrideEmail = $this->option('email');
            $recipients = [];

            if ($overrideEmail) {
                $recipients[] = [
                    'email' => $overrideEmail,
                    'name' => 'Testing Admin'
                ];
                $this->line("  [Test Mode] Overriding recipients with: {$overrideEmail}");
            } else {
                // Production: All active admins with role_id = 1
                $adminUsers = User::whereHas('employee', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('role_id', 1)
                    ->whereNotNull('email')
                    ->get();

                foreach ($adminUsers as $user) {
                    if (!empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = [
                            'email' => $user->email,
                            'name' => $user->name
                        ];
                    }
                }
            }

            if (empty($recipients)) {
                $this->info("  ⚠️ No valid admin recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            // 1. Department-wise summary (Opening Balance, Total Expense, Remaining)
            $departments = Department::all();
            $departmentSummary = [];
            $overallOpeningBalance = 0;
            $overallTotalExpense = 0;

            $days = (int) ($this->option('days') ?: 10);
            $startDate = Carbon::today('Asia/Kolkata')->subDays($days - 1)->startOfDay();
            $endDate = Carbon::today('Asia/Kolkata')->endOfDay();

            foreach ($departments as $dept) {
                $op = (float) PettyOpeningBalance::where('department_id', $dept->id)->sum('amount');
                $exp = (float) PettyCashData::where('department_id', $dept->id)->sum('price');
                $rem = $op - $exp;

                $lastDeptOb = PettyOpeningBalance::where('department_id', $dept->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastDeptTopUp = null;
                if ($lastDeptOb) {
                    $lastDeptTopUp = [
                        'amount' => (float) $lastDeptOb->amount,
                        'date' => $lastDeptOb->created_at ? $lastDeptOb->created_at->format('d M Y') : '-'
                    ];
                }

                $deptEntries = PettyCashData::with(['expense'])
                    ->where('department_id', $dept->id)
                    ->where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate)
                    ->orderBy('created_at', 'desc')
                    ->get();

                $deptRecent = [];
                $deptRecentTotal = 0;
                foreach ($deptEntries as $e) {
                    $deptRecentTotal += (float) $e->price;
                    $deptRecent[] = [
                        'id' => $e->id,
                        'date' => $e->created_at ? $e->created_at->format('d M Y') : '-',
                        'expense_name' => $e->expense ? $e->expense->name : 'N/A',
                        'price' => (float) $e->price,
                        'remark' => $e->remark,
                        'is_approved' => (bool) $e->is_approved,
                    ];
                }

                $overallOpeningBalance += $op;
                $overallTotalExpense += $exp;

                $departmentSummary[] = [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'opening_balance' => $op,
                    'total_expense' => $exp,
                    'remaining_balance' => $rem,
                    'last_top_up' => $lastDeptTopUp,
                    'recent_entries' => $deptRecent,
                    'recent_total_price' => $deptRecentTotal
                ];
            }

            $overallRemainingBalance = $overallOpeningBalance - $overallTotalExpense;

            // 1b. Overall Last Opening Balance (Last Top-Up)
            $lastOpeningBalance = PettyOpeningBalance::orderBy('created_at', 'desc')->first();
            $lastTopUp = null;
            if ($lastOpeningBalance) {
                $lastTopUp = [
                    'amount' => (float) $lastOpeningBalance->amount,
                    'date' => $lastOpeningBalance->created_at ? $lastOpeningBalance->created_at->format('d M Y') : '-'
                ];
            }

            // 2. All Last N days complete entries (for global reference if needed)
            $entries = PettyCashData::with(['department', 'expense'])
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->orderBy('created_at', 'desc')
                ->get();

            $recentEntries = [];
            $recentTotalPrice = 0;

            foreach ($entries as $e) {
                $recentTotalPrice += (float) $e->price;
                $recentEntries[] = [
                    'id' => $e->id,
                    'date' => $e->created_at ? $e->created_at->format('d M Y') : '-',
                    'department' => $e->department ? $e->department->name : '-',
                    'expense_name' => $e->expense ? $e->expense->name : 'N/A',
                    'price' => (float) $e->price,
                    'remark' => $e->remark,
                    'is_approved' => (bool) $e->is_approved,
                ];
            }

            $payload = [
                'tenant_name' => $tenant->tenant_name,
                'alert_prefix' => $this->option('alert'),
                'date_display' => Carbon::now('Asia/Kolkata')->format('d M Y, h:i A'),
                'start_date' => $startDate->format('d M Y'),
                'end_date' => $endDate->format('d M Y'),
                'department_summary' => $departmentSummary,
                'overall_opening_balance' => $overallOpeningBalance,
                'overall_total_expense' => $overallTotalExpense,
                'overall_remaining_balance' => $overallRemainingBalance,
                'last_top_up' => $lastTopUp,
                'recent_entries' => $recentEntries,
                'recent_total_price' => $recentTotalPrice,
            ];

            // 3. Send email to each recipient
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient['email'], $recipient['name'])->send(new PettyCashSummaryReportMail($payload));
                    $this->line("  ✅ Petty Cash report email sent successfully to {$recipient['email']}");
                } catch (Exception $e) {
                    $this->error("  ❌ Failed to send mail to {$recipient['email']}. Error: " . $e->getMessage());
                }
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}

