<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Carbon\Carbon;

class UpdateSubscriptionEndDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-end-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-update subscription end dates based on recurrence when current end date is reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Find subscriptions where end_date is today or past, and status is NOT pending
        $expiredSubscriptions = Subscription::where('is_active', true)
            ->where('is_recurring', true)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $today)
            ->where('status', '!=', 'pending')
            ->get();

        $updated = 0;

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->status = 'pending';
            $subscription->save();
            $updated++;
        }

        $this->info("Reset {$updated} expired subscriptions to 'pending'.");
        return 0;
    }

    /**
     * Calculate next end date based on current end date, recurrence type, and interval
     */
    private function calculateNextEndDate($currentEndDate, $recurrenceType, $interval)
    {
        if (!$currentEndDate || !$recurrenceType || !$interval) {
            return null;
        }

        $end = Carbon::parse($currentEndDate);
        $interval = (int) $interval;

        switch ($recurrenceType) {
            case 'daily':
                return $end->copy()->addDays($interval)->format('Y-m-d');
            case 'weekly':
                return $end->copy()->addWeeks($interval)->format('Y-m-d');
            case 'monthly':
                return $end->copy()->addMonths($interval)->format('Y-m-d');
            case 'yearly':
                return $end->copy()->addYears($interval)->format('Y-m-d');
            default:
                return null;
        }
    }
}
