<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProspectToCustomerService;

class ConvertProspectsToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prospects:convert-to-customers {--dry-run : Show what would be converted without actually converting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert prospects to customers for all sales records with Close_win status';

    protected $prospectToCustomerService;

    public function __construct(ProspectToCustomerService $prospectToCustomerService)
    {
        parent::__construct();
        $this->prospectToCustomerService = $prospectToCustomerService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting prospect to customer conversion...');

        if ($isDryRun) {
            $this->showPendingConversions();
        } else {
            $convertedCount = $this->prospectToCustomerService->processPendingConversions();
            $this->info("Successfully converted {$convertedCount} prospects to customers.");
        }

        $this->info('Conversion process completed.');
    }

    /**
     * Show pending conversions without executing them
     */
    protected function showPendingConversions()
    {
        $pendingRecords = \App\Models\SalesRecord::where('status_id', 1)
            ->whereDoesntHave('customer', function($query) {
                $query->whereColumn('customers.prospectus_id', 'sales_records.prospectus_id');
            })
            ->with(['prospectus', 'user'])
            ->get();

        if ($pendingRecords->isEmpty()) {
            $this->info('No pending conversions found.');
            return;
        }

        $this->info("Found {$pendingRecords->count()} prospects that would be converted to customers:");
        
        $this->table(
            ['ID', 'Lead Name', 'Prospect Name', 'User', 'Created Date'],
            $pendingRecords->map(function($record) {
                return [
                    $record->id,
                    $record->leads_name,
                    $record->prospectus->prospectus_name ?? 'N/A',
                    $record->user->name ?? 'N/A',
                    $record->createdat ? $record->createdat->format('Y-m-d') : 'N/A'
                ];
            })
        );
    }
}
