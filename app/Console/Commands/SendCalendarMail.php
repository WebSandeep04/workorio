<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\CalendarMailReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendCalendarMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:send-mail {--date= : The target date (YYYY-MM-DD)} {--alert=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send pending calendar events and monthly summary email to users with calendar active.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Calendar mail generation...');

        $tenants = Tenant::on('mysql')->where('is_sales_enabled', 1)->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Found {$tenants->count()} tenants. Processing...");

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Calendar email generation completed for all tenants.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $targetDate = $this->option('date');
            if (!$targetDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $targetDate)) {
                $targetDate = Carbon::today('Asia/Kolkata')->format('Y-m-d');
            }

            $currentMonth = Carbon::parse($targetDate)->format('Y-m');
            $monthStart = Carbon::parse($targetDate)->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::parse($targetDate)->endOfMonth()->format('Y-m-d');

            $startDate = $monthStart;
            $endDate = Carbon::parse($targetDate)->addDays(2)->format('Y-m-d');

            // Find Recipients
            // Note: The raw PHP script checked for `is_calander` or `is_calendar`. 
            // In Eloquent we can try both by looking at the schema builder if needed, 
            // but assuming `is_calendar` is the correct intended model column moving forward.
            // If it can crash, we verify using Schema builder.
            $hasCalendarCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_calendar');
            $hasCalanderCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_calander');
            
            $colName = null;
            if ($hasCalendarCol) $colName = 'is_calendar';
            elseif ($hasCalanderCol) $colName = 'is_calander';

            if (!$colName) {
                $this->info("  ⚠️ Neither 'is_calendar' nor 'is_calander' columns found in users table for {$tenant->tenant_name}. Skipping.");
                return;
            }

            $recipientEmails = User::whereHas('employee', function ($q) {
                    $q->where('status', 'active');
                })
                ->where($colName, 1)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter(function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                })
                ->toArray();

            if (empty($recipientEmails)) {
                $this->info("  No recipients found with {$colName} = 1 in {$tenant->tenant_name}.");
                return;
            }

            // query 1: Tomorrow's / Range Events
            $sqlTomorrow = "
            SELECT 
              e.name AS item_name,
              c.name AS client_name,
              e.event_date AS event_date,
              dcs.status_id AS status_id,
              cs.name AS status_name,
              dcs.missed_reason_id AS missed_reason_id,
              mr.name AS missed_reason_name,
              0 AS alert_before_days
            FROM calendar_events e
            JOIN calendar_event_client ec ON ec.event_id = e.id
            JOIN calendar_clients c ON c.id = ec.client_id
            LEFT JOIN calendar_date_client_statuses dcs
              ON dcs.event_date = e.event_date AND dcs.client_id = ec.client_id
            LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
            LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
            WHERE e.event_date BETWEEN ? AND ?
              AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

            UNION ALL

            SELECT
              ce.name AS item_name,
              c.name AS client_name,
              ccce.event_date AS event_date,
              dcs.status_id AS status_id,
              cs.name AS status_name,
              dcs.missed_reason_id AS missed_reason_id,
              mr.name AS missed_reason_name,
              ce.alert_before_days AS alert_before_days
            FROM calendar_client_common_events ccce
            JOIN common_events ce ON ce.id = ccce.common_event_id
            JOIN calendar_clients c ON c.id = ccce.client_id
            LEFT JOIN calendar_date_client_statuses dcs
              ON dcs.event_date = ccce.event_date AND dcs.client_id = ccce.client_id
            LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
            LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
            WHERE (ccce.event_date BETWEEN ? AND ? 
                   OR (ccce.event_date > ? AND ? >= DATE_SUB(ccce.event_date, INTERVAL ce.alert_before_days DAY)))
              AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

            ORDER BY event_date DESC, client_name, item_name
            ";

            $rangeEventsRaw = DB::select($sqlTomorrow, [$startDate, $endDate, $startDate, $endDate, $endDate, $targetDate]);
            $rangeEvents = collect($rangeEventsRaw)->map(function ($item) { return (array) $item; })->toArray();


            // Query 2: Monthly Breakdown Events
            $sqlMonthly = "
            SELECT 
              c.name AS client_name,
              e.name AS item_name,
              e.event_date AS event_date,
              dcs.status_id AS status_id,
              cs.name AS status_name,
              dcs.missed_reason_id AS missed_reason_id,
              mr.name AS missed_reason_name,
              'Event' AS source_type,
              0 AS alert_before_days
            FROM calendar_events e
            JOIN calendar_event_client ec ON ec.event_id = e.id
            JOIN calendar_clients c ON c.id = ec.client_id
            LEFT JOIN calendar_date_client_statuses dcs
              ON dcs.event_date = e.event_date AND dcs.client_id = ec.client_id
            LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
            LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
            WHERE e.event_date >= ? AND e.event_date <= ?
              AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

            UNION ALL

            SELECT
              c.name AS client_name,
              ce.name AS item_name,
              ccce.event_date AS event_date,
              dcs.status_id AS status_id,
              cs.name AS status_name,
              dcs.missed_reason_id AS missed_reason_id,
              mr.name AS missed_reason_name,
              'Common Event' AS source_type,
              ce.alert_before_days AS alert_before_days
            FROM calendar_client_common_events ccce
            JOIN common_events ce ON ce.id = ccce.common_event_id
            JOIN calendar_clients c ON c.id = ccce.client_id
            LEFT JOIN calendar_date_client_statuses dcs
              ON dcs.event_date = ccce.event_date AND dcs.client_id = ccce.client_id
            LEFT JOIN calendar_statuses cs ON cs.id = dcs.status_id
            LEFT JOIN calendar_missed_reasons mr ON mr.id = dcs.missed_reason_id
            WHERE ccce.event_date >= ? AND ccce.event_date <= ?
              AND (dcs.status_id IS NULL OR dcs.status_id <> 3)

            ORDER BY client_name, event_date, item_name
            ";

            $monthlyEventsRaw = DB::select($sqlMonthly, [$monthStart, $monthEnd, $monthStart, $monthEnd]);
            $monthlyEvents = collect($monthlyEventsRaw)->map(function ($item) { return (array) $item; })->toArray();

            // Setup Data Context
            $payload = [
                'alert_prefix' => $this->option('alert'),
                'targetDate' => $targetDate,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'currentMonth' => $currentMonth,
                'monthStart' => $monthStart,
                'monthEnd' => $monthEnd,
                'rangeEvents' => $rangeEvents,
                'monthlyEvents' => $monthlyEvents
            ];

            try {
                Mail::to($recipientEmails)->send(new CalendarMailReport($payload, $endDate));
                $this->info("  ✅ Calendar report sent to " . count($recipientEmails) . " users.");
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
