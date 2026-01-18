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
        $prompt = <<<EOT
You are an AI data extraction service.

Your task is to extract structured business card information from OCR text
and return the result strictly in the JSON format defined below.

Rules:
- Use ONLY the keys provided in the schema.
- Fill values only if they are clearly present in the text.
- If a value is missing or unclear, return null.
- Do NOT guess or hallucinate.
- Clean and normalize values (trim spaces, lowercase emails, remove symbols from phone numbers).
- Phone numbers should be digits only (include country code if available).
- Social media links should be returned as a JSON object.
- The response must be valid JSON only. No explanation, no extra text.

Database JSON Schema:

{
  "name": null,
  "designation": null,
  "company_name": null,

  "email": null,
  "phone_primary": null,
  "phone_secondary": null,
  "website": null,

  "address": null,
  "city": null,
  "state": null,
  "pincode": null,
  "country": null,

  "social_links": {
    "linkedin": null,
    "twitter": null,
    "facebook": null,
    "instagram": null,
    "other": []
  },

  "raw_text": null,
  "card_image_url": null,
  "raw_ai_response": null
}

OCR Text:
<<<
$text
>>>
EOT;


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
