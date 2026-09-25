<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;
use App\Models\SalesRecord;
use App\Models\User;

class SendLeadFCMNotification implements ShouldQueue
{
    use Queueable;

    protected $salesRecordId;
    protected $userIds;
    protected $type;
    protected $connectionName;

    /**
     * Create a new job instance.
     */
    public function __construct(int $salesRecordId, array $userIds, string $type, string $connectionName)
    {
        $this->salesRecordId = $salesRecordId;
        $this->userIds = $userIds;
        $this->type = $type;
        $this->connectionName = $connectionName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Re-establish the correct tenant database connection
            if (str_starts_with($this->connectionName, 'tenant_')) {
                $tenantId = (int) str_replace('tenant_', '', $this->connectionName);
                \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
            } else {
                DB::setDefaultConnection($this->connectionName);
            }

            $lead = SalesRecord::find($this->salesRecordId);
            if (!$lead) return;

            $users = User::whereIn('id', $this->userIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->get();

            if ($users->isEmpty()) {
                return;
            }

            $credentialsPath = storage_path('app/firebase_credentials.json');

            if (!file_exists($credentialsPath)) {
                Log::error('FCM Credentials file not found at ' . $credentialsPath);
                return;
            }

            $credentialsJson = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $credentialsJson['project_id'] ?? null;

            if (!$projectId) {
                Log::error('Invalid Firebase credentials file. Missing project_id.');
                return;
            }

            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsPath
            );

            $authToken = $credentials->fetchAuthToken();
            $accessToken = $authToken['access_token'];

            $apiUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $leadName = $lead->leads_name ?: 'Untitled Lead';
            $title = $this->type === 'created' ? 'New Lead Created!' : 'Lead Reassigned!';
            $body = $this->type === 'created' 
                ? "A new lead '{$leadName}' has been added." 
                : "Lead '{$leadName}' has been reassigned.";

            foreach ($users as $user) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'lead_id' => (string) $lead->id,
                            'type' => 'lead_' . $this->type
                        ]
                    ]
                ]);

                if (!$response->successful()) {
                    Log::error('FCM Send Error for token ' . $user->fcm_token . ': ' . $response->body());
                }
            }

        } catch (\Exception $e) {
            Log::error('Lead FCM Notification Exception: ' . $e->getMessage());
        }
    }
}
