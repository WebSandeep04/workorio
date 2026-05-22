<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Movement;
use Carbon\Carbon;

$movement = Movement::orderBy('id', 'desc')->first();
echo "Last Movement ID: " . $movement->id . "\n";
echo "Time String from DB: " . $movement->time . "\n";
$parsed = Carbon::parse($movement->time, 'Asia/Kolkata');
$now = Carbon::now('Asia/Kolkata');
echo "Parsed Time: " . $parsed->format('Y-m-d H:i:s P') . "\n";
echo "Now Time: " . $now->format('Y-m-d H:i:s P') . "\n";
echo "Diff in minutes: " . $parsed->diffInMinutes($now) . "\n";
echo "Diff in minutes (absolute=false): " . $parsed->diffInMinutes($now, false) . "\n";
