<?php

namespace App\Services;

use App\Models\User;
use App\Models\SystemEmailLog;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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

    /**
     * Centralized mail dispatch with automatic per-recipient audit logging.
     *
     * @param string $mailCode Mail alert code (e.g. 'ATTENDANCE_MORNING')
     * @param array|string $recipients Single email string or array of emails
     * @param Mailable $mailable The mailable instance to dispatch
     * @param array|null $meta Optional extra metadata/context (e.g. ['records_count' => 12])
     * @return bool True if successfully sent, false if failed
     */
    public static function sendAndLog(string $mailCode, $recipients, Mailable $mailable, ?array $meta = null): bool
    {
        // Normalize recipients to a flat array of email strings
        $emails = is_array($recipients) ? $recipients : [$recipients];
        $emails = array_filter(array_map('trim', $emails), fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        if (empty($emails)) {
            return false;
        }

        // Preload tenant users matching these emails for linked user_id and name
        $userMap = collect();
        if (Schema::hasTable('users')) {
            $userMap = User::whereIn('email', $emails)->get()->keyBy('email');
        }

        $subject = method_exists($mailable, 'envelope') ? ($mailable->envelope()->subject ?? class_basename($mailable)) : ($mailable->subject ?? class_basename($mailable));

        $status = 'sent';
        $errorMessage = null;

        try {
            Mail::to($emails)->send($mailable);
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("Failed sending email [{$mailCode}]: " . $errorMessage);
        }

        // Log record for every recipient
        if (Schema::hasTable('system_email_logs')) {
            $now = now();
            $logEntries = [];

            foreach ($emails as $email) {
                $user = $userMap->get($email);
                $logEntries[] = [
                    'mail_code' => $mailCode,
                    'recipient_email' => $email,
                    'recipient_name' => $user ? $user->name : null,
                    'user_id' => $user ? $user->id : null,
                    'subject' => $subject,
                    'status' => $status,
                    'error_message' => $errorMessage,
                    'meta' => $meta ? json_encode($meta) : null,
                    'sent_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($logEntries)) {
                try {
                    SystemEmailLog::insert($logEntries);
                } catch (\Throwable $e) {
                    Log::error("Failed to insert system_email_logs: " . $e->getMessage());
                }
            }
        }

        return $status === 'sent';
    }
}
