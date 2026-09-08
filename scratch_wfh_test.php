<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\AttendanceReportService::class);
$shift = \App\Models\Shift::first();

// Create a dummy attendance to test
$attendance = \App\Models\Attendance::first();
if ($attendance) {
    echo "Testing computeAndSaveDailyStatus with is_wfh = true\n";
    $attendance->is_wfh = 1;
    
    // Assume 9 hours worked (should be present)
    $service->computeAndSaveDailyStatus($attendance);
    
    echo "Final Status: " . $attendance->computed_status . "\n";
    echo "Reason: " . $attendance->status_reason . "\n";
} else {
    echo "No attendance records found to test.";
}
