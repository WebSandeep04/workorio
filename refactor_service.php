<?php

$file = 'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Services\AttendanceReportService.php';
$content = file_get_contents($file);

// Let's replace generateDailyBreakdown completely.
$startStr = 'public function generateDailyBreakdown($attendances, $startDate, $endDate, $holidays, $leaves, $holidaysData = null, $user = null)';
$endStr = 'return $dailyData;' . "\n" . '    }';

$startPos = strpos($content, $startStr);
$endPos = strpos($content, $endStr, $startPos) + strlen($endStr);

$newMethod = <<<EOD
public function generateDailyBreakdown(\$attendances, \$startDate, \$endDate, \$holidays, \$leaves, \$holidaysData = null, \$user = null)
    {
        \$dailyData = [];
        \$attendanceByDate = \$attendances->keyBy(function (\$item) {
            return \Carbon\Carbon::parse(\$item->date)->format('Y-m-d');
        });
        
        \$currentDate = \$startDate->copy();
        while (\$currentDate->lte(\$endDate)) {
            \$dateStr = \$currentDate->format('Y-m-d');
            \$dayName = \$currentDate->format('l');
            \$displayDate = \$currentDate->format('M j, Y');
            
            \$isWeeklyOff = false;
            \$isHalfDayWorking = false;
            \$shift = \$user && \$user->employee ? \$user->employee->getShiftForDate(\$dateStr) : null;
            if (\$shift) {
                if (\$shift->week_offs && is_array(\$shift->week_offs)) {
                    \$isWeeklyOff = in_array(date('w', strtotime(\$dayName)), \$shift->week_offs);
                }
                if (\$shift->half_days && is_array(\$shift->half_days)) {
                    \$isHalfDayWorking = in_array(date('w', strtotime(\$dayName)), \$shift->half_days);
                }
            }

            \$dayData = [
                'date' => \$dateStr,
                'display_date' => \$displayDate,
                'day_name' => \$dayName,
                'is_sunday' => \$isWeeklyOff,
                'is_holiday' => in_array(\$dateStr, \$holidays),
                'is_leave' => isset(\$leaves[\$dateStr]),
                'leave_type' => \$leaves[\$dateStr] ?? null,
                'holiday_name' => null,
                'status' => 'NA',
                'status_reason' => '-',
                'code' => 'NA',
                'class' => 'text-secondary',
                'hours' => 0,
                'office_hours' => 0,
                'field_hours' => 0,
                'break_time' => 0,
                'cycles' => ['office' => 0, 'field' => 0, 'break' => 0],
                'first_in' => '-',
                'last_out' => '-',
                'late_by' => '-',
                'grace_balance' => '-',
                'late_reason' => '-',
                'movements' => []
            ];
            
            if (isset(\$attendanceByDate[\$dateStr])) {
                \$attendance = \$attendanceByDate[\$dateStr];
                
                if (!empty(\$attendance->computed_status)) {
                    \$dayData['hours'] = (float) \$attendance->computed_hours;
                    \$dayData['status'] = \$attendance->computed_status;
                    \$dayData['status_reason'] = \$attendance->status_reason;
                } else {
                    \$dayData['hours'] = (float) (\$attendance->computed_hours ?? 0);
                    \$dayData['status'] = 'NA';
                    \$dayData['status_reason'] = '-';
                }
                
                \$dayData['late_minutes'] = (int) (\$attendance->late_minutes ?? 0);
                if (\$dayData['late_minutes'] > 0) {
                    \$dayData['late_by'] = \$dayData['late_minutes'] . ' min';
                }
                
                \$dayData['office_hours'] = \$this->calculateTypeHours(\$attendance->movements, 'office');
                \$dayData['field_hours'] = \$this->calculateTypeHours(\$attendance->movements, 'field');
                \$dayData['break_time'] = \$this->calculateTypeHours(\$attendance->movements, 'break');
                \$dayData['cycles'] = \$this->calculateDayCycles(\$attendance->movements);
                \$dayData['is_wfh'] = \$attendance->is_wfh;
                
                \$firstInMov = \$attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'in')->first();
                if (\$firstInMov) {
                    \$dayData['first_in'] = \Carbon\Carbon::parse(\$firstInMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                \$lastOutMov = \$attendance->movements->whereIn('movement_type', ['office', 'field'])->where('movement_action', 'out')->last();
                if (\$lastOutMov) {
                    \$dayData['last_out'] = \Carbon\Carbon::parse(\$lastOutMov->time)->setTimezone('Asia/Kolkata')->format('H:i');
                }
                
                \$dayData['movements'] = \$attendance->movements->map(function(\$movement) {
                    return [
                        'time' => \Carbon\Carbon::parse(\$movement->time)->setTimezone('Asia/Kolkata')->format('H:i'),
                        'type' => ucfirst(\$movement->movement_type),
                        'action' => ucfirst(\$movement->movement_action),
                        'description' => \$movement->description,
                        'latitude' => \$movement->latitude,
                        'longitude' => \$movement->longitude
                    ];
                })->toArray();
                
                \$descriptions = \$attendance->movements
                    ->map(fn(\$m) => \$m->description ? trim(\$m->description) : null)
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();
                
                \$dayData['description'] = !empty(\$descriptions) ? implode('<br>', \$descriptions) : null;
                
                if (\$dayData['late_minutes'] > 0 && \$firstInMov && \$firstInMov->description) {
                    \$desc = trim(\$firstInMov->description);
                    \$prefix = "Late punch-in: ";
                    if (stripos(\$desc, \$prefix) === 0) {
                        \$dayData['late_reason'] = trim(substr(\$desc, strlen(\$prefix)));
                    } else {
                        \$dayData['late_reason'] = trim(\$desc);
                    }
                }

                if (\$dayData['is_holiday'] && \$holidaysData && isset(\$holidaysData[\$dateStr])) {
                    \$dayData['holiday_name'] = \$holidaysData[\$dateStr]->name;
                }
                
                \$finalLabel = strtolower(\$dayData['status']);
                \$isWfhWowOrHw = false;
                if (\$attendance->is_wfh) {
                    if (str_contains(\$finalLabel, 'working')) {
                        \$isWfhWowOrHw = true;
                    } elseif (str_contains(\$finalLabel, 'present')) {
                        \$finalLabel = 'halfday';
                    }
                }

                if (\$finalLabel === 'na') {
                    \$dayData['code'] = 'NA';
                    \$dayData['class'] = 'text-secondary';
                } elseif (\$finalLabel === 'absent') {
                    \$dayData['code'] = 'A';
                    \$dayData['class'] = 'text-danger';
                } elseif (str_contains(\$finalLabel, 'halfday')) {
                    \$dayData['code'] = 'P2';
                    \$dayData['class'] = 'text-primary';
                } elseif (str_contains(\$finalLabel, 'present')) {
                    \$dayData['code'] = 'P';
                    \$dayData['class'] = 'text-success';
                } elseif (str_contains(\$finalLabel, 'weekly off working')) {
                    \$dayData['code'] = \$isWfhWowOrHw ? 'W/O-W<sub>wfh</sub>' : 'W/O-W';
                    \$dayData['class'] = 'text-success';
                } elseif (str_contains(\$finalLabel, 'holiday working')) {
                    \$dayData['code'] = \$isWfhWowOrHw ? 'H/W<sub>wfh</sub>' : 'H/W';
                    \$dayData['class'] = 'text-success';
                } else {
                    \$dayData['code'] = 'P';
                    \$dayData['class'] = 'text-success';
                }
                
                if (!\$lastOutMov && ((float)(\$attendance->computed_hours ?? 0)) > 0) {
                    \$dayData['code'] = 'A';
                    \$dayData['class'] = 'text-danger';
                }

            } else {
                if (\$isWeeklyOff) {
                    \$dayData['status'] = 'weekly off';
                    \$dayData['code'] = 'S';
                    \$dayData['class'] = 'text-danger small';
                } elseif (in_array(\$dateStr, \$holidays)) {
                    \$dayData['status'] = 'holiday';
                    \$dayData['code'] = 'H';
                    \$dayData['class'] = 'text-secondary';
                    if (\$holidaysData && isset(\$holidaysData[\$dateStr])) {
                        \$dayData['holiday_name'] = \$holidaysData[\$dateStr]->name;
                    }
                } elseif (isset(\$leaves[\$dateStr])) {
                    if (\$leaves[\$dateStr] === 'RH') {
                        \$dayData['status'] = 'restricted holiday';
                        \$dayData['code'] = 'RH';
                        \$dayData['class'] = 'text-primary';
                    } elseif (\$leaves[\$dateStr] === 'SL') {
                        \$dayData['status'] = 'short leave';
                        \$dayData['code'] = 'SL';
                        \$dayData['class'] = 'text-info';
                    } elseif (\$leaves[\$dateStr] === 'LWP') {
                        \$dayData['status'] = 'unpaid leave';
                        \$dayData['code'] = 'LWP';
                        \$dayData['class'] = 'text-danger';
                    } else {
                        \$dayData['status'] = 'leave';
                        \$dayData['code'] = 'L';
                        \$dayData['class'] = 'text-warning';
                    }
                } else {
                    \$dayData['status'] = 'NA';
                    \$dayData['status_reason'] = '-';
                    \$dayData['code'] = 'NA';
                    \$dayData['class'] = 'text-secondary';
                }
            }
            
            \$dailyData[] = \$dayData;
            \$currentDate->addDay();
        }
        
        return \$dailyData;
    }
EOD;

$newContent = substr($content, 0, $startPos) . $newMethod . substr($content, $endPos);
file_put_contents($file, $newContent);
echo "generateDailyBreakdown updated in Service.\n";
