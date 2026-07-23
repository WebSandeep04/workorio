<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Services\TenantDatabaseService::setDefaultConnection(4);

$salaries = \App\Models\EmployeeSalary::with('structure')->get();
echo "Found " . $salaries->count() . " EmployeeSalary records:\n";
foreach ($salaries as $s) {
    echo "Emp ID: {$s->employee_id}, Effective From: {$s->effective_from}, Has Structure: " . ($s->structure ? 'Yes' : 'No') . "\n";
}

$summaries = \App\Models\MonthlyAttendanceSummary::where('month', 6)->where('year', 2026)->where('is_locked', true)->pluck('employee_id')->toArray();
echo "\nLocked Summaries for Month 6, Year 2026: " . implode(', ', $summaries) . "\n";
