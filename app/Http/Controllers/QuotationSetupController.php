<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Traits\TenantAwareStorage;

class QuotationSetupController extends Controller
{
    use TenantAwareStorage;
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'template_name' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $existing = DB::table('quotation_settings')->first();
            if ($existing && $existing->logo_path) {
                $this->deleteTenantFile($existing->logo_path);
            }
            // Use tenant-aware storage with isolation
            $path = $this->storeTenantFile($request->file('logo'), 'quotation/logos');
            $data['logo_path'] = $path;
        }
        unset($data['logo']);

        $existing = DB::table('quotation_settings')->first();

        if ($existing) {
            DB::table('quotation_settings')
                ->where('id', $existing->id)
                ->update($data);
            
            return response()->json([
                'message' => 'Quotation settings updated successfully',
                'logo_path' => $data['logo_path'] ?? ($existing->logo_path ?? null)
            ]);
        } else {
            DB::table('quotation_settings')->insert($data);
            
            return response()->json([
                'message' => 'Quotation settings saved successfully',
                'logo_path' => $data['logo_path'] ?? null
            ]);
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
