<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear the log file
file_put_contents(storage_path('logs/laravel.log'), '');

\App\Services\TenantDatabaseService::setDefaultConnection(4);

$summary = \App\Models\MonthlyAttendanceSummary::where('is_locked', 1)->first();
if ($summary) {
    echo "Running for Month: " . $summary->month . ", Year: " . $summary->year . "\n";
    app(\App\Services\PayrollCalculationService::class)->processPayroll($summary->month, $summary->year);
} else {
    echo "No locked summary found for emp 7\n";
}
