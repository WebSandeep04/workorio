<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantEmailAlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $alerts = [
            [
                'mail_code' => 'ATTENDANCE_MORNING',
                'name' => 'Morning Attendance Status',
                'category' => 'Attendance',
                'default_time' => '10:30 AM',
                'description' => 'Morning attendance check-in status report listing employees punched in, on leave, or absent.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'ATTENDANCE_NIGHT',
                'name' => 'Evening Attendance & Monthly Summary',
                'category' => 'Attendance',
                'default_time' => '08:35 PM',
                'description' => 'Evening attendance wrap-up with work hours, breaks, and cumulative month-to-date breakdown.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'TIMESHEET_YESTERDAY',
                'name' => "Yesterday's Worklog Summary",
                'category' => 'Timesheet',
                'default_time' => '10:00 AM',
                'description' => "Consolidated daily summary of yesterday's submitted timesheet worklogs.",
                'is_active' => 1,
            ],
            [
                'mail_code' => 'TIMESHEET_TODAY',
                'name' => "Today's Worklog Summary",
                'category' => 'Timesheet',
                'default_time' => '08:40 PM',
                'description' => "Evening review of all worklogs and timesheets submitted today.",
                'is_active' => 1,
            ],
            [
                'mail_code' => 'TASK_SELF_REMINDER',
                'name' => 'Personal Task Reminders',
                'category' => 'Tasks',
                'default_time' => '10:30 AM',
                'description' => 'Daily reminder sent to individual employees regarding their pending assigned tasks.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'TASK_ALL_SUMMARY',
                'name' => 'All Tasks Summary',
                'category' => 'Tasks',
                'default_time' => '11:15 AM',
                'description' => 'Comprehensive summary of all pending and overdue tasks across the company grouped by user.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'TASK_SIGNAL_PENDING',
                'name' => 'Signal WhatsApp Pending Tasks',
                'category' => 'Tasks',
                'default_time' => '03:00 PM',
                'description' => 'Daily alert for all pending and unconverted Signal WhatsApp tasks.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'SALES_ADMIN_FOLLOWUP',
                'name' => 'Sales Admin Follow-up Report',
                'category' => 'Sales',
                'default_time' => '09:30 AM',
                'description' => 'Consolidated daily follow-up tracking report for administrators and sales managers.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'SALES_USER_FOLLOWUP',
                'name' => 'Sales Agent Follow-up Due',
                'category' => 'Sales',
                'default_time' => '09:45 AM',
                'description' => 'Targeted reminders for sales agents on leads scheduled for follow-up today.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'CALLING_ANALYTICS',
                'name' => 'Tele-Calling Analytics Report',
                'category' => 'Tele-Calling',
                'default_time' => '07:30 PM',
                'description' => 'Daily tele-calling campaign productivity, converted leads, and calling metrics.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'CALENDAR_MORNING',
                'name' => 'Calendar Events (Morning)',
                'category' => 'Calendar',
                'default_time' => '11:00 AM',
                'description' => 'Morning summary of pending social media calendar events and upcoming deadlines.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'CALENDAR_AFTERNOON',
                'name' => 'Calendar Events (Afternoon)',
                'category' => 'Calendar',
                'default_time' => '02:15 PM',
                'description' => 'Midday status update for pending calendar posts and design tasks.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'FINANCE_PETTY_CASH',
                'name' => 'Petty Cash Summary Report',
                'category' => 'Finance',
                'default_time' => '04:00 PM',
                'description' => 'Daily petty cash summary report with department balances and 10-day expense history.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'FINANCE_SUBSCRIPTION',
                'name' => 'Subscription Status & Overdue',
                'category' => 'Finance',
                'default_time' => '02:30 PM',
                'description' => 'Alerts for active client subscriptions, pending renewals, and overdue invoices.',
                'is_active' => 1,
            ],
            [
                'mail_code' => 'WHATSAPP_INBOX',
                'name' => 'WhatsApp Inbox Summary',
                'category' => 'WhatsApp',
                'default_time' => '02:45 PM',
                'description' => 'Daily summary of incoming WhatsApp messages and conversation status.',
                'is_active' => 1,
            ],
        ];

        foreach ($alerts as $alert) {
            DB::table('system_email_alerts')->updateOrInsert(
                ['mail_code' => $alert['mail_code']],
                array_merge($alert, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
