<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display the notifications page.
     */
    public function index()
    {
        return view('software-setup.notifications.index');
    }

    /**
     * Send notification to all devices.
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
            // Get all unique FCM tokens
            $tokens = User::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->unique()
                ->values()
                ->all();

            if (empty($tokens)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No devices found with FCM tokens.'
                ], 400);
            }

            // Path to your Firebase service account JSON file
            $credentialsPath = storage_path('app/firebase_credentials.json');

            if (!file_exists($credentialsPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase credentials file not found in storage/app/firebase_credentials.json'
                ], 500);
            }

            // Get Project ID from JSON file
            $credentialsJson = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $credentialsJson['project_id'] ?? null;

            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Firebase credentials file. Missing project_id.'
                ], 500);
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

            $successCount = 0;
            $failureCount = 0;

            foreach ($tokens as $token) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $request->title,
                            'body' => $request->body,
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $successCount++;
                } else {
                    $failureCount++;
                    Log::error('FCM Send Error for token ' . $token . ': ' . $response->body());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Notification sent successfully. Success: {$successCount}, Failed: {$failureCount}",
                'success_count' => $successCount,
                'failure_count' => $failureCount
            ]);

        } catch (\Exception $e) {
            Log::error('Notification Send Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending notification: ' . $e->getMessage()
            ], 500);
        }
    }
}
