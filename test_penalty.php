<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Services\TenantDatabaseService::setDefaultConnection(4);
$summary = \App\Models\MonthlyAttendanceSummary::where('employee_id', 10)->where('month', 8)->first();
$employee = \App\Models\Employee::with('shiftHistory', 'shiftRelation')->find(10);
$shift = $employee ? $employee->getShiftForDate(\Carbon\Carbon::create(2026, 8)->endOfMonth()) : null;

echo json_encode([
    'late_count' => $summary->late_count ?? null,
    'shift' => $shift ? $shift->only(['id', 'name', 'penalty_eligible_days', 'penalty_deduction_days']) : null
]);
