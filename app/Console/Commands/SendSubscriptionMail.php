<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\SubscriptionMailReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendSubscriptionMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-mail {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily subscription summary and overdue alerts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Subscription mail generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Subscription email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);

            // Determine recipient emails.
            // Check if there is an is_subscription column in users table
            $hasIsSubscription = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_subscription');
            
            $recipientEmails = [];
            if ($hasIsSubscription) {
                $recipientEmails = User::where('is_subscription', 1)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
            } 

            $recipientEmails = array_filter($recipientEmails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

           

            if (empty($recipientEmails)) {
                $this->info("  ⚠️ No recipients found for {$tenant->tenant_name}. Skipping.");
                return;
            }

            // Subscriptions query
            $sql = "
                SELECT 
                    s.id,
                    s.subscription_name,
                    s.amount,
                    s.recurrence_type,
                    s.billing_type,
                    s.created_at as sub_created_at,
                    c.name AS customer_name, 
                    c.company_name, 
                    c.phone,
                    c.email,
                    p.product_name,
                    h.period_start,
                    h.period_end,
                    h.due_date,
                    h.status AS history_status,
                    h.id AS history_id,
                    h.amount AS history_amount,
                    h.created_at AS history_created_at,
                    h.updated_at AS history_updated_at
                FROM subscriptions s 
                LEFT JOIN customers c ON s.customer_id = c.id 
                LEFT JOIN sales_products p ON s.product_id = p.id 
                LEFT JOIN subscription_histories h ON s.id = h.subscription_id
                WHERE s.is_active = 1 
                ORDER BY c.name ASC, h.due_date DESC
            ";

            $results = DB::select($sql);

            $overdueItems = [];
            $statusGroups = []; 
            $totalActive = 0;
            $totalReceivable = 0;
            $today = Carbon::today('Asia/Kolkata')->format('Y-m-d');

            if (!empty($results)) {
                foreach ($results as $row) {
                    $row = (array) $row;
                    $totalActive++;
                    
                    $statusRaw = $row['history_status'] ?? 'Pending';
                    $statusKey = ucwords(strtolower($statusRaw));
                    $dueDate = $row['due_date'];
                    
                    if (in_array(strtolower($statusRaw), ['payment received', 'last payment received'])) {
                        continue;
                    }

                    $item = [
                        'customer' => $row['customer_name'] ?: ($row['company_name'] ?: 'Unknown'),
                        'product' => $row['product_name'] ?: ($row['subscription_name'] ?: 'Sub #' . $row['id']),
                        'amount' => (float)$row['amount'],
                        'due_date' => $dueDate,
                        'status' => $statusKey, 
                        'recurrence' => ucfirst($row['recurrence_type'] ?? 'One Time'),
                        'billing' => ucfirst($row['billing_type'] ?? '-'),
                    ];

                    $isPaid = in_array(strtolower($statusKey), ['paid', 'payment received', 'completed']);
                    $isOverdue = (!$isPaid && $dueDate && $dueDate < $today);

                    if ($isOverdue) {
                        $daysOver = Carbon::parse($dueDate)->diffInDays(Carbon::parse($today));
                        $item['notes'] = floor($daysOver) . " days overdue";
                        $overdueItems[] = $item;
                        $totalReceivable += $item['amount'];
                    } else {
                        if (!$isPaid) {
                             $totalReceivable += $item['amount'];
                             if ($dueDate) {
                                 // Check positive difference (future date)
                                 if (Carbon::parse($dueDate)->greaterThanOrEqualTo(Carbon::parse($today))) {
                                    $daysLeft = Carbon::parse($today)->diffInDays(Carbon::parse($dueDate));
                                    if ($daysLeft >= 0 && $daysLeft <= 7) {
                                        $item['notes'] = "Due in " . ceil($daysLeft) . " days";
                                    }
                                 }
                             }
                        }
                        $statusGroups[$statusKey][] = $item;
                    }
                }
            }
            
            ksort($statusGroups);

            $payload = [
                'alert_prefix' => $this->option('alert'),
                'overdueItems' => $overdueItems,
                'statusGroups' => $statusGroups,
                'totalActive' => $totalActive,
                'totalReceivable' => $totalReceivable,
                'dateDisplay' => Carbon::now('Asia/Kolkata')->format('d M Y')
            ];

            try {
                Mail::to($recipientEmails)->send(new SubscriptionMailReport($payload));
                $this->info("  ✅ Subscription report sent to " . count($recipientEmails) . " users.");
            } catch (\Exception $e) {
                $this->error("  ❌ Mailer Error: " . $e->getMessage());
            }

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
