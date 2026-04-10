<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CallingType;

class CallingDashboardController extends Controller
{
    private function getCurrentUserId()
    {
        return Auth::id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }

    public function todayFollowupsTable() { return view('calling.status_table', ['title' => "Today's Followups", 'data_url' => route('calling.todayfollowupstabledata')]); }
    public function underProcessTable() { return view('calling.status_table', ['title' => 'Under Process', 'data_url' => route('calling.underprocesstabledata')]); }
    public function todayCompletedTable() { return view('calling.status_table', ['title' => 'Today Completed', 'data_url' => route('calling.todaycompletedtabledata')]); }
    public function todayPendingTable() { return view('calling.status_table', ['title' => 'Today Pending', 'data_url' => route('calling.todaypendingtabledata')]); }
    public function todayNewTable() { return view('calling.status_table', ['title' => 'New Followups', 'data_url' => route('calling.todaynewtabledata')]); }
    public function allLeadsTable() { return view('calling.status_table', ['title' => 'My All Leads', 'data_url' => route('calling.allleadstabledata')]); }

    // Dashboard count methods
    public function todayFollowups() { return response()->json(['totalLeads' => $this->todayFollowupsTableData(request())->getData()->total]); }
    public function underProcess() { return response()->json(['underprocess' => $this->underProcessTableData(request())->getData()->total ?? 0]); }
    public function todayCompleted() { return response()->json(['todaycompleted' => $this->todayCompletedTableData(request())->getData()->total]); }
    public function todayPending() { return response()->json(['todaypending' => $this->todayPendingTableData(request())->getData()->total]); }
    public function todayNew() { return response()->json(['todaynew' => $this->todayNewTableData(request())->getData()->total]); }
    public function allLeads() { return response()->json(['allleads' => $this->getBaseTableQuery($this->getCurrentUserId())->count()]); }

    private function getBaseTableQuery($userId)
    {
        return DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_types.name as status_name',
                'calling_campaign_calling.next_followup_date as pivot_followup',
                DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark')
            );
    }

    public function todayFollowupsTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();
        $query = $this->getBaseTableQuery($userId);
        
        $query->where(function ($q) use ($today) {
            $q->whereDate('calling_campaign_calling.next_followup_date', '<=', $today)
              ->orWhere(function ($sq) use ($today) {
                  $sq->whereDate('calling_campaign_calling.next_followup_date', '>', $today)
                     ->whereDate('calling_campaign_calling.updated_at', $today);
              });
        });

        return response()->json($query->paginate(10));
    }

    public function underProcessTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $statusIds = CallingType::where('name', 'like', '%Under Process%')->orWhere('name', 'like', '%In Progress%')->pluck('id');
        $query = $this->getBaseTableQuery($userId);
        if ($statusIds->isNotEmpty()) {
            $query->whereIn('calling_campaign_calling.calling_type_id', $statusIds);
        } else {
            return response()->json(['data' => []]);
        }
        return response()->json($query->paginate(10));
    }

    public function todayCompletedTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();
        $query = $this->getBaseTableQuery($userId)
            ->whereDate('calling_campaign_calling.updated_at', $today)
            ->where(function($q) use ($today) {
                $q->whereDate('calling_campaign_calling.next_followup_date', '>', $today)
                  ->orWhereNull('calling_campaign_calling.next_followup_date');
            });
        return response()->json($query->paginate(10));
    }

    public function todayPendingTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();
        $query = $this->getBaseTableQuery($userId)
            ->where(function($q) use ($today) {
                $q->whereDate('calling_campaign_calling.next_followup_date', '<', $today)
                  ->orWhere(function($sq) use ($today) {
                      $sq->whereDate('calling_campaign_calling.next_followup_date', $today)
                         ->whereDate('calling_campaign_calling.updated_at', '<', $today);
                  });
            });
        return response()->json($query->paginate(10));
    }

    public function todayNewTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $today = Carbon::today()->toDateString();
        $query = $this->getBaseTableQuery($userId)
            ->whereDate('calling_campaign_calling.updated_at', $today)
            ->whereDate('calling_campaign_calling.created_at', '<=', $today);
        return response()->json($query->paginate(10));
    }

    public function allLeadsTableData(Request $request)
    {
        $userId = $this->getCurrentUserId();
        return response()->json($this->getBaseTableQuery($userId)->paginate(10));
    }

    /**
     * Calling Analytics View
     */
    public function analytics()
    {
        return view('calling.analytics');
    }

    public function getAnalyticsData()
    {
        $userId = $this->getCurrentUserId();
        
        // Get trends (last 7 days or months)
        $trends = DB::table('calling_campaign_calling')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get distribution
        $distribution = DB::table('calling_campaign_calling')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->select('calling_types.name', DB::raw('count(*) as count'))
            ->where('calling_campaign_calling.user_id', $userId)
            ->where('calling_campaign_calling.is_locked', 1)
            ->groupBy('calling_types.name')
            ->get();

        return response()->json([
            'trends' => $trends,
            'distribution' => $distribution,
            'user_info' => [
                'name' => Auth::user()->name ?? session('user_name'),
                'total_leads' => $this->allLeads()->getData()->allleads
            ]
        ]);
    }
}
