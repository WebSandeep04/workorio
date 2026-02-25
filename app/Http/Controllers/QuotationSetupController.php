<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuotationSetupController extends Controller
{
    /**
     * Display the quotation setup page
     */
    public function index()
    {
        return view('quotation.setup');
    }

    /**
     * Get current quotation settings
     */
    public function fetch()
    {
        if (!Schema::hasTable('quotation_settings')) {
            return response()->json(['data' => null]);
        }

        $settings = DB::table('quotation_settings')->first();
        
        if ($settings) {
            // Parse services if it's JSON
            if ($settings->services) {
                try {
                    $settings->services = json_decode($settings->services, true);
                } catch (\Exception $e) {
                    $settings->services = [];
                }
            }
        }

        return response()->json(['data' => $settings]);
    }

    /**
     * Store or update quotation settings
     */
    public function store(Request $request)
    {
        if (!Schema::hasTable('quotation_settings')) {
            return response()->json(['message' => 'Table does not exist'], 500);
        }

        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'mission' => 'nullable|string',
            'vision' => 'nullable|string',
            'core_values' => 'nullable|string',
            'services' => 'nullable|array',
            'office_name' => 'nullable|string|max:255',
            'office_address' => 'nullable|string',
            'office_city' => 'nullable|string|max:255',
            'office_state' => 'nullable|string|max:255',
            'office_pincode' => 'nullable|string|max:20',
            'office_country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'bank_details' => 'nullable|string',
            'logo_path' => 'nullable|string|max:500',
            'template_name' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
        ]);

        // Convert services array to JSON
        if (isset($data['services']) && is_array($data['services'])) {
            $data['services'] = json_encode($data['services']);
        }

        $existing = DB::table('quotation_settings')->first();

        if ($existing) {
            DB::table('quotation_settings')
                ->where('id', $existing->id)
                ->update($data);
            
            return response()->json(['message' => 'Quotation settings updated successfully']);
        } else {
            DB::table('quotation_settings')->insert($data);
            
            return response()->json(['message' => 'Quotation settings saved successfully']);
        }
    }

    /**
     * Get settings for PDF generation (API endpoint)
     */
    public function getSettings()
    {
        if (!Schema::hasTable('quotation_settings')) {
            return response()->json(['data' => null]);
        }

        $settings = DB::table('quotation_settings')->first();
        
        if ($settings) {
            // Parse services if it's JSON
            if ($settings->services) {
                try {
                    $settings->services = json_decode($settings->services, true);
                } catch (\Exception $e) {
                    $settings->services = [];
                }
            }
        }

        return response()->json(['data' => $settings]);
    }
}
