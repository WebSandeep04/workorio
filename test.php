<?php
require 'vendor/autoload.php';
use Carbon\Carbon;

$time = '2026-05-22 17:25:53';
$recentTime = Carbon::parse($time, 'Asia/Kolkata');
$now = Carbon::now('Asia/Kolkata');

echo "Recent Time parsed: " . $recentTime->format('Y-m-d H:i:s P') . "\n";
echo "Now (Asia/Kolkata): " . $now->format('Y-m-d H:i:s P') . "\n";
echo "Diff in minutes: " . $recentTime->diffInMinutes($now) . "\n";
