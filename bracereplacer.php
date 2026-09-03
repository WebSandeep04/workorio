<?php

function replaceMethodBody($file, $methodStart, $newBody) {
    $lines = file($file);
    $out = [];
    $in_func = false;
    $braceCount = 0;
    $startedBrace = false;
    
    foreach ($lines as $i => $line) {
        if (!$in_func && strpos($line, $methodStart) !== false) {
            $in_func = true;
            $out[] = rtrim($line) . "\n";
            continue;
        }
        
        if (!$in_func) {
            $out[] = $line;
        } else {
            $opens = substr_count($line, '{');
            $closes = substr_count($line, '}');
            
            if ($opens > 0) {
                $startedBrace = true;
            }
            
            $braceCount += $opens;
            $braceCount -= $closes;
            
            if ($startedBrace && $braceCount === 0) {
                $out[] = "    {\n" . $newBody . "\n    }\n";
                $in_func = false;
                $startedBrace = false;
            }
        }
    }
    file_put_contents($file, implode("", $out));
}

$monthly = <<<EOD
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

        \$userIdsWithAttendance = \App\Models\Attendance::whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->pluck('user_id')
            ->unique()
            ->toArray();

        \$users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function(\$query) use (\$userIdsWithAttendance) {
                \$query->whereHas('employee', function(\$q) {
                    \$q->where('status', 'active');
                })->orWhereIn('id', \$userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        \$allAttendances = \App\Models\Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');

        \$holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function(\$item) {
                return \$item->holiday_date->format('Y-m-d');
            });
        \$holidays = \$holidaysData->keys()->toArray();

        \$allLeavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
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
        
        return [
            'month' => [
                'display' => \$startDate->format('F Y'),
                'value' => \$month
            ],
            'dates' => \$dates,
            'users' => \$usersData
        ];
EOD;

$userM = <<<EOD
        \$startDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->startOfMonth();
        \$endDate = \Carbon\Carbon::createFromFormat('Y-m', \$month)->endOfMonth();

        \$user = \App\Models\User::with(['employee.shiftHistory.shift'])->find(\$userId);
        if (!\$user || !\$user->is_attendance) {
            return null;
        }

        \$attendances = \App\Models\Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->where('user_id', \$userId)
            ->whereBetween('date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get();

        \$holidaysData = \App\Models\Holiday::whereBetween('holiday_date', [\$startDate->format('Y-m-d'), \$endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function(\$holiday) {
                return \$holiday->holiday_date->format('Y-m-d');
            });
        \$holidays = \$holidaysData->keys()->toArray();

        \$leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('user_id', \$userId)
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

        return [
            'user' => [
                'id' => \$user->id,
                'name' => \$user->name,
                'designation' => \$user->employee->designation->name ?? 'N/A'
            ],
            'daily_status' => \$dailyData,
            'summary' => \$summary
        ];
EOD;

$dateM = <<<EOD
        \$dateObj = \Carbon\Carbon::parse(\$date);
        \$dateStr = \$dateObj->format('Y-m-d');

        \$userIdsWithAttendance = \App\Models\Attendance::where('date', \$dateStr)
            ->pluck('user_id')
            ->unique()
            ->toArray();

        \$users = \App\Models\User::with(['employee.shiftHistory.shift'])
            ->where('role_id', '!=', 1)
            ->where('is_attendance', 1)
            ->where(function(\$query) use (\$userIdsWithAttendance) {
                \$query->whereHas('employee', function(\$q) {
                    \$q->where('status', 'active');
                })->orWhereIn('id', \$userIdsWithAttendance);
            })
            ->orderBy('name')
            ->get();

        \$attendances = \App\Models\Attendance::with(['movements' => function(\$query) {
                \$query->orderBy('time');
            }])
            ->where('date', \$dateStr)
            ->get()
            ->groupBy('user_id');

        \$holidaysData = \App\Models\Holiday::where('holiday_date', \$dateStr)->get()->keyBy(function(\$holiday) {
            return \$holiday->holiday_date->format('Y-m-d');
        });
        \$holidays = \$holidaysData->keys()->toArray();

        \$leavesRaw = \App\Models\LeaveRequest::with('leaveType')->where('status', 'approved')
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

        return [
            'date' => \$dateObj->format('M j, Y'),
            'summary' => [
                'total_working' => count(\$users),
                'total_present' => collect(\$reportData)->whereIn('data.code', ['P', 'P2', 'W/O-W', 'H/W'])->count(),
                'total_absent' => collect(\$reportData)->where('data.code', 'A')->count(),
                'total_leaves' => collect(\$reportData)->whereIn('data.code', ['L', 'HD', 'SL', 'RH', 'LWP'])->count(),
                'total_na' => collect(\$reportData)->where('data.code', 'NA')->count()
            ],
            'users' => \$reportData
        ];
EOD;

$ctrl = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\AttendanceController.php';
replaceMethodBody($ctrl, 'private function _fetchMonthlyReportData', $monthly);
replaceMethodBody($ctrl, 'private function _fetchUserReportData', $userM);
replaceMethodBody($ctrl, 'private function _fetchDateReportData', $dateM);

$apiCtrl = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\Api\AttendanceReportApiController.php';
replaceMethodBody($apiCtrl, 'private function _fetchMonthlyReportData', $monthly);
replaceMethodBody($apiCtrl, 'private function _fetchUserReportData', $userM);
replaceMethodBody($apiCtrl, 'private function _fetchDateReportData', $dateM);

echo "Success!\n";

