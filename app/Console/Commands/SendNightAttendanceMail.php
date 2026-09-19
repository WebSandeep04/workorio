<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use App\Services\TenantDatabaseService;
use App\Services\AttendanceReportService;
use App\Mail\NightAttendanceReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendNightAttendanceMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:send-night-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send evening attendance summary and monthly breakdown to all active users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting night attendance email generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Night attendance email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $todayStr = Carbon::today('Asia/Kolkata')->format('Y-m-d');
            $startOfMonthStr = Carbon::today('Asia/Kolkata')->startOfMonth()->format('Y-m-d');
            
            // Get users (excluding admins, per the PHP code: role_id != 1 AND e.status = 'active')
            $users = User::where('role_id', '!=', 1)
                ->where('is_attendance', 1)
                ->whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->get();

            if ($users->isEmpty()) {
                $this->info("  No active employees found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            $reportData = [
                'today' => [],
                'monthly' => []
            ];

            // Pre-fetch all attendance for the month for these users
            $attendances = Attendance::with(['movements' => function($q) {
                    $q->orderBy('time', 'ASC');
                }])
                ->whereIn('user_id', $users->pluck('id'))
                ->whereBetween('date', [$startOfMonthStr, $todayStr])
                ->get()
                ->groupBy('user_id');

            // Pre-fetch all approved leaves for the month
            $leaveRequests = LeaveRequest::with('leaveType')->whereIn('user_id', $users->pluck('id'))
                ->where('status', 'approved')
                ->where(function($query) use ($startOfMonthStr, $todayStr) {
                    $query->whereBetween('start_date', [$startOfMonthStr, $todayStr])
                          ->orWhereBetween('end_date', [$startOfMonthStr, $todayStr])
                          ->orWhere(function($q) use ($startOfMonthStr, $todayStr) {
                              $q->where('start_date', '<=', $startOfMonthStr)
                                ->where('end_date', '>=', $todayStr);
                          });
                })->get();
                
            $leavesForService = [];
            foreach ($leaveRequests as $req) {
                $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
                foreach ($period as $dt) {
                    $d = $dt->format('Y-m-d');
                    if ($dt >= new \DateTime($startOfMonthStr) && $dt <= new \DateTime($todayStr)) {
                        if (!isset($leavesForService[$req->user_id])) {
                            $leavesForService[$req->user_id] = [];
                        }
                        $leavesForService[$req->user_id][$d] = ($req->leaveType && !$req->leaveType->is_paid) ? 'LWP' : ($req->is_rh ? 'RH' : ($req->is_sl ? 'SL' : ($req->is_half_day ? 'HD' : 'L')));
                    }
                }
            }
            
            $holidaysData = Holiday::whereBetween('holiday_date', [$startOfMonthStr, $todayStr])
                ->get()
                ->keyBy(function($holiday) {
                    return Carbon::parse($holiday->holiday_date)->format('Y-m-d');
                });
            $holidays = $holidaysData->keys()->toArray();
            
            $reportService = new AttendanceReportService();

            foreach ($users as $user) {
                $userAtts = $attendances->get($user->id, collect());
                $userLeavesArray = $leavesForService[$user->id] ?? [];
                
                $shift = $user->employee->shiftRelation ?? null;

                $dailyBreakdown = $reportService->generateDailyBreakdown(
                    $userAtts,
                    Carbon::parse($startOfMonthStr),
                    Carbon::parse($todayStr),
                    $holidays,
                    $userLeavesArray,
                    $holidaysData, $user);

                if (empty($dailyBreakdown)) {
                    continue;
                }
                
                $todayRow = collect($dailyBreakdown)->last();
                
                $todayAtt = $userAtts->firstWhere('date', $todayRow['date']);
                $firstMovement = $todayAtt ? $todayAtt->movements->first() : null;
                $mode = $firstMovement ? ($firstMovement->mode ?? '-') : '-';
                $place = $firstMovement ? ($firstMovement->place ?? '-') : '-';

                $todayData = [
                    'user_name' => $user->name,
                    'status' => $todayRow['status'],
                    'punch_in' => $todayRow['first_in'],
                    'punch_out' => $todayRow['last_out'],
                    'mode' => $mode,
                    'place' => $place,
                    'total_hours' => $this->formatHoursMinutes($todayRow['hours']),
                    'late_reason' => $todayRow['late_reason'],
                    'late_by' => $todayRow['late_by'],
                    'status_reason' => $todayRow['status_reason']
                ];
                
                $monthlyRecords = [];
                $monthlyOfficeTotalMinutes = 0;
                
                foreach ($dailyBreakdown as $day) {
                    // Include if they had attendance, were absent on a working day (or holiday working/absent), leave, or holiday
                    if ($day['hours'] > 0 || $day['is_leave'] || $day['is_holiday'] || str_contains(strtolower($day['status']), 'absent')) {
                        $dayAtt = $userAtts->firstWhere('date', $day['date']);
                        $dayFirstMov = $dayAtt ? $dayAtt->movements->first() : null;

                        $monthlyRecords[] = [
                            'date' => $day['display_date'],
                            'status' => $day['status'],
                            'punch_in' => $day['first_in'],
                            'punch_out' => $day['last_out'],
                            'mode' => $dayFirstMov ? ($dayFirstMov->mode ?? '-') : '-',
                            'place' => $dayFirstMov ? ($dayFirstMov->place ?? '-') : '-',
                            'total_hours' => $this->formatHoursMinutes($day['hours']),
                            'late_reason' => $day['late_reason'],
                            'late_by' => $day['late_by'],
                            'status_reason' => $day['status_reason']
                        ];
                    }
                    
                    if ($day['hours'] > 0) {
                        $monthlyOfficeTotalMinutes += (int) round($day['hours'] * 60);
                    }
                }
                
                $reportData['today'][] = $todayData;

                $reportData['monthly'][] = [
                    'user_name' => $user->name,
                    'records' => $monthlyRecords,
                    'monthly_total' => $this->formatHoursMinutes($monthlyOfficeTotalMinutes / 60)
                ];
            }

            // Who gets the email? "Send to all users including admin - filter valid emails"
            // where e.status = 'active'
            $recipientEmails = User::where(function($q) {
                     $q->where('is_attendance', 1)->orWhere('role_id', 1);
                })
                ->whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })->whereNotNull('email')->pluck('email')->filter(function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                })->toArray();

            if (empty($recipientEmails)) {
                $this->info("  No valid emails to send the report to in {$tenant->tenant_name}");
                return;
            }

            try {
                Mail::to($recipientEmails)->send(new NightAttendanceReport(
                    $reportData, 
                    $todayStr, 
                    Carbon::parse($startOfMonthStr)->format('F Y'),
                    $this->option('alert')
                ));
                $this->info("  ✅ Night attendance report sent to " . count($recipientEmails) . " users.");
            } catch (\Exception $e) {
                $this->error("  ❌ Mailer Error: " . $e->getMessage());
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }



    private function formatHoursMinutes($decimalHours) {
        if ($decimalHours <= 0) {
            return '0:00 hrs';
        }
        
        $totalMinutes = (int) round($decimalHours * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' hrs';
    }


}
