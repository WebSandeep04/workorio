<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeamAnalyticsController extends Controller
{
    public function index()
    {
        return view('team-analytics');
    }

    // Get team members for the current manager
    public function getTeamMembers()
    {
        $userId = Auth::id();

        $teamMembers = User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->where('users.id', '!=', $userId) // Exclude self
            ->select('id', 'name')
            ->get();

        return response()->json($teamMembers);
    }

    // Get analytics for a specific team member
    public function getMemberAnalytics(Request $request)
    {
        $userId = Auth::id();
        $memberId = $request->member_id;

        // Verify the member is a subordinate of the current user
        $isSubordinate = User::where('id', $memberId)
            ->whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->exists();

        if (!$isSubordinate) {
            return response()->json(['error' => 'Unauthorized access to team member data'], 403);
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();

        // Get all leads for the team member
        $allLeads = SalesRecord::where('sales_records.user_id', $memberId);

        // Total leads
        $totalLeads = $allLeads->count();

        // Leads this month
        $leadsThisMonth = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereMonth('createdat', $today->month)
            ->whereYear('createdat', $today->year)
            ->count();

        // Leads this week
        $leadsThisWeek = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereBetween('createdat', [$startOfWeek, $startOfWeek->copy()->endOfWeek()])
            ->count();

        // Leads today
        $leadsToday = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereDate('createdat', $today)
            ->count();

        // Follow-ups due today (using same logic as SalesDashboardController)
        $followUpsToday = SalesRecord::where('sales_records.user_id', $memberId)
            ->where(function ($query) use ($today) {
                $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                      ->orWhereDate('sales_records.next_follow_up_date', '=', $today)
                      ->orWhereNull('sales_records.next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                            ->whereDate('sales_records.updatedat', $today);
                      });
            })
            ->whereNotIn('sales_records.status_id', [1, 2])
            ->count();

        // Follow-ups this week
        $followUpsThisWeek = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereBetween('next_follow_up_date', [$startOfWeek, $startOfWeek->copy()->endOfWeek()])
            ->count();

        // Today Done (Today Completed)
        $todayDone = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereNotIn('sales_records.status_id', [1, 2])
            ->whereDate('sales_records.updatedat', $today)
            ->whereDate('sales_records.next_follow_up_date', '>', $today)
            ->count();

        // Today Pending
        $todayPending = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereNotIn('sales_records.status_id', [1, 2])
            ->where(function ($query) use ($today) {
                $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                      ->orWhereNull('sales_records.next_follow_up_date');
            })
            ->count();

        // Today Under Process
        $todayUnderProcess = SalesRecord::where('sales_records.user_id', $memberId)
            ->whereNotIn('sales_records.status_id', [1, 2])
            ->whereDate('sales_records.updatedat', $today)
            ->whereDate('sales_records.next_follow_up_date', $today)
            ->count();

        // Status distribution
        $statusDistribution = SalesRecord::where('sales_records.user_id', $memberId)
            ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->select('sales_status.status_name', DB::raw('count(*) as count'))
            ->groupBy('sales_status.status_name', 'sales_status.id')
            ->get();

        // Recent leads (last 10)
        $recentLeads = SalesRecord::where('sales_records.user_id', $memberId)
            ->with(['status', 'prospectus'])
            ->orderBy('createdat', 'desc')
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = SalesRecord::where('sales_records.user_id', $memberId)
                ->whereMonth('createdat', $month->month)
                ->whereYear('createdat', $month->year)
                ->count();
            
            $monthlyTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        return response()->json([
            'total_leads' => $totalLeads,
            'leads_this_month' => $leadsThisMonth,
            'leads_this_week' => $leadsThisWeek,
            'leads_today' => $leadsToday,
            'follow_ups_today' => $followUpsToday,
            'follow_ups_this_week' => $followUpsThisWeek,
            'today_done' => $todayDone,
            'today_pending' => $todayPending,
            'today_under_process' => $todayUnderProcess,
            'status_distribution' => $statusDistribution,
            'recent_leads' => $recentLeads,
            'monthly_trend' => $monthlyTrend
        ]);
    }

    // Get team overview analytics
    public function getTeamOverview()
    {
        $userId = Auth::id();

        // Get all subordinates
        $subordinateIds = User::whereHas('managers', function($q) use ($userId) {
                $q->where('manager_id', $userId);
            })
            ->pluck('users.id');

        if ($subordinateIds->isEmpty()) {
            return response()->json(['error' => 'No team members found'], 404);
        }

        $today = Carbon::today();

        // Team total leads
        $teamTotalLeads = SalesRecord::whereIn('sales_records.user_id', $subordinateIds)
            ->count();

        // Team leads today
        $teamLeadsToday = SalesRecord::whereIn('sales_records.user_id', $subordinateIds)
            ->whereDate('createdat', $today)
            ->count();

        // Team follow-ups today (using same logic as SalesDashboardController)
        $teamFollowUpsToday = SalesRecord::whereIn('sales_records.user_id', $subordinateIds)
            ->where(function ($query) use ($today) {
                $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                      ->orWhereDate('sales_records.next_follow_up_date', '=', $today)
                      ->orWhereNull('sales_records.next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                            ->whereDate('sales_records.updatedat', $today);
                      });
            })
            ->whereNotIn('sales_records.status_id', [1, 2])
            ->count();

        // Team status distribution
        $teamStatusDistribution = SalesRecord::whereIn('sales_records.user_id', $subordinateIds)
            ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
            ->select('sales_status.status_name', DB::raw('count(*) as count'))
            ->groupBy('sales_status.status_name', 'sales_status.id')
            ->get();

        // Individual member stats
        $memberStats = User::whereIn('id', $subordinateIds)
            ->select('id', 'name')
            ->get()
            ->map(function ($member) use ($today) {
                $totalLeads = SalesRecord::where('sales_records.user_id', $member->id)
                    ->count();

                $leadsToday = SalesRecord::where('sales_records.user_id', $member->id)
                    ->whereDate('createdat', $today)
                    ->count();

                $followUpsToday = SalesRecord::where('sales_records.user_id', $member->id)
                    ->where(function ($query) use ($today) {
                        $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                              ->orWhereDate('sales_records.next_follow_up_date', '=', $today)
                              ->orWhereNull('sales_records.next_follow_up_date')
                              ->orWhere(function ($q) use ($today) {
                                  $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                                    ->whereDate('sales_records.updatedat', $today);
                              });
                    })
                    ->whereNotIn('sales_records.status_id', [1, 2])
                    ->count();

                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'total_leads' => $totalLeads,
                    'leads_today' => $leadsToday,
                    'follow_ups_today' => $followUpsToday
                ];
            });

        return response()->json([
            'team_total_leads' => $teamTotalLeads,
            'team_leads_today' => $teamLeadsToday,
            'team_follow_ups_today' => $teamFollowUpsToday,
            'team_status_distribution' => $teamStatusDistribution,
            'member_stats' => $memberStats
        ]);
    }

    // Debug endpoint to check server setup
    public function debugRemarks(Request $request)
    {
        $salesRecordId = $request->input('sales_record_id', 24);
        
        return response()->json([
            'php_version' => phpversion(),
            'carbon_available' => class_exists('Carbon\Carbon'),
            'sales_record_id' => $salesRecordId,
            'db_connection' => config('database.default'),
            'test' => 'Debug endpoint working'
        ]);
    }

    // Get remarks for a specific sales record
    public function getRemarks(Request $request)
    {
        try {
            $salesRecordId = $request->input('sales_record_id');

            \Log::info('getRemarks called with ID: ' . $salesRecordId);

            if (!$salesRecordId) {
                \Log::warning('No sales_record_id provided');
                return response()->json(['error' => 'Sales record ID is required'], 400);
            }

            // Verify the sales record exists
            $salesRecord = SalesRecord::where('id', $salesRecordId)
                ->with(['prospectus', 'status', 'city', 'state', 'businessType', 'product', 'user'])
                ->first();

            if (!$salesRecord) {
                \Log::warning('Sales record not found: ' . $salesRecordId);
                return response()->json(['error' => 'Sales record not found'], 404);
            }

            // Get the creator name from lead_assignment_logs (first entry)
            $creator = DB::table('lead_assignment_logs')
                ->where('sales_record_id', $salesRecordId)
                ->join('users', 'lead_assignment_logs.assigned_by', '=', 'users.id')
                ->orderBy('lead_assignment_logs.id', 'asc')
                ->select('users.name')
                ->first();

            \Log::info('Sales record found: ' . $salesRecord->id);

            // Get all remarks for this sales record, ordered by date
            $remarks = \App\Models\Remark::where('sales_remark_id', $salesRecordId)
                ->orderBy('remark_date', 'desc')
                ->get()
                ->map(function ($remark) {
                    try {
                        return [
                            'id' => $remark->id,
                            'date' => $remark->remark_date ? $remark->remark_date->format('d/m/Y') : 'N/A',
                            'remark' => $remark->remark ?? '',
                            'created_at' => $remark->created_at ? $remark->created_at->format('d/m/Y H:i') : 'N/A'
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Error formatting remark: ' . $e->getMessage());
                        return [
                            'id' => $remark->id,
                            'date' => 'N/A',
                            'remark' => $remark->remark ?? '',
                            'created_at' => 'N/A'
                        ];
                    }
                });

            \Log::info('Remarks count: ' . $remarks->count());

            $response = [
                'sales_record' => [
                    'id' => $salesRecord->id,
                    'leads_name' => $salesRecord->leads_name ?? '-',
                    'contact_person' => $salesRecord->contact_person ?? '-',
                    'contact_number' => $salesRecord->contact_number ?? '-',
                    'email' => $salesRecord->email ?? '-',
                    'state_name' => optional($salesRecord->state)->state_name ?? 'N/A',
                    'city_name' => optional($salesRecord->city)->city_name ?? 'N/A',
                    'product_name' => optional($salesRecord->product)->product_name ?? 'N/A',
                    'business_name' => optional($salesRecord->businessType)->business_name ?? 'N/A',
                    'status_name' => optional($salesRecord->status)->status_name ?? 'N/A',
                    'ticket_value' => $salesRecord->ticket_value ?? '-',
                    'next_follow_up_date' => $salesRecord->next_follow_up_date ? Carbon::parse($salesRecord->next_follow_up_date)->format('d/m/Y') : 'N/A',
                    'owner_name' => optional($salesRecord->user)->name ?? 'N/A',
                    'created_by_name' => optional($creator)->name ?? 'N/A'
                ],
                'remarks' => $remarks
            ];

            \Log::info('Returning response successfully');
            return response()->json($response);
            
        } catch (\Exception $e) {
            \Log::error('Error in getRemarks: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Error loading remarks',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}
