<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('prospectus:import-sql {path}', function (string $path) {
    if (!file_exists($path)) {
        $this->error("File not found: {$path}");
        return 1;
    }

    $sql = file_get_contents($path);
    if ($sql === false || trim($sql) === '') {
        $this->error('The SQL file is empty or unreadable.');
        return 1;
    }

    try {
        DB::unprepared($sql);
        $this->info('Prospectus SQL import completed successfully.');
    } catch (\Throwable $e) {
        $this->error('Import failed: ' . $e->getMessage());
        return 1;
    }

    return 0;
})->purpose('Import a .sql file into the database');

// Note: The actual subscription:send-alerts command is defined in app/Console/Commands/SendSubscriptionAlerts.php
// This route command was causing infinite loop, so it's removed.

// Schedule subscription end date updates daily
use Illuminate\Support\Facades\Schedule;


Schedule::command('subscriptions:generate-recurring')
    ->daily()
    ->at('01:00')
    ->timezone('Asia/Kolkata')
    ->description('Generate new billing cycles for recurring subscriptions across all tenants');

Schedule::command('sales:send-follow-up-report')
    ->daily()
    ->at('08:00')
    ->timezone('Asia/Kolkata')
    ->description('Send follow-up due reports to sales users');

Schedule::command('sales:send-admin-follow-up-report')
    ->daily()
    ->at('08:15')
    ->timezone('Asia/Kolkata')
    ->description('Send consolidated daily follow-up reports to tenant admins/managers');

// Dummy command to test cron job
// use Illuminate\Support\Facades\Log;

// Schedule::call(function () {
//     Log::info('Cron Job Test: Scheduler is running at ' . now());
// })->dailyAt('18:40')->timezone('Asia/Kolkata')->name('cron:test');
