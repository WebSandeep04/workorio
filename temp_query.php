<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::where('name', 'like', '%Anupriya%')->first();
if (!$u) {
    echo "User not found\n";
    exit;
}
$e = $u->employee;
$s = $e->getShiftForDate('2026-07-03');
if (!$s) {
    echo "Shift not found\n";
    exit;
}
echo 'Full Day Hr: ' . $s->full_day_hr . "\n";
echo 'Half Day Hr: ' . $s->half_day_hr . "\n";
echo 'Start Time: ' . $s->start_time . "\n";
echo 'End Time: ' . $s->end_time . "\n";
