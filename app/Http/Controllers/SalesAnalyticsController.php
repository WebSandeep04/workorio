<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SalesRecord;
use App\Models\Remark;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsController extends Controller
{
    public function index()
    {
        return view('sales-analytics.index');
    }

    // Get analytics for all data
    public function getAnalytics()
    {
        $analytics = [
            'user_distribution' => $this->getUserDistribution(),
            'lead_statistics' => $this->getLeadStatistics(),
            'followup_statistics' => $this->getFollowupStatistics(),
            'recent_activities' => $this->getRecentActivities()
        ];

        return response()->json($analytics);
    }

    // Get analytics filtered by specific user
    public function getUserAnalytics(Request $request)
    {
        $userId = $request->input('user_id');
        
        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
        }

        $user = User::with(['role', 'manager'])
            ->find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $analytics = [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->role_name ?? 'N/A',
                'manager' => $user->manager->name ?? 'No Manager',
                'is_manager' => $user->is_manager ? 'Yes' : 'No',
                'subordinates_count' => $user->subordinates()->count()
            ],
            'lead_statistics' => $this->getUserLeadStatistics($userId),
            'followup_statistics' => $this->getUserFollowupStatistics($userId),
            'recent_leads' => $this->getUserRecentLeads($userId),
            'performance_trends' => $this->getUserPerformanceTrends($userId)
        ];

        return response()->json($analytics);
    }

    // Get user distribution by role
    private function getUserDistribution()
    {
        return DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'roles.role_name',
                DB::raw('count(*) as count')
            )
            ->groupBy('roles.id', 'roles.role_name')
            ->orderBy('roles.role_name')
            ->get();
    }

    // Get lead statistics
    private function getLeadStatistics()
    {
        $stats = [
            'total_leads' => SalesRecord::count(),
            'leads_by_status' => DB::table('sales_records')
                ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
                ->select(
                    'sales_status.status_name',
                    DB::raw('count(*) as count')
                )
                ->groupBy('sales_status.id', 'sales_status.status_name')
                ->get(),
            'leads_by_month' => $this->getLeadsByMonth()
        ];

        return $stats;
    }

    // Get follow-up statistics
    private function getFollowupStatistics()
    {
        $today = Carbon::today();
        
        // Enhanced pending follow-ups logic exactly like TeamAnalyticsController
        $pendingFollowups = SalesRecord::where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereDate('next_follow_up_date', '=', $today)
                      ->orWhereNull('next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->whereNotIn('status_id', [1, 2]) // Exclude completed statuses
            ->count();
        
        // Today's specific statistics using same logic as SalesDashboardController
        $todayFollowups = SalesRecord::where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereDate('next_follow_up_date', '=', $today)
                      ->orWhereNull('next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->whereNotIn('status_id', [1, 2])
            ->count();
        
        $todayCompleted = SalesRecord::whereNotIn('status_id', [1, 2])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();
        
        $todayPending = SalesRecord::whereNotIn('status_id', [1, 2])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();
        
        $todayUnderProcess = SalesRecord::whereNotIn('status_id', [1, 2])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();
        
        $todayCreated = SalesRecord::whereNotIn('status_id', [1, 2])
            ->whereDate('createdat', $today)
            ->count();
        
        $stats = [
            'total_followups' => SalesRecord::whereNotNull('next_follow_up_date')->count(),
            'pending_followups' => $pendingFollowups,
            'today_followups' => $todayFollowups,
            'upcoming_followups' => SalesRecord::where('next_follow_up_date', '>', $today)->count(),
            'completed_followups' => SalesRecord::whereDate('next_follow_up_date', '>', $today)
                ->whereDate('updatedat', $today)
                ->whereNotIn('status_id', [1, 2])
                ->count(),
            // Today's specific statistics
            'today_completed' => $todayCompleted,
            'today_pending' => $todayPending,
            'today_under_process' => $todayUnderProcess,
            'today_created' => $todayCreated,
            'followups_by_user' => DB::table('sales_records')
                ->join('users', 'sales_records.user_id', '=', 'users.id')
                ->select(
                    'users.name',
                    DB::raw('count(*) as total'),
                    DB::raw('sum(case when next_follow_up_date < ? then 1 else 0 end) as pending'),
                    DB::raw('sum(case when date(next_follow_up_date) = ? then 1 else 0 end) as today')
                )
                ->whereNotNull('next_follow_up_date')
                ->groupBy('users.id', 'users.name')
                ->addBinding($today, 'select')
                ->addBinding($today->format('Y-m-d'), 'select')
                ->get()
        ];

        return $stats;
    }

    // Get recent activities
    private function getRecentActivities()
    {
        $activities = [];

        // Recent sales records
        $recentLeads = SalesRecord::with(['user'])
            ->orderBy('createdat', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentLeads as $lead) {
            $activities[] = [
                'type' => 'new_lead',
                'message' => "New lead '{$lead->leads_name}' created by {$lead->user->name}",
                'timestamp' => $lead->createdat->format('d/m/Y H:i'),
                'data' => $lead
            ];
        }

        // Recent remarks
        $recentRemarks = Remark::with(['salesRecord.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentRemarks as $remark) {
            $activities[] = [
                'type' => 'new_remark',
                'message' => "Remark added to lead '{$remark->salesRecord->leads_name}' by {$remark->salesRecord->user->name}",
                'timestamp' => $remark->created_at->format('d/m/Y H:i'),
                'data' => $remark
            ];
        }

        // Sort by timestamp and return top 15
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return array_slice($activities, 0, 15);
    }

    // Get user-specific lead statistics
    private function getUserLeadStatistics($userId)
    {
        $user = User::find($userId);
        
        return [
            'total_leads' => $user->salesRecords()->count(),
            'leads_by_status' => DB::table('sales_records')
                ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
                ->where('sales_records.user_id', $userId)
                ->select(
                    'sales_status.status_name',
                    DB::raw('count(*) as count')
                )
                ->groupBy('sales_status.id', 'sales_status.status_name')
                ->get(),
            'leads_by_month' => $this->getUserLeadsByMonth($userId),
            'recent_leads' => $user->salesRecords()
                ->orderBy('createdat', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    // Get user-specific follow-up statistics
    private function getUserFollowupStatistics($userId)
    {
        $today = Carbon::today();
        
        // Enhanced pending follow-ups logic exactly like TeamAnalyticsController
        $pendingFollowups = SalesRecord::where('user_id', $userId)
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereDate('next_follow_up_date', '=', $today)
                      ->orWhereNull('next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            ->whereNotIn('status_id', [1, 2]) // Exclude completed statuses
            ->count();
        
        // Today's specific statistics using same logic as SalesDashboardController
        $todayFollowups = SalesRecord::where('user_id', $userId)
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereDate('next_follow_up_date', '=', $today)
                      ->orWhereNull('next_follow_up_date')
                      ->orWhere(function ($q) use ($today) {
                          $q->whereDate('next_follow_up_date', '>', $today)
                            ->whereDate('updatedat', $today);
                      });
            })
            // ->whereNotIn('status_id', [1, 2])
            ->count();
        
        $todayCompleted = SalesRecord::where('user_id', $userId)
            // ->whereNotIn('status_id', [1, 2])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', '>', $today)
            ->count();
        
        $todayPending = SalesRecord::where('user_id', $userId)
            // ->whereNotIn('status_id', [1, 2])
            ->where(function ($query) use ($today) {
                $query->whereDate('next_follow_up_date', '<=', $today)
                      ->orWhereNull('next_follow_up_date');
            })
            ->count();
        
        $todayUnderProcess = SalesRecord::where('user_id', $userId)
            // ->whereNotIn('status_id', [1, 2])
            ->whereDate('updatedat', $today)
            ->whereDate('next_follow_up_date', $today)
            ->count();
        
        $todayCreated = SalesRecord::where('user_id', $userId)
            // ->whereNotIn('status_id', [1, 2])
            ->whereDate('createdat', $today)
            ->count();
        
        return [
            'total_followups' => SalesRecord::where('user_id', $userId)
                ->whereNotNull('next_follow_up_date')
                ->count(),
            'pending_followups' => $pendingFollowups,
            'today_followups' => $todayFollowups,
            'upcoming_followups' => SalesRecord::where('user_id', $userId)
                ->where('next_follow_up_date', '>', $today)
                ->count(),
            'completed_followups' => SalesRecord::where('user_id', $userId)
                ->whereDate('next_follow_up_date', '>', $today)
                ->whereDate('updatedat', $today)
                ->whereNotIn('status_id', [1, 2])
                ->count(),
            // Today's specific statistics
            'today_completed' => $todayCompleted,
            'today_pending' => $todayPending,
            'today_under_process' => $todayUnderProcess,
            'today_created' => $todayCreated
        ];
    }

    // Get user's recent leads
    private function getUserRecentLeads($userId)
    {
        return SalesRecord::with(['status', 'prospectus'])
            ->where('user_id', $userId)
            ->orderBy('createdat', 'desc')
            ->limit(10)
            ->get()
            ->map(function($lead) {
                return [
                    'id' => $lead->id,
                    'leads_name' => $lead->leads_name,
                    'contact_person' => $lead->contact_person,
                    'status' => $lead->status->status_name ?? 'N/A',
                    'prospectus' => $lead->prospectus->prospectus_name ?? 'N/A',
                    'created_at' => $lead->createdat->format('d/m/Y'),
                    'next_follow_up_date' => $lead->next_follow_up_date ? Carbon::parse($lead->next_follow_up_date)->format('d/m/Y') : 'N/A'
                ];
            });
    }

    // Get user performance trends
    private function getUserPerformanceTrends($userId)
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'leads_created' => SalesRecord::where('user_id', $userId)
                    ->whereDate('createdat', $date->format('Y-m-d'))
                    ->count(),
                'followups_completed' => SalesRecord::where('user_id', $userId)
                    ->whereIn('status_id', [1, 2])
                    ->whereDate('updatedat', $date->format('Y-m-d'))
                    ->count()
            ];
        }
        return $trends;
    }

    // Get leads by month
    private function getLeadsByMonth()
    {
        return DB::table('sales_records')
            ->select(
                DB::raw('DATE_FORMAT(createdat, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
    }

    // Get user leads by month
    private function getUserLeadsByMonth($userId)
    {
        return DB::table('sales_records')
            ->where('user_id', $userId)
            ->select(
                DB::raw('DATE_FORMAT(createdat, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
    }

    // Get all users for filter dropdown
    public function getUsers()
    {
        $users = User::with(['role', 'manager'])
            ->orderBy('name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->role_name ?? 'N/A',
                    'manager' => $user->manager->name ?? 'No Manager',
                    'leads_count' => $user->salesRecords()->count()
                ];
            });

        return response()->json($users);
    }
}
