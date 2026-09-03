<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$att = \App\Models\Attendance::first();
echo json_encode($att, JSON_PRETTY_PRINT);
