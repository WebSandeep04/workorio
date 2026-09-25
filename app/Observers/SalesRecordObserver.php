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

        // --- FCM Notification Logic ---
        $creatorId = $this->getCurrentUserId();
        $assignedToId = $salesRecord->user_id;

        // Fetch users who should receive mail/notification
        $salesUserIds = \App\Models\User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })
            ->where('is_sales', 1)
            ->where('is_new_lead_add_mail', 1)
            ->pluck('id')
            ->toArray();

        $notifyUserIds = $salesUserIds;
        if ($creatorId) $notifyUserIds[] = $creatorId;
        if ($assignedToId) $notifyUserIds[] = $assignedToId;

        $notifyUserIds = array_unique(array_filter($notifyUserIds));

        if (!empty($notifyUserIds)) {
            $connectionName = \Illuminate\Support\Facades\DB::getDefaultConnection();
            dispatch(new \App\Jobs\SendLeadFCMNotification($salesRecord->id, $notifyUserIds, 'created', $connectionName));
        }
        // ------------------------------

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
            $oldUserId = $salesRecord->getOriginal('user_id');
            $newUserId = $salesRecord->user_id;
            $assignedBy = $this->getCurrentUserId();

            \App\Models\LeadAssignmentLog::create([
                'sales_record_id' => $salesRecord->id,
                'from_user_id' => $oldUserId,
                'to_user_id' => $newUserId,
                'assigned_by' => $assignedBy,
                'remark' => 'Lead reassigned/transferred',
            ]);

            // --- FCM Notification Logic ---
            $notifyUserIds = [];
            if ($assignedBy) $notifyUserIds[] = $assignedBy;
            if ($newUserId) $notifyUserIds[] = $newUserId;
            if ($oldUserId) $notifyUserIds[] = $oldUserId;

            $notifyUserIds = array_unique(array_filter($notifyUserIds));

            if (!empty($notifyUserIds)) {
                $connectionName = \Illuminate\Support\Facades\DB::getDefaultConnection();
                dispatch(new \App\Jobs\SendLeadFCMNotification($salesRecord->id, $notifyUserIds, 'reassigned', $connectionName));
            }
            // ------------------------------
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
