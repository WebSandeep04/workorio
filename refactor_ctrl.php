<?php

$file = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\AttendanceController.php';
$content = file_get_contents($file);

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
            
            // Delegate all data building to the unified service
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

file_put_contents($file, $content);
echo "AttendanceController _fetchMonthlyReportData updated!\n";
