<?php

$file = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\AttendanceController.php';
$content = file_get_contents($file);

// 1. Refactor _fetchUserReportData
$startStrUser = 'private function _fetchUserReportData($userId, $month)';
$endStrUser = 'return [';

$startPosUser = strpos($content, $startStrUser);
$endPosUser = strpos($content, $endStrUser, $startPosUser);

$newMethodUser = <<<EOD
    private function _fetchUserReportData(\$userId, \$month)
    {
        \$startDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->startOfMonth();
        \$endDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->endOfMonth();

        \$user = User::with(['employee.shiftHistory.shift'])->find(\$userId);
        if (!\$user || !\$user->is_attendance) {
            return null;
        }

        \$attendances = Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->where('user_id', \$userId)
            ->whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get();

        \$holidaysData = Holiday::whereBetween('holiday_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function(\$holiday) {
                return \$holiday->holiday_date->format('Y-m-d');
            });
        \$holidays = \$holidaysData->keys()->toArray();

        \$leavesRaw = LeaveRequest::with('leaveType')->where('user_id', \$userId)
            ->where('status', 'approved')
            ->where(function(\$query) use (\$startDate, \$endDate) {
                \$query->whereBetween('start_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
                      ->orWhere(function(\$q) use (\$startDate, \$endDate) {
                          \$q->where('start_date', '<=', \$startDate->format('Y-m-d'))
                            ->where('end_date', '>=', \$endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')]);
            })->get();

        \$leaves = [];
        foreach (\$leavesRaw as \$leave) {
            \$lStart = \Carbon\Carbon::parse(\$leave->start_date)->max(\$startDate);
            \$lEnd = \Carbon\Carbon::parse(\$leave->end_date)->min(\$endDate);
            
            \$lType = 'L';
            if (\$leave->is_half_day) \$lType = 'HD';
            elseif (\$leave->is_sl) \$lType = 'SL';
            elseif (\$leave->is_rh) \$lType = 'RH';
            elseif (\$leave->leaveType && strtolower(\$leave->leaveType->name) === 'lwp') \$lType = 'LWP';
            
            \$curr = \$lStart->copy();
            while (\$curr->lte(\$lEnd)) {
                \$leaves[\$curr->format('Y-m-d')] = \$lType;
                \$curr->addDay();
            }
        }

        \$dailyData = \$this->reportService->generateDailyBreakdown(\$attendances, \$startDate, \$endDate, \$holidays, \$leaves, \$holidaysData, \$user);
        
        // Re-format leaves for the summary function
        \$leavesList = array_map(function(\$k, \$v) { return \$v; }, array_keys(\$leaves), \$leaves);
        \$formattedLeavesForSummary = array_combine(array_keys(\$leaves), \$leavesList);

        \$summary = \$this->reportService->calculateMonthlySummary(\$attendances, \$startDate, \$endDate, \$holidays, \$formattedLeavesForSummary, \$holidaysData, \$user);

        
EOD;

$content = substr($content, 0, $startPosUser) . $newMethodUser . substr($content, $endPosUser);

// 2. Refactor _fetchDateReportData
$startStrDate = 'private function _fetchDateReportData($date)';
$endStrDate = 'return $reportData;';

$startPosDate = strpos($content, $startStrDate);
$endPosDate = strpos($content, $endStrDate, $startPosDate) + strlen($endStrDate);

$newMethodDate = <<<EOD
    private function _fetchDateReportData(\$date)
    {
        \$dateObj = \Carbon\Carbon::parse(\$date);
        \$dateStr = \$dateObj->format('Y-m-d');
        \$startOfMonth = \$dateObj->copy()->startOfMonth();

        // Get all user IDs who have attendance records on this date
        \$userIdsWithAttendance = \App\Models\Attendance::where('date', \$dateStr)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        \$users = User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function(\$query) use (\$userIdsWithAttendance) {
                \$query->whereHas('employee', function(\$q) {
                    \$q->where('status', 'active');
                })->orWhereIn('id', \$userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        \$attendances = Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->where('date', \$dateStr)
            ->get()
            ->groupBy('user_id');

        \$holidaysData = Holiday::where('holiday_date', \$dateStr)->get()->keyBy(function(\$holiday) {
            return \$holiday->holiday_date->format('Y-m-d');
        });
        \$holidays = \$holidaysData->keys()->toArray();

        // For late grace logic, we might need attendance from start of month up to this date
        // But since we are purely reading DB values now, we don't need to fetch all month attendances!
        \$leavesRaw = LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function(\$query) use (\$dateStr) {
                \$query->where('start_date', '<=', \$dateStr)
                      ->where('end_date', '>=', \$dateStr);
            })->get();

        \$userLeaves = [];
        foreach (\$leavesRaw as \$leave) {
            \$lType = 'L';
            if (\$leave->is_half_day) \$lType = 'HD';
            elseif (\$leave->is_sl) \$lType = 'SL';
            elseif (\$leave->is_rh) \$lType = 'RH';
            elseif (\$leave->leaveType && strtolower(\$leave->leaveType->name) === 'lwp') \$lType = 'LWP';
            \$userLeaves[\$leave->user_id] = \$lType;
        }

        \$reportData = [];
        foreach (\$users as \$user) {
            \$userAttendance = \$attendances->get(\$user->id, collect());
            
            \$leaveArr = isset(\$userLeaves[\$user->id]) ? [\$dateStr => \$userLeaves[\$user->id]] : [];
            
            \$dailyData = \$this->reportService->generateDailyBreakdown(\$userAttendance, \$dateObj, \$dateObj, \$holidays, \$leaveArr, \$holidaysData, \$user);
            \$dayData = \$dailyData[0];
            
            \$reportData[] = [
                'user' => [
                    'id' => \$user->id,
                    'name' => \$user->name,
                    'designation' => \$user->employee->designation->name ?? 'N/A'
                ],
                'data' => \$dayData
            ];
        }

        return \$reportData;
EOD;

$content = substr($content, 0, $startPosDate) . $newMethodDate . substr($content, $endPosDate);

file_put_contents($file, $content);
echo "AttendanceController _fetchUserReportData and _fetchDateReportData updated!\n";

