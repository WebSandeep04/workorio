<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesRecord;
use App\Models\Customer;
use App\Models\Prospectus;
use Illuminate\Support\Facades\DB;

class PopulateCustomerIdInSalesRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:populate-customer-id {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate customer_id field in sales_records and create customers if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting to populate customer_id in sales_records...');

        // Get all Close Won sales records
        $closeWonLeads = SalesRecord::where('status_id', 1)
            ->whereNull('customer_id')
            ->get();

        $this->info("Found {$closeWonLeads->count()} Close Won leads to process");

        $updatedCount = 0;
        $createdCustomersCount = 0;
        $skippedCount = 0;

        foreach ($closeWonLeads as $lead) {
            $this->line("Processing lead: {$lead->leads_name} (ID: {$lead->id})");
            
            // Try to find existing customer by prospectus_id
            $customer = null;
            if ($lead->prospectus_id) {
                $customer = Customer::where('prospectus_id', $lead->prospectus_id)
                    ->first();
            }
            
            // If no customer found, create one
            if (!$customer) {
                if (!$isDryRun) {
                    $customer = Customer::create([
                        'name' => $lead->contact_person,
                        'email' => $lead->email,
                        'phone' => $lead->contact_number,
                        'address' => $lead->address,
                        'company_name' => $lead->leads_name,
                        'tenant_id' => $lead->tenant_id,
                        'prospectus_id' => $lead->prospectus_id
                    ]);
                    $createdCustomersCount++;
                    $this->line("  ✓ Created new customer: {$customer->name}");
                } else {
                    $this->line("  ✓ Would create new customer: {$lead->contact_person}");
                    $createdCustomersCount++;
                    // For dry run, create a dummy customer object to continue processing
                    $customer = (object)['id' => 'NEW'];
                }
            } else {
                $this->line("  ✓ Found existing customer: {$customer->name}");
            }
            
            // Update the sales record with customer_id
            if (!$isDryRun && $customer && $customer->id !== 'NEW') {
                $lead->update(['customer_id' => $customer->id]);
                $updatedCount++;
                $this->line("  ✓ Updated sales record with customer_id: {$customer->id}");
            } else if ($customer && $customer->id !== 'NEW') {
                $updatedCount++;
                $this->line("  ✓ Would update sales record with customer_id: {$customer->id}");
            } else if ($customer && $customer->id === 'NEW') {
                $updatedCount++;
                $this->line("  ✓ Would update sales record with customer_id: NEW");
            } else {
                $skippedCount++;
                $this->line("  ✗ Failed to process lead");
            }
        }

        if ($isDryRun) {
            $this->info("\nDRY RUN SUMMARY:");
            $this->info("Would create: {$createdCustomersCount} customers");
            $this->info("Would update: {$updatedCount} sales records");
            $this->info("Would skip: {$skippedCount} sales records");
            $this->info("\nRun without --dry-run to apply changes");
        } else {
            $this->info("\nUPDATE SUMMARY:");
            $this->info("Created: {$createdCustomersCount} customers");
            $this->info("Updated: {$updatedCount} sales records");
            $this->info("Skipped: {$skippedCount} sales records");
        }

        return 0;
    }
}
