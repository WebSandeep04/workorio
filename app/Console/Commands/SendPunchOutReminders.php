<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\EmployeeLocation;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Carbon\Carbon;
use Exception;

class SendPunchOutReminders extends Command
{
    protected $signature = 'attendance:send-punch-out-reminders';
    protected $description = 'Send FCM reminders to employees who forgot to punch out';

    public function handle()
    {
        $this->info('Starting punch-out reminder generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Punch-out reminder generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            // Get attendances for today that haven't received all 4 reminders
            $attendances = Attendance::with(['user.employee.places', 'movements', 'user.employeeShift.shift'])
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
                    // Check if employee is in allowed radius
                    $employee = $user->employee;
                    if ($employee && $employee->is_place_allowed) {
                        $allowedPlaces = $employee->places;
                        if ($allowedPlaces->count() > 0) {
                            // Get most recent location for today
                            $recentLocation = EmployeeLocation::where('employee_id', $employee->id)
                                ->whereDate('tracked_at', today())
                                ->orderBy('tracked_at', 'desc')
                                ->first();

                            if ($recentLocation && $recentLocation->latitude && $recentLocation->longitude) {
                                $isWithinRange = false;
                                $userLat = (float) $recentLocation->latitude;
                                $userLong = (float) $recentLocation->longitude;

                                foreach ($allowedPlaces as $place) {
                                    $distance = $this->haversineGreatCircleDistance(
                                        $userLat, $userLong, 
                                        $place->latitude, $place->longitude
                                    );

                                    if ($distance <= $place->radius) {
                                        $isWithinRange = true;
                                        break;
                                    }
                                }

                                if ($isWithinRange) {
                                    $shouldSend = false; // Do not send if they are inside allowed place
                                }
                            }
                        }
                    }
                }

                    if ($shouldSend) {
                        $this->sendFcm($apiUrl, $accessToken, $user->fcm_token);
                        $attendance->increment('punch_out_reminders_sent');
                        $this->info("Sent reminder #".($remindersSent + 1)." to user {$user->id} in tenant {$tenant->tenant_name}");
                    }
                }
            }
        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
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
