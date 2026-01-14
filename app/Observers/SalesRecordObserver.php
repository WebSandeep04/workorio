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
        // Check if status was changed to Close_win
        if ($salesRecord->wasChanged('status_id') && 
            $this->prospectToCustomerService->shouldConvertToCustomer($salesRecord)) {
            $this->prospectToCustomerService->convertProspectToCustomer($salesRecord);
        }
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
