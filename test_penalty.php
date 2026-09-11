<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Services\TenantDatabaseService::setDefaultConnection(4);
$summary = \App\Models\MonthlyAttendanceSummary::where('employee_id', 10)->where('month', 8)->first();

echo json_encode($summary ? $summary->toArray() : []);
