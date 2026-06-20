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
            $leaveRequests = LeaveRequest::whereIn('user_id', $users->pluck('id'))
                ->where('status', 'approved')
                ->where(function($query) use ($startOfMonthStr, $todayStr) {
                    $query->whereBetween('start_date', [$startOfMonthStr, $todayStr])
                          ->orWhereBetween('end_date', [$startOfMonthStr, $todayStr])
                          ->orWhere(function($q) use ($startOfMonthStr, $todayStr) {
                              $q->where('start_date', '<=', $startOfMonthStr)
                                ->where('end_date', '>=', $todayStr);
                          });
                })->get();
                
            $leavesList = [];
            $leavesForService = [];
            foreach ($leaveRequests as $req) {
                $period = new \DatePeriod(new \DateTime($req->start_date), new \DateInterval('P1D'), (new \DateTime($req->end_date))->modify('+1 day'));
                foreach ($period as $dt) {
                    $d = $dt->format('Y-m-d');
                    if (!isset($leavesList[$req->user_id])) {
                        $leavesList[$req->user_id] = collect();
                        $leavesForService[$req->user_id] = [];
                    }
                    $leavesList[$req->user_id]->push((object)['date' => \Carbon\Carbon::parse($d)]);

                    $type = 'L';
                    if ($req->is_half_day) $type = 'HD';
                    if ($req->is_sl) $type = 'SL';
                    if ($req->type === 'restricted_holiday') $type = 'RH';
                    $leavesForService[$req->user_id][$d] = $type;
                }
            }
            $leaves = collect($leavesList);
            
            $holidays = Holiday::whereBetween('holiday_date', [$startOfMonthStr, $todayStr])->pluck('holiday_date')->toArray();
            $reportService = new AttendanceReportService();

            foreach ($users as $user) {
                $userAtts = $attendances->get($user->id, collect())->keyBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });
                $userLeaves = $leaves->get($user->id, collect())->keyBy(function($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });

                $shift = $user->employee->shiftRelation ?? null;

                $getRealStatus = function($date, $hours = 0) use ($shift, $holidays, $leavesForService, $user, $reportService) {
                    $dayName = Carbon::parse($date)->format('l');
                    $isWeeklyOff = false;
                    $isHalfDayWorking = false;
                    if ($shift) {
                        if ($shift->week_offs && is_array($shift->week_offs)) {
                            $isWeeklyOff = in_array(date('w', strtotime($dayName)), $shift->week_offs);
                        }
                        if ($shift->half_days && is_array($shift->half_days)) {
                            $isHalfDayWorking = in_array(date('w', strtotime($dayName)), $shift->half_days);
                        }
                    }
                    $isHoliday = in_array($date, $holidays);
                    $leaveType = $leavesForService[$user->id][$date] ?? null;
                    $hasHalfDayLeave = ($leaveType === 'HD');
                    $shortLeaveHr = ($leaveType === 'SL' && $shift) ? (float)($shift->sl_end_limit ?? 0) : 0;
                    
                    [$fullDayHr, $halfDayHr] = $reportService->getThresholds($shift);
                    if ($isHalfDayWorking) {
                        $fullDayHr = $halfDayHr;
                        $halfDayHr = $halfDayHr / 2;
                    }
                    
                    $statusInfo = $reportService->determineStatus($date, $hours, $fullDayHr, $halfDayHr, $isWeeklyOff, $isHoliday, $leaveType, $hasHalfDayLeave, $shortLeaveHr);
                    return $statusInfo['label'] ?? 'absent';
                };

                // 1. Today's Summary
                $todayAtt = $userAtts->get($todayStr);
                if ($todayAtt) {
                    $dayData = $this->formatAttendanceDay($user, $todayAtt, $todayStr);
                    $hours = $reportService->calculateTotalHours($todayAtt->movements, $shift, $todayStr);
                    $dayData['status'] = $getRealStatus($todayStr, $hours);
                    $reportData['today'][] = $dayData;
                } else {
                    $isOnLeave = $userLeaves->has($todayStr);
                    $reportData['today'][] = [
                        'user_name' => $user->name,
                        'status' => $isOnLeave ? $getRealStatus($todayStr, 0) : 'absent',
                        'punch_in' => '-',
                        'punch_out' => '-',
                        'mode' => '-',
                        'place' => '-',
                        'total_hours' => '-',
                        'late_reason' => '-'
                    ];
                }

                // 2. Monthly Breakdown
                if ($userAtts->isEmpty() && $userLeaves->isEmpty()) {
                    continue;
                }

                $allDates = $userAtts->keys()->merge($userLeaves->keys())->unique()->sort()->values();
                
                $monthlyRecords = [];
                $monthlyOfficeTotalMinutes = 0;

                foreach ($allDates as $date) {
                    $isLeave = $userLeaves->has($date) && !$userAtts->has($date);
                    if ($isLeave) {
                        $monthlyRecords[] = [
                            'date' => Carbon::parse($date)->format('M j, Y'),
                            'status' => $getRealStatus($date, 0),
                            'punch_in' => '-',
                            'punch_out' => '-',
                            'mode' => '-',
                            'place' => '-',
                            'total_hours' => '-',
                            'late_reason' => '-'
                        ];
                    } else if ($userAtts->has($date)) {
                        $att = $userAtts->get($date);
                        $dayData = $this->formatAttendanceDay($user, $att, $date);
                        $hours = $reportService->calculateTotalHours($att->movements, $shift, $date);
                        $dayData['status'] = $getRealStatus($date, $hours);
                        $monthlyRecords[] = $dayData;
                        $monthlyOfficeTotalMinutes += $dayData['raw_office_minutes'];
                    }
                }

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

    private function formatAttendanceDay($user, $attendance, $date)
    {
        $movements = $attendance->movements;
        
        $firstMovement = $movements->first();
        $mode = $firstMovement ? ($firstMovement->mode ?? '-') : '-';
        $place = $firstMovement ? ($firstMovement->place ?? '-') : '-';

        // Get late reason from first 'in' (office or field)
        $firstIn = $movements->first(function ($m) {
            return in_array($m->movement_type, ['office', 'field']) && $m->movement_action === 'in';
        });
        $desc = $firstIn->description ?? null;
        $lateReason = '-';
        if (!empty($desc)) {
            $prefix = "Late punch-in: ";
            if (stripos($desc, $prefix) === 0) {
                $lateReason = trim(substr($desc, strlen($prefix)));
            } else {
                $lateReason = trim($desc);
            }
        }

        // Calculate 'office' hours and bounds
        $officeMovements = $movements->filter(function($m) { return $m->movement_type === 'office'; });
        
        $firstOfficeIn = $officeMovements->filter(function($m) { return $m->movement_action === 'in'; })->first();
        $lastOfficeOut = $officeMovements->filter(function($m) { return $m->movement_action === 'out'; })->last();
        
        $punchInIST = 'Not Marked';
        if ($firstOfficeIn) {
            $punchInIST = Carbon::parse($firstOfficeIn->time, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
        }

        $punchOutIST = 'Not Marked';
        if ($lastOfficeOut) {
            $punchOutIST = Carbon::parse($lastOfficeOut->time, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
        }

        $officeMinutes = 0;
        if ($firstOfficeIn) {
            $start = Carbon::parse($firstOfficeIn->time, 'UTC');
            // If they haven't punched out yet, the PHP script logic treated it as 0. 
            // So we'll mirror that: must have both punch In and Out.
            if ($lastOfficeOut) {
                $end = Carbon::parse($lastOfficeOut->time, 'UTC');
                if ($end->greaterThan($start)) {
                    $officeMinutes = $start->diffInMinutes($end);
                }
            }
        }

        return [
            'date' => Carbon::parse($date)->format('M j, Y'),
            'user_name' => $user->name,
            'status' => 'present',
            'punch_in' => $punchInIST,
            'punch_out' => $punchOutIST,
            'mode' => $mode,
            'place' => $place,
            'total_hours' => $this->formatHoursMinutes($officeMinutes / 60),
            'raw_office_minutes' => $officeMinutes,
            'late_reason' => $lateReason
        ];
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
