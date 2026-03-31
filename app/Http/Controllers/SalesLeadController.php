<?php

namespace App\Http\Controllers;

use App\Models\SalesRecord;
use App\Models\Remark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesLeadController extends Controller
{
    public function index(){
        return view('lead');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'prospectus_id' => 'required|integer',
        'leads_name' => 'nullable|string',
        'contact_person' => 'nullable|string',
        'contact_number' => 'nullable|string',
        'status_id' => 'required|string',
        'address' => 'nullable|string',
        'state_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'email' => 'nullable|email',
        'next_follow_up_date' => 'required|date',
        'business_type_id' => 'nullable|integer',
        'remark' => 'required|string',
        'website_link' => 'nullable|string',
        'lead_source_id' => 'nullable|string',
        'products_id' => 'nullable|string']);

    // Set additional fields
    $validated['user_id'] = $this->getCurrentUserId();
    $validated['createdat'] = now();    

    // Extract remark before saving SalesRecord
    $remarkText = $validated['remark'] ?? null;
    unset($validated['remark']);

    // Save sales record
    $salesRecord = SalesRecord::create($validated);

    // Save remark in 'remarks' table
    if ($remarkText) {
        Remark::create([
            'remark_date' => now()->toDateString(),
            'remark' => $remarkText,
            'sales_remark_id' => $salesRecord->id
        ]);
    }

    // --- Start Email Notification ---
    $creator = \App\Models\User::find($salesRecord->user_id);

    $recipientEmails = \App\Models\User::whereHas('employee', function ($q) {
            $q->where('status', 'active');
        })
        ->where('is_sales', 1)
        ->whereNotNull('email')
        ->pluck('email')
        ->toArray();
        
    if ($creator && $creator->email && !in_array($creator->email, $recipientEmails)) {
        $recipientEmails[] = $creator->email;
    }

    $recipientEmails = array_filter($recipientEmails, function($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    });

    if (!empty($recipientEmails)) {
        try {
            \Illuminate\Support\Facades\Mail::to($recipientEmails)
                ->send(new \App\Mail\NewLeadNotification($salesRecord, $creator, $remarkText));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send new lead email: " . $e->getMessage());
        }
    }
    // --- End Email Notification ---

    return response()->json(['message' => 'Sales record saved successfully']);
}

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        return session('user_id');
    }

}


