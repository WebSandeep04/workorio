<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Carbon\Carbon;

class SendPunchOutReminders extends Command
{
    protected $signature = 'attendance:send-punch-out-reminders';
    protected $description = 'Send FCM reminders to employees who forgot to punch out';

    public function handle()
    {
        // Get attendances for today that haven't received all 4 reminders
        $attendances = Attendance::with(['user', 'movements', 'user.employeeShift.shift'])
            ->whereDate('date', today())
            ->where('punch_out_reminders_sent', '<', 4)
            ->whereHas('movements', function($q) {
                $q->where('movement_type', 'punch_in');
            })
            ->whereDoesntHave('movements', function($q) {
                $q->where('movement_type', 'punch_out');
            })
            ->get();

        $credentialsPath = storage_path('app/firebase_credentials.json');
        
        if (!file_exists($credentialsPath) || $attendances->isEmpty()) {
            return;
        }

        $credentialsJson = json_decode(file_get_contents($credentialsPath), true);
        $projectId = $credentialsJson['project_id'] ?? null;

        if (!$projectId) return;

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            $credentialsPath
        );
        $accessToken = $credentials->fetchAuthToken()['access_token'];
        $apiUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $now = Carbon::now();

        foreach ($attendances as $attendance) {
            $user = $attendance->user;
            if (!$user || !$user->fcm_token) continue;

            $shift = $user->employeeShift->shift ?? null;
            if (!$shift) continue;

            // Assuming end_time is HH:mm:ss, create Carbon instance for today's end time
            $endTime = Carbon::createFromFormat('H:i:s', $shift->end_time);

            if ($now->greaterThan($endTime)) {
                $diffInMinutes = $now->diffInMinutes($endTime);
                $remindersSent = $attendance->punch_out_reminders_sent;

                $shouldSend = false;

                if ($remindersSent == 0 && $diffInMinutes >= 10) {
                    $shouldSend = true;
                } elseif ($remindersSent == 1 && $diffInMinutes >= 25) { // 10 + 15
                    $shouldSend = true;
                } elseif ($remindersSent == 2 && $diffInMinutes >= 45) { // 25 + 20
                    $shouldSend = true;
                } elseif ($remindersSent == 3 && $diffInMinutes >= 70) { // 45 + 25
                    $shouldSend = true;
                }

                if ($shouldSend) {
                    $this->sendFcm($apiUrl, $accessToken, $user->fcm_token);
                    $attendance->increment('punch_out_reminders_sent');
                    $this->info("Sent reminder #".($remindersSent + 1)." to user {$user->id}");
                }
            }
        }
    }

    private function sendFcm($apiUrl, $accessToken, $token)
    {
        Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => 'Shift Ended',
                    'body' => 'You forgot to punch out!',
                ]
            ]
        ]);
    }
}
