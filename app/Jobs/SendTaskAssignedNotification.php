<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Auth\Credentials\ServiceAccountCredentials;
use App\Models\Task;
use App\Models\User;

class SendTaskAssignedNotification implements ShouldQueue
{
    use Queueable;

    protected $task;
    protected $userIds;
    protected $assignedBy;
    protected $connectionName;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task, array $userIds, ?int $assignedBy, string $connectionName)
    {
        // Don't serialize the Eloquent model entirely with full relations to avoid issues.
        // But Laravel handles model serialization automatically if using SerializesModels trait.
        // We will just store the ID to be safe and re-fetch it in the correct connection.
        $this->task = $task->id;
        $this->userIds = $userIds;
        $this->assignedBy = $assignedBy;
        $this->connectionName = $connectionName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Re-establish the correct tenant database connection
            // The queue worker runs in a separate process, so we must recreate the config array
            if (str_starts_with($this->connectionName, 'tenant_')) {
                $tenantId = (int) str_replace('tenant_', '', $this->connectionName);
                \App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
            } else {
                DB::setDefaultConnection($this->connectionName);
            }

            // Fetch the task in the correct connection
            $task = Task::find($this->task);
            if (!$task) return;

            // Fetch users who have FCM tokens
            $users = User::whereIn('id', $this->userIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->get();

            if ($users->isEmpty()) {
                return;
            }

            // Path to Firebase credentials
            $credentialsPath = storage_path('app/firebase_credentials.json');

            if (!file_exists($credentialsPath)) {
                Log::error('FCM Credentials file not found at ' . $credentialsPath);
                return;
            }

            // Get Project ID from JSON file
            $credentialsJson = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $credentialsJson['project_id'] ?? null;

            if (!$projectId) {
                Log::error('Invalid Firebase credentials file. Missing project_id.');
                return;
            }

            // Create Google Auth Credentials
            $credentials = new ServiceAccountCredentials(
                ['https://www.googleapis.com/auth/firebase.messaging'],
                $credentialsPath
            );

            // Fetch access token
            $authToken = $credentials->fetchAuthToken();
            $accessToken = $authToken['access_token'];

            $apiUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // Prepare the notification message
            $title = "New Task Assigned";
            $taskName = $task->task_name ?: 'Untitled Task';
            $body = "You have been assigned to task: {$taskName}";

            // Send push notification to each user
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
                            'task_id' => (string) $task->id,
                            'type' => 'task_assigned'
                        ]
                    ]
                ]);

                if (!$response->successful()) {
                    Log::error('FCM Send Error for token ' . $user->fcm_token . ': ' . $response->body());
                }
            }

        } catch (\Exception $e) {
            Log::error('Task Assigned Notification Exception: ' . $e->getMessage());
        }
    }
}
