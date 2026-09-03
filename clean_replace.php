<?php

$files = [
    'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\AttendanceController.php',
    'd:\DontDelete\laravel\leadmanagement (akrati ui work)\app\Http\Controllers\Api\AttendanceReportApiController.php'
];

foreach ($files as $file) {
    $c = file_get_contents($file);
    
    // Replace _fetchMonthlyReportData
    $pattern = '/private function _fetchMonthlyReportData\(.*?\{.*?\n    \}(?=\n\n    private function |$)/ms';
    // wait, regex for an entire function is hard.
}
