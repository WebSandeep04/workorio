<?php

namespace App\Observers;

use App\Models\Calling;
use App\Models\CallingAssignmentLog;
use Illuminate\Support\Facades\Auth;

class CallingObserver
{
    /**
     * Handle the Calling "created" event.
     */
    public function created(Calling $calling): void
    {
        // Log initial assignment
        if ($calling->user_id) {
            CallingAssignmentLog::create([
                'calling_id' => $calling->id,
                'from_user_id' => null,
                'to_user_id' => $calling->user_id,
                'assigned_by' => $this->getCurrentUserId(),
                'remark' => 'Initial calling assignment on creation',
            ]);
        }
    }

    /**
     * Handle the Calling "updated" event.
     */
    public function updated(Calling $calling): void
    {
        // Log assignment changes
        if ($calling->wasChanged('user_id')) {
            CallingAssignmentLog::create([
                'calling_id' => $calling->id,
                'from_user_id' => $calling->getOriginal('user_id'),
                'to_user_id' => $calling->user_id,
                'assigned_by' => $this->getCurrentUserId(),
                'remark' => 'Calling reassigned/transferred',
            ]);
        }
    }

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        
        if (session()->has('user_id')) {
            return session('user_id');
        }
        
        return null; // Return null if automated context
    }
}
