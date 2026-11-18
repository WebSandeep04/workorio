<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmailRecipientService
{
    /**
     * Get valid recipient emails for a specific mail code strictly from database mapping.
     *
     * @param string $mailCode e.g., 'ATTENDANCE_MORNING', 'FINANCE_PETTY_CASH'
     * @return array List of valid recipient emails
     */
    public static function getRecipients(string $mailCode): array
    {
        // 1. Check if the alert exists & is active in tenant DB
        if (Schema::hasTable('system_email_alerts')) {
            $alert = DB::table('system_email_alerts')->where('mail_code', $mailCode)->first();
            
            // If the alert is explicitly turned off by tenant admin, return empty (skip sending)
            if ($alert && !$alert->is_active) {
                return [];
            }
        }

        // 2. Fetch recipient mappings strictly from database
        if (Schema::hasTable('system_email_alert_recipients')) {
            $userIds = DB::table('system_email_alert_recipients')
                ->where('mail_code', $mailCode)
                ->pluck('user_id');

            if ($userIds->isNotEmpty()) {
                return User::whereIn('id', $userIds)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->filter(fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                    ->unique()
                    ->values()
                    ->toArray();
            }
        }

        return [];
    }
}
