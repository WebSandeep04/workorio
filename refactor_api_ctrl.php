<?php

$file = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\Api\AttendanceReportApiController.php';
$content = file_get_contents($file);

// Replace _fetchMonthlyReportData
$startStr = 'private function _fetchMonthlyReportData($month)';
$endStr = 'return [';

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos);

$newMethod = <<<EOD
    private function _fetchMonthlyReportData(\$month)
    {
        \$startDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->startOfMonth();
        \$endDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->endOfMonth();

        \$dates = [];
        \$curr = \$startDate->copy();
        while (\$curr->lte(\$endDate)) {
            \$dates[] = [
                'date' => \$curr->format('Y-m-d'),
                'day' => \$curr->format('d'),
                'day_name' => \$curr->format('D'),
                'is_sunday' => \$curr->dayOfWeek === \Carbon\Carbon::SUNDAY
            ];
            \$curr->addDay();
        }

        // Get all user IDs who have attendance records in the selected month
        \$userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
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

        \$allAttendances = Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        \$holidaysData = Holiday::whereBetween('holiday_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function(\$item) {
                return \$item->holiday_date->format('Y-m-d');
            });
        \$holidays = \$holidaysData->keys()->toArray();

        \$allLeavesRaw = LeaveRequest::with('leaveType')->where('status', 'approved')
            ->where(function(\$query) use (\$startDate, \$endDate) {
                \$query->whereBetween('start_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
                      ->orWhere(function(\$q) use (\$startDate, \$endDate) {
                          \$q->where('start_date', '<=', \$startDate->format('Y-m-d'))
                            ->where('end_date', '>=', \$endDate->format('Y-m-d'));
                      })
                      ->orWhereBetween('end_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')]);
            })->get();

        \$userLeaves = [];
        foreach (\$allLeavesRaw as \$leave) {
            \$lStart = \Carbon\Carbon::parse(\$leave->start_date)->max(\$startDate);
            \$lEnd = \Carbon\Carbon::parse(\$leave->end_date)->min(\$endDate);
            
            \$lType = 'L';
            if (\$leave->is_half_day) \$lType = 'HD';
            elseif (\$leave->is_sl) \$lType = 'SL';
            elseif (\$leave->is_rh) \$lType = 'RH';
            elseif (\$leave->leaveType && strtolower(\$leave->leaveType->name) === 'lwp') \$lType = 'LWP';
            
            \$currL = \$lStart->copy();
            while (\$currL->lte(\$lEnd)) {
                \$userLeaves[\$leave->user_id][\$currL->format('Y-m-d')] = \$lType;
                \$currL->addDay();
            }
        }

        \$usersData = [];
        foreach (\$users as \$user) {
            \$userAttendances = \$allAttendances->get(\$user->id, collect());
            \$userLeavesDetails = \$userLeaves[\$user->id] ?? [];
            
            \$dailyData = \$this->reportService->generateDailyBreakdown(\$userAttendances, \$startDate, \$endDate, \$holidays, \$userLeavesDetails, \$holidaysData, \$user);
            
            \$dailyStatuses = [];
            foreach (\$dailyData as \$d) {
                \$dailyStatuses[] = [
                    'date' => \$d['date'],
                    'code' => \$d['code'],
                    'class' => \$d['class']
                ];
            }
            
            \$leavesList = array_map(function(\$k, \$v) { return \$v; }, array_keys(\$userLeavesDetails), \$userLeavesDetails);
            \$formattedLeavesForSummary = array_combine(array_keys(\$userLeavesDetails), \$leavesList);

            \$summary = \$this->reportService->calculateMonthlySummary(\$userAttendances, \$startDate, \$endDate, \$holidays, \$formattedLeavesForSummary, \$holidaysData, \$user);

            \$usersData[] = [
                'user' => [
                    'id' => \$user->id,
                    'name' => \$user->name,
                    'designation' => \$user->employee->designation->name ?? 'N/A'
                ],
                'daily_status' => \$dailyStatuses,
                'summary' => \$summary
            ];
        }

        
EOD;

$content = substr($content, 0, $startPos) . $newMethod . substr($content, $endPos);

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
echo "AttendanceReportApiController updated!\n";
