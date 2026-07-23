<?php
$file = __DIR__.'/app/Services/PayrollCalculationService.php';
$content = file_get_contents($file);

// Replace multi-line if blocks for employeeId == 7
$content = preg_replace('/if \(\$employeeId == 7\) \{\s*(\\\\Log::.*?)\s*\}/s', '$1', $content);

// Replace single-line if blocks for employeeId == 7
$content = preg_replace('/if \(\$employeeId == 7\) (\\\\Log::.*?);/', '$1;', $content);

// Replace "EMP ID: 7" with "EMP ID: {$employeeId}"
$content = str_replace('EMP ID: 7', 'EMP ID: {$employeeId}', $content);

// Replace "EMP ID 7" with "EMP ID {$employeeId}"
$content = str_replace('EMP ID 7', 'EMP ID {$employeeId}', $content);

file_put_contents($file, $content);
echo "Hardcoded Employee ID 7 removed from logs!\n";
