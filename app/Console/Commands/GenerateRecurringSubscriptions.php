<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Carbon\Carbon;
use Exception;

class GenerateRecurringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:generate-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new pending billing cycles for active recurring subscriptions across all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recurring subscription generation...');

        // 1. Get all tenants from the master database
        // We explicitly use the 'mysql' connection to ensure we are querying the landlord DB
        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Recurring subscription generation completed for all tenants.');
        return 0;
    }

    /**
     * Process subscriptions for a single tenant
     */
    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            // Switch context to this tenant
            // accessible via static method as seen in TenantDatabaseService
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            // Verify connection works (optional but good for logging)
            // If setDefaultConnection throws, we catch it below.
            
            $today = Carbon::now();
            
            // We use the Subscription model which now uses the default connection (switched above)
            Subscription::where('is_active', true)
                ->where('is_recurring', true)
                ->chunk(100, function ($subscriptions) use ($today) {
                    foreach ($subscriptions as $subscription) {
                        $this->processSubscription($subscription, $today);
                    }
                });

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            // Optional: Reset to mysql if needed, but next loop overrides it anyway.
            // Resetting is good practice.
            DB::setDefaultConnection('mysql');
        }
    }

    private function processSubscription($subscription, $today)
    {
        // 1. Get the latest history record to determine where we left off
        $latestHistory = DB::table('subscription_histories')
            ->where('subscription_id', $subscription->id)
            ->orderBy('period_start', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Determine the End Date of the last cycle
        $lastCycleEnd = null;

        if ($latestHistory) {
            // Normal case: We have a history record
            if ($latestHistory->period_end) {
                $lastCycleEnd = Carbon::parse($latestHistory->period_end);
            } elseif ($latestHistory->due_date) {
                // Fallback for legacy postpaid data
                $lastCycleEnd = Carbon::parse($latestHistory->due_date);
            } else {
                // Extreme fallback
                $lastCycleEnd = Carbon::parse($latestHistory->period_start); 
            }
        } else {
            // No history? Use subscription start_date as the base.
            // Effectively we assume the cycle ends "yesterday" relative to start date strictly for calculation loops
            $lastCycleEnd = Carbon::parse($subscription->start_date)->subDay();
        }

        // 2. Loop to catch up all missing cycles up to Today
        // Requirement: If the cycle end date is in the past, a new cycle should open.
        // Example: Jan 31 < Feb 1 (Today) -> Create Feb Cycle.
        
        $loops = 0;
        $maxLoops = 36; // Limit to ~3 years to prevent infinite loops

        while ($lastCycleEnd->copy()->addDay()->lte($today) && $loops < $maxLoops) {
            $nextStartDate = $lastCycleEnd->copy()->addDay();
            
            // Calculate Next End Date
            $nextEndDateStr = $this->calculateEndDate(
                $nextStartDate->format('Y-m-d'), 
                $subscription->recurrence_type, 
                $subscription->recurrence_interval ?? 1
            );
            
            if (!$nextEndDateStr) break; 
            
            $nextEndDate = Carbon::parse($nextEndDateStr); 
            
            // Standardize period_end to be exclusive of next start (Cycle - 1 day)
            $periodEnd = $nextEndDate->copy()->subDay();
            
            // Recurrence End Date Check
            if ($subscription->recurrence_end_date) {
                $limitDate = Carbon::parse($subscription->recurrence_end_date);
                if ($nextStartDate->gt($limitDate)) {
                    break;
                }
            }

            // Determine Due Date
            $dueDate = $nextEndDate->format('Y-m-d'); // Default Postpaid (Transition Day)
            if ($subscription->billing_type === 'Prepaid') {
                $dueDate = $nextStartDate->format('Y-m-d');
            }

            // Check specific period existence to avoid duplicates
            $exists = DB::table('subscription_histories')
                ->where('subscription_id', $subscription->id)
                ->where('period_start', $nextStartDate->format('Y-m-d'))
                ->exists();

            if (!$exists) {
                DB::table('subscription_histories')->insert([
                    'subscription_id' => $subscription->id,
                    'period_start' => $nextStartDate->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                    'due_date' => $dueDate,
                    'amount' => $subscription->amount,
                    'status' => 'pending', 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->info("  [Sub #{$subscription->id}] Generated cycle: {$nextStartDate->toDateString()} to {$periodEnd->toDateString()}");
            }

            // Advance loop
            $lastCycleEnd = $periodEnd;
            $loops++;
        }
    }

    /**
     * Calculate end date based on start date, recurrence type, and interval
     */
    private function calculateEndDate($startDate, $recurrenceType, $interval)
    {
        if (!$startDate || !$recurrenceType || !$interval) {
            return null;
        }

        $start = Carbon::parse($startDate);
        $interval = (int) $interval;

        switch ($recurrenceType) {
            case 'daily':
                return $start->copy()->addDays($interval)->format('Y-m-d');
            case 'weekly':
                return $start->copy()->addWeeks($interval)->format('Y-m-d');
            case 'monthly':
                return $start->copy()->addMonths($interval)->format('Y-m-d');
            case 'quarterly':
                return $start->copy()->addMonths(3 * $interval)->format('Y-m-d');
            case 'half_yearly':
                return $start->copy()->addMonths(6 * $interval)->format('Y-m-d');
            case 'yearly':
                return $start->copy()->addYears($interval)->format('Y-m-d');
            default:
                return null;
        }
    }
}
