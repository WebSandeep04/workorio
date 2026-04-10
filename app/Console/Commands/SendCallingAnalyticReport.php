<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Mail\CallingAnalyticReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class SendCallingAnalyticReport extends Command
{
    protected $signature = 'calling:send-analytic-report';
    protected $description = 'Send daily calling analytic reports to admins for all tenants.';

    public function handle()
    {
        $this->info('Starting Daily Calling Analytic Report generation...');

        $tenants = Tenant::on('mysql')->get();

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        foreach ($tenants as $tenant) {
            $this->processTenant($tenant);
        }

        $this->info('Calling analytic report generation completed.');
        return 0;
    }

    private function processTenant(Tenant $tenant)
    {
        $this->line("Processing Tenant: {$tenant->tenant_name} (ID: {$tenant->id})");

        try {
            TenantDatabaseService::setDefaultConnection($tenant->id);
            
            $today = Carbon::today()->toDateString();

            // Get all possible calling types for headers
            $allCallingTypes = DB::table('calling_types')->orderBy('name')->pluck('name')->toArray();
            
            // Get recipients
            if ($tenant->id == 1) {
                $recipientEmails = ['shamshad@triserv360.com'];
            } else {
                $recipientEmails = User::whereHas('employee', function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where('role_id', 1)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
            }

            if (empty($recipientEmails)) {
                $this->info("  No active admin recipients for {$tenant->tenant_name}.");
                return;
            }

            // Fetch Active Users
            $users = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })->get();

            $userData = [];
            $userStatusCounts = []; // Matrix: [username][status_name] = count
            $userLeads = [];

            foreach ($users as $user) {
                $userId = $user->id;
                
                // Base Query per user (Only locked leads)
                $baseQuery = DB::table('calling_campaign_calling')
                    ->where('user_id', $userId)
                    ->where('is_locked', 1);

                $allLeadsCount = (clone $baseQuery)->count();
                
                // Dashboard Metrics
                $todayFollowups = (clone $baseQuery)->where(function ($q) use ($today) {
                    $q->whereDate('next_followup_date', '<=', $today)
                      ->orWhere(function ($sq) use ($today) {
                          $sq->whereDate('next_followup_date', '>', $today)
                             ->whereDate('updated_at', $today);
                      });
                })->count();

                $underProcess = (clone $baseQuery)
                    ->whereDate('updated_at', $today)
                    ->whereDate('next_followup_date', $today)
                    ->count();

                $todayCompleted = (clone $baseQuery)
                    ->whereDate('updated_at', $today)
                    ->whereDate('next_followup_date', '>', $today)
                    ->count();

                $todayPending = (clone $baseQuery)
                    ->where(function ($q) use ($today) {
                        $q->whereDate('next_followup_date', '<=', $today)
                          ->orWhereNull('next_followup_date');
                    })->count();

                $todayNew = (clone $baseQuery)
                    ->whereDate('created_at', $today)
                    ->count();

                if ($allLeadsCount > 0 || $todayFollowups > 0) {
                    $userData[] = [
                        'name' => $user->name,
                        'allLeads' => $allLeadsCount,
                        'todayFollowups' => $todayFollowups,
                        'underProcess' => $underProcess,
                        'todayCompleted' => $todayCompleted,
                        'todayPending' => $todayPending,
                        'todayNew' => $todayNew
                    ];

                    // Matrix preparation: Initialize all statuses to 0
                    $userStatusCounts[$user->name] = array_fill_keys($allCallingTypes, 0);

                    // User Status Counts (Today's activity only)
                    $statusCounts = DB::table('calling_campaign_calling')
                        ->join('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
                        ->where('calling_campaign_calling.user_id', $userId)
                        ->whereDate('calling_campaign_calling.updated_at', $today)
                        ->select('calling_types.name', DB::raw('count(*) as count'))
                        ->groupBy('calling_types.name')
                        ->get();
                    
                    foreach($statusCounts as $sc) {
                        if (isset($userStatusCounts[$user->name][$sc->name])) {
                            $userStatusCounts[$user->name][$sc->name] = $sc->count;
                        }
                    }

                    // User Detailed Leads
                    $leads = DB::table('calling_campaign_calling')
                        ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
                        ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
                        ->leftJoin('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
                        ->where('calling_campaign_calling.user_id', $userId)
                        ->whereDate('calling_campaign_calling.updated_at', $today)
                        ->select(
                            'callings.name as lead_name',
                            'callings.phone',
                            'callings.email',
                            'callings.city',
                            'callings.state',
                            'calling_campaigns.name as campaign_name',
                            'calling_types.name as status_name',
                            'calling_campaign_calling.updated_at',
                            'calling_campaign_calling.next_followup_date',
                            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
                        )
                        ->orderBy('calling_campaign_calling.updated_at', 'DESC')
                        ->get();

                    if (!$leads->isEmpty()) {
                        $userLeads[$user->name] = $leads;
                    }
                }
            }

            $globalStatusData = DB::table('calling_campaign_calling')
                ->join('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
                ->whereDate('calling_campaign_calling.updated_at', $today)
                ->select('calling_types.name', DB::raw('count(*) as count'))
                ->groupBy('calling_types.name')
                ->get();

            if (empty($userData)) {
                $this->info("  No calling activity today for {$tenant->tenant_name}");
                return;
            }

            // Send Mail
            Mail::to($recipientEmails)->send(new CallingAnalyticReport(
                $userData, 
                $globalStatusData, 
                Carbon::today()->format('d M, Y'), 
                $tenant->tenant_name,
                $userStatusCounts,
                $userLeads,
                $allCallingTypes
            ));
            $this->info("  ✅ Report sent to: " . implode(', ', $recipientEmails));

        } catch (Exception $e) {
            $this->error("Failed to process tenant {$tenant->tenant_name}: " . $e->getMessage());
        } finally {
            DB::setDefaultConnection('mysql');
        }
    }
}
