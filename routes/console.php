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

Schedule::command('attendance:lock-past')
    ->daily()
    ->at('01:00')
    ->timezone('Asia/Kolkata')
    ->description('Automatically lock all past attendance records');

Schedule::command('leave:process-lapse')
    ->daily()
    ->at('00:30')
    ->timezone('Asia/Kolkata')
    ->description('Automatically process monthly or yearly leave lapses across all users');

Schedule::command('sales:send-admin-follow-up-report', ['--alert=1'])
    ->daily()
    ->at('09:30')
    ->timezone('Asia/Kolkata')
    ->description('Send consolidated daily follow-up reports to tenant admins/managers');

Schedule::command('sales:send-follow-up-report', ['--alert=2'])
    ->daily()
    ->at('09:45')
    ->timezone('Asia/Kolkata')
    ->description('Send follow-up due reports to sales users');

Schedule::command('worklog:send-yesterday-mail', ['--alert=3'])
    ->daily()
    ->at('10:00')
    ->timezone('Asia/Kolkata')
    ->description("Send yesterday's worklog summary to HR/Admins");

Schedule::command('attendance:send-morning-mail', ['--alert=4'])
    ->daily()
    ->at('10:30')
    ->timezone('Asia/Kolkata')
    ->description('Send morning attendance summary email to all users across all tenants');

Schedule::command('task:send-self-mail', ['--alert=5'])
    ->daily()
    ->at('10:30')
    ->timezone('Asia/Kolkata')
    ->description('Send daily task reminders to individual users for their pending tasks');

Schedule::command('calendar:send-mail', ['--alert=6'])
    ->daily()
    ->at('11:00')
    ->timezone('Asia/Kolkata')
    ->description('Send daily pending calendar events and monthly summary to calendar users');

Schedule::command('task:send-all-mail', ['--alert=7'])
    ->daily()
    ->at('11:15')
    ->timezone('Asia/Kolkata')
    ->description('Send daily summary of all pending tasks grouped by user');

Schedule::command('calendar:send-mail', ['--alert=8'])
    ->daily()
    ->at('14:15')
    ->timezone('Asia/Kolkata')
    ->description('Send daily pending calendar events and monthly summary to calendar users');

Schedule::command('subscription:send-mail', ['--alert=9'])
    ->daily()
    ->at('14:30')
    ->timezone('Asia/Kolkata')
    ->description('Send daily subscription summary and overdue alerts');

Schedule::command('calling:send-analytic-report', ['--alert=10'])
    ->daily()
    ->at('19:30')
    ->timezone('Asia/Kolkata')
    ->description('Send daily calling analytic reports to admins for all tenants');

Schedule::command('attendance:send-night-mail', ['--alert=11'])
    ->daily()
    ->at('20:35')
    ->timezone('Asia/Kolkata')
    ->description('Send evening attendance summary and monthly breakdown to all active users');

Schedule::command('worklog:send-today-mail', ['--alert=12'])
    ->daily()
    ->at('20:40')
    ->timezone('Asia/Kolkata')
    ->description("Send today's worklog summary to HR/Admins");

Schedule::command('sales:send-admin-follow-up-report', ['--alert=13'])
    ->daily()
    ->at('23:30')
    ->timezone('Asia/Kolkata')
    ->description('Send consolidated daily follow-up reports to tenant admins/managers');



