<?php

namespace App\Services;

use App\Models\SalesRecord;
use App\Models\Customer;
use App\Models\Prospectus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProspectToCustomerService
{
    /**
     * Convert a prospect to customer when status becomes Close_win
     */
    public function convertProspectToCustomer(SalesRecord $salesRecord)
    {
        try {
            DB::beginTransaction();

            // Check if customer already exists for this prospect
            $existingCustomer = Customer::where('prospectus_id', $salesRecord->prospectus_id)
                ->first();

            if ($existingCustomer) {
                // Update sales record with existing customer_id if not already set
                if ($salesRecord->customer_id != $existingCustomer->id) {
                    $salesRecord->update(['customer_id' => $existingCustomer->id]);
                }
                Log::info("Customer already exists for prospect ID: {$salesRecord->prospectus_id}");
                DB::commit();
                return $existingCustomer;
            }

            // Get prospect details
            $prospectus = Prospectus::find($salesRecord->prospectus_id);
            if (!$prospectus) {
                Log::error("Prospectus not found for ID: {$salesRecord->prospectus_id}");
                DB::rollBack();
                return null;
            }

            // Create customer from prospect details
            $customer = Customer::create([
                'name' => $salesRecord->leads_name,
                'email' => $salesRecord->email,
                'phone' => $salesRecord->contact_number,
                'address' => $salesRecord->address,
                'company_name' => $prospectus->prospectus_name,
                'tenant_id' => $salesRecord->tenant_id,
                'prospectus_id' => $salesRecord->prospectus_id,
            ]);

            // Update sales record with customer_id
            $salesRecord->update(['customer_id' => $customer->id]);

            Log::info("Successfully converted prospect to customer. Customer ID: {$customer->id}, Prospect ID: {$salesRecord->prospectus_id}");

            DB::commit();
            return $customer;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error converting prospect to customer: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a sales record should be converted to customer
     */
    public function shouldConvertToCustomer(SalesRecord $salesRecord)
    {
        // Check if status is Close_win (ID = 1)
        return $salesRecord->status_id == 1;
    }

    /**
     * Process all sales records that should be converted to customers
     */
    public function processPendingConversions()
    {
        $pendingRecords = SalesRecord::where('status_id', 1)
            ->whereDoesntHave('customer', function($query) {
                $query->whereColumn('customers.prospectus_id', 'sales_records.prospectus_id');
            })
            ->get();

        $convertedCount = 0;
        foreach ($pendingRecords as $record) {
            if ($this->convertProspectToCustomer($record)) {
                $convertedCount++;
            }
        }

        Log::info("Processed {$convertedCount} prospect to customer conversions");
        return $convertedCount;
    }
}
