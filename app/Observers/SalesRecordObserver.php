<?php

namespace App\Observers;

use App\Models\SalesRecord;
use App\Services\ProspectToCustomerService;

class SalesRecordObserver
{
    protected $prospectToCustomerService;

    public function __construct(ProspectToCustomerService $prospectToCustomerService)
    {
        $this->prospectToCustomerService = $prospectToCustomerService;
    }

    /**
     * Handle the SalesRecord "created" event.
     */
    public function created(SalesRecord $salesRecord): void
    {
        // Log initial assignment
        if ($salesRecord->user_id) {
            \App\Models\LeadAssignmentLog::create([
                'sales_record_id' => $salesRecord->id,
                'from_user_id' => null,
                'to_user_id' => $salesRecord->user_id,
                'assigned_by' => $this->getCurrentUserId(),
                'remark' => 'Initial lead assignment on creation',
            ]);
        }

        // Check if this is a Close_win status and convert to customer
        if ($this->prospectToCustomerService->shouldConvertToCustomer($salesRecord)) {
            $this->prospectToCustomerService->convertProspectToCustomer($salesRecord);
        }
    }

    /**
     * Handle the SalesRecord "updated" event.
     */
    public function updated(SalesRecord $salesRecord): void
    {
        // Log assignment changes
        if ($salesRecord->wasChanged('user_id')) {
            \App\Models\LeadAssignmentLog::create([
                'sales_record_id' => $salesRecord->id,
                'from_user_id' => $salesRecord->getOriginal('user_id'),
                'to_user_id' => $salesRecord->user_id,
                'assigned_by' => $this->getCurrentUserId(),
                'remark' => 'Lead reassigned/transferred',
            ]);
        }

        // Check if status was changed to Close_win
        if ($salesRecord->wasChanged('status_id') && 
            $this->prospectToCustomerService->shouldConvertToCustomer($salesRecord)) {
            $this->prospectToCustomerService->convertProspectToCustomer($salesRecord);
        }
    }

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            return \Illuminate\Support\Facades\Auth::id();
        }
        
        if (session()->has('user_id')) {
            return session('user_id');
        }
        
        return null;
    }

    /**
     * Handle the SalesRecord "deleted" event.
     */
    public function deleted(SalesRecord $salesRecord): void
    {
        //
    }

    /**
     * Handle the SalesRecord "restored" event.
     */
    public function restored(SalesRecord $salesRecord): void
    {
        //
    }

    /**
     * Handle the SalesRecord "force deleted" event.
     */
    public function forceDeleted(SalesRecord $salesRecord): void
    {
        //
    }
}
