<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    /**
     * Parses raw text from a business card using Gemini AI
     * and returns structured JSON data.
     */
    public function parseCard(Request $request)
    {
        // validate input
        $request->validate([
            'text' => 'required|string',
        ]);

        $text = $request->input('text');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key not configured'], 500);
        }

        // Updated URL per user request for gemini-2.0-flash
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

        // Prompt to instruct Gemini to extract specific fields
        $prompt = "Extract the following details from the text below and return ONLY a valid JSON object with these keys: name, designation, company, email, phone, website, address. If a field is not found, set it to null. Do not use markdown formatting or explanations. \n\nText: " . $text;

        try {
            // Updated to send API Key in headers
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->post($url, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Extract the text content from Gemini's response structure
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                
                // Clean any potential markdown code blocks (e.g. ```json ... ```)
                $cleanJson = str_replace(['```json', '```'], '', $rawText);
                
                // Decode to ensure it's valid JSON before sending back
                $parsedData = json_decode($cleanJson, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return response()->json($parsedData);
                } else {
                     return response()->json(['error' => 'Failed to decode AI response', 'raw' => $rawText], 500);
                }
            } else {
                return response()->json(['error' => 'Gemini API call failed', 'details' => $response->body()], $response->status());
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error processing card', 'message' => $e->getMessage()], 500);
        }
    }
}
