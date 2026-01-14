<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{
public function todayfollowups()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $totalLeads = DB::table('sales_records')
        ->where('user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('next_follow_up_date', '>', $today)
                        ->whereDate('updatedat', $today);
                  });
        })
        ->whereNotIn('status_id', [1, 2,15,20])
        ->count();

    return response()->json(['totalLeads' => $totalLeads]);
}


 public function underprocess()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $underprocess = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2,15,20])
        ->whereDate('updatedat', $today)
        ->whereDate('next_follow_up_date', $today)
        ->count();

    return response()->json(['underprocess' => $underprocess]);
}

 public function todaycompleted()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $todaycompleted = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2,15,20])
        ->whereDate('updatedat', $today)
        ->whereDate('next_follow_up_date', '>', $today)
        ->count();

    return response()->json(['todaycompleted' => $todaycompleted]);
}

public function todaypending()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $todaypending = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2,15,20])
        ->where(function ($query) use ($today) {
            $query->whereDate('next_follow_up_date', '<=', $today)
                  ->orWhereNull('next_follow_up_date');
        })
        ->count();

    return response()->json(['todaypending' => $todaypending]);
}

public function todaynew()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $todaynew = DB::table('sales_records')
        ->where('user_id', $userId)
        ->whereNotIn('status_id', [1, 2,15,20])
        ->whereDate('createdat', $today)
        ->count();

    return response()->json(['todaynew' => $todaynew]);
}

public function allleads()
{
    $userId = $this->getCurrentUserId();

    $leadCount = DB::table('sales_records')
        ->where('user_id', $userId)
        ->count();

    return response()->json(['allleads' => $leadCount]);
}


public function estimateticket()
{
    $userId = $this->getCurrentUserId();

    $estimateticket = DB::table('sales_records')
        ->where('user_id', $userId)
        ->sum('ticket_value');

    return response()->json(['estimateticket' => $estimateticket]);
}

public function ordersSummary()
{
    if (!Schema::hasTable('quotations')) {
        return response()->json([
            'today' => 0,
            'pending' => 0,
            'approved' => 0,
            'processing' => 0,
            'cancelled' => 0,
            'total' => 0,
        ]);
    }

    $today = Carbon::today();

    $totalOrders = DB::table('quotations')->count();
    $todayOrders = DB::table('quotations')->whereDate('created_at', $today)->count();

    $statusCounts = DB::table('quotations')
        ->selectRaw('LOWER(COALESCE(status, "draft")) as status_key, COUNT(*) as total')
        ->groupBy('status_key')
        ->pluck('total', 'status_key');

    $sumByStatus = function (array $labels) use ($statusCounts) {
        $sum = 0;
        foreach ($labels as $label) {
            $sum += $statusCounts[strtolower($label)] ?? 0;
        }
        return $sum;
    };

    $pending = $sumByStatus(['pending', 'awaiting approval', 'draft', 'sent', 'submitted']);
    $approved = $sumByStatus(['approved', 'accepted', 'confirmed', 'won', 'closed won']);
    $processing = $sumByStatus(['processing', 'in production', 'dispatched', 'in-progress', 'in progress']);
    $cancelled = $sumByStatus(['cancelled', 'canceled', 'declined', 'lost', 'rejected', 'closed lost']);

    return response()->json([
        'today' => $todayOrders,
        'pending' => $pending,
        'approved' => $approved,
        'processing' => $processing,
        'cancelled' => $cancelled,
        'total' => $totalOrders,
    ]);
}

public function callingSummary()
{
    if (!Schema::hasTable('callings')) {
        return response()->json([
            'pending' => 0,
            'completed' => 0,
            'missed' => 0,
        ]);
    }

    $today = Carbon::today();

    $pending = Schema::hasColumn('callings', 'next_follow_up_date')
        ? DB::table('callings')
            ->where(function ($query) use ($today) {
                $query->whereNull('next_follow_up_date')
                      ->orWhereDate('next_follow_up_date', '>=', $today);
            })
            ->count()
        : DB::table('callings')->count();

    $completed = Schema::hasColumn('callings', 'updated_at')
        ? DB::table('callings')->whereDate('updated_at', $today)->count()
        : 0;

    $missed = Schema::hasColumn('callings', 'next_follow_up_date')
        ? DB::table('callings')
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', '<', $today)
            ->count()
        : 0;

    return response()->json([
        'pending' => $pending,
        'completed' => $completed,
        'missed' => $missed,
    ]);
}

public function worklogSummary()
{
    if (!Schema::hasTable('worklogs')) {
        return response()->json([
            'total_entries' => 0,
            'today_entries' => 0,
            'hours_captured' => 0,
        ]);
    }

    $today = Carbon::today();

    $totalEntries = DB::table('worklogs')->count();
    $todayEntries = DB::table('worklogs')->whereDate('work_date', $today)->count();
    $minutes = DB::table('worklogs')
        ->selectRaw('COALESCE(SUM(hours * 60 + minutes), 0) as total_minutes')
        ->value('total_minutes');

    $hoursCaptured = round(($minutes ?? 0) / 60, 1);

    return response()->json([
        'total_entries' => $totalEntries,
        'today_entries' => $todayEntries,
        'hours_captured' => $hoursCaptured,
    ]);
}

public function calendarSummary()
{
    if (!Schema::hasTable('calendar_events')) {
        return response()->json([
            'total_events' => 0,
            'today_events' => 0,
            'upcoming_week' => 0,
        ]);
    }

    $today = Carbon::today();
    $hasEventDate = Schema::hasColumn('calendar_events', 'event_date');

    $totalEvents = DB::table('calendar_events')->count();
    $todayEvents = $hasEventDate
        ? DB::table('calendar_events')->whereDate('event_date', $today)->count()
        : 0;

    $upcomingWeek = $hasEventDate
        ? DB::table('calendar_events')
            ->whereBetween('event_date', [$today->copy()->addDay(), $today->copy()->addDays(7)])
            ->count()
        : 0;

    return response()->json([
        'total_events' => $totalEvents,
        'today_events' => $todayEvents,
        'upcoming_week' => $upcomingWeek,
    ]);
}

public function attendanceSummary()
{
    if (!Schema::hasTable('attendance')) {
        return response()->json([
            'present' => 0,
            'absent' => 0,
            'late' => 0,
        ]);
    }

    $today = Carbon::today();

    $present = DB::table('attendance')->whereDate('date', $today)->count();
    $totalUsers = Schema::hasTable('users') ? DB::table('users')->count() : $present;
    $absent = max(($totalUsers ?? 0) - $present, 0);

    $late = 0;
    if (Schema::hasTable('movements')) {
        $cutoff = (clone $today)->setTime(10, 30);
        $late = DB::table('movements as m')
            ->join('attendance as a', 'a.id', '=', 'm.attendance_id')
            ->whereDate('a.date', $today)
            ->where('m.movement_action', 'in')
            ->select('a.id', DB::raw('MIN(m.time) as first_in'))
            ->groupBy('a.id')
            ->havingRaw('MIN(m.time) > ?', [$cutoff->toDateTimeString()])
            ->count();
    }

    return response()->json([
        'present' => $present,
        'absent' => $absent,
        'late' => $late,
    ]);
}

public function piedata()
{
    $data = DB::table('sales_status as ss')
        ->leftJoin('sales_records as sr', 'ss.id', '=', 'sr.status_id')
        ->select('ss.status_name', DB::raw('COUNT(sr.id) as count'))
        ->groupBy('ss.status_name')
        ->get();

    return response()->json($data);
}


public function userTasks()
{
    $userId = $this->getCurrentUserId();
    
    if (!$userId) {
        return response()->json(['tasks' => []]);
    }
    
    $tasks = DB::table('tasks as t')
        ->leftJoin('users as u', 't.user_id', '=', 'u.id')
        ->leftJoin('customers as c', 't.customer_id', '=', 'c.id')
        ->leftJoin('task_statuses as ts', 't.task_status_id', '=', 'ts.id')
        ->leftJoin('task_priorities as tp', 't.task_priority_id', '=', 'tp.id')
        ->where('t.user_id', $userId)
        ->select(
            't.id',
            't.task_name',
            't.task',
            't.is_done',
            't.created_at',
            'u.name as user_name',
            'c.name as customer_name',
            'ts.name as status_name',
            'ts.color as status_color',
            'tp.name as priority_name',
            'tp.color as priority_color'
        )
        ->orderBy('t.created_at', 'desc')
        ->limit(10)
        ->get();
    
    return response()->json(['tasks' => $tasks]);
}

public function bardata()
{
    $userId = $this->getCurrentUserId();
    $monthlyData = DB::table('sales_records')
        ->selectRaw('MONTH(createdat) as month, COUNT(*) as count')
        ->whereYear('createdat', Carbon::now()->year)
        ->where('user_id', $userId) // Optional
        ->groupBy(DB::raw('MONTH(createdat)'))
        ->pluck('count', 'month');

    // Format data for Chart.js
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $data = [];

    foreach (range(1, 12) as $month) {
        $data[] = $monthlyData[$month] ?? 0;
    }

    return response()->json([
        'labels' => $months,
        'data' => $data
    ]);
}


public function todayfollowupstable(){
    return view('todayfollowupstable');
}

public function underprocesstable(){
    return view('underprocess');
}

public function todaycompletedtable(){
    return view('todaycompletedtable');
}
public function todaypendingtable(){
    return view('todaypendingtable');
}
public function todaynewtable(){
    return view('todaynewtable');
}



// for table data


public function todayfollowupstabledata()
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();

    $records = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin(DB::raw('(
            SELECT r1.id, r1.sales_remark_id, r1.remark
            FROM remarks r1
            INNER JOIN (
                SELECT sales_remark_id, MAX(remark_date) as latest_date
                FROM remarks
                GROUP BY sales_remark_id
            ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
        ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
        ->where('sales_records.user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                        ->whereDate('sales_records.updatedat', '=', $today);
                  });
        })
        ->whereNotIn('sales_records.status_id', [1, 2,15,20])
        ->orderBy('sales_records.next_follow_up_date', 'asc')
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'latest_remarks.remark as latest_remark'
        )->paginate(5);

    return response()->json($records);
}


public function todayunderprocessfollowupstabledata()
{
    $userId = $this->getCurrentUserId();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
    ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '=', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2,15,20])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}
public function todaycompletedfollowupstabledata()
{
    $userId = $this->getCurrentUserId();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->join('states', 'sales_records.state_id', '=', 'states.id')
    ->join('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '>', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(10);


    return response()->json($records);
}
public function todaypendingfollowupstabledata()
{
    $userId = $this->getCurrentUserId();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
    ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->where(function ($query) use ($today) {
        $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
              ->orWhereNull('sales_records.next_follow_up_date');
    })
    // ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(20);


    return response()->json($records);
}
public function todaynewfollowupstabledata()
{
    $userId = $this->getCurrentUserId();
    $today =Carbon::today()->toDateString();

$records = DB::table('sales_records')
    ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
    ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
    ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
    ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
    ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
    ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
    ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
    ->leftJoin(DB::raw('(
        SELECT r1.id, r1.sales_remark_id, r1.remark
        FROM remarks r1
        INNER JOIN (
            SELECT sales_remark_id, MAX(remark_date) as latest_date
            FROM remarks
            GROUP BY sales_remark_id
        ) r2 ON r1.sales_remark_id = r2.sales_remark_id AND r1.remark_date = r2.latest_date
    ) as latest_remarks'), 'sales_records.id', '=', 'latest_remarks.sales_remark_id')
    ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.createdat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc')
    ->select(
        'sales_records.*',
        'sales_status.status_name',
        'prospectuses.prospectus_name',
        'sales_business_types.business_name',
        'sales_lead_sources.source_name',
        'sales_products.product_name',
        'states.state_name',
        'cities.city_name',
        'latest_remarks.remark as latest_remark'
    )
    ->paginate(20);


    return response()->json($records);
}


// for searching

public function searchFollowups(Request $request)
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->join('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->join('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->join('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->join('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->join('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->join('states', 'sales_records.state_id', '=', 'states.id')
        ->join('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
                ->where('sales_records.user_id', $userId)
        ->where(function ($query) use ($today) {
            $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
                  ->orWhere(function ($q) use ($today) {
                      $q->whereDate('sales_records.next_follow_up_date', '>', $today)
                        ->whereDate('sales_records.updatedat', '=', $today);
                  });
        })
        ->whereNotIn('sales_records.status_id', [1, 2])
        ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('sales_records.leads_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_records.address', 'LIKE', "%{$search}%");
        });
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}



public function searchunderprocessFollowups(Request $request)
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
       ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '=', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('sales_records.leads_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_records.address', 'LIKE', "%{$search}%");
        });
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}

public function searchcompletedFollowups(Request $request)
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
           ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.next_follow_up_date', '>', $today)
    ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('sales_records.leads_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_records.address', 'LIKE', "%{$search}%");
        });
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}
public function searchpendingFollowups(Request $request)
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
           ->where('sales_records.user_id', $userId)
    ->where(function ($query) use ($today) {
        $query->whereDate('sales_records.next_follow_up_date', '<=', $today)
              ->orWhereNull('sales_records.next_follow_up_date');
    })
    // ->whereDate('sales_records.updatedat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('sales_records.leads_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_records.address', 'LIKE', "%{$search}%");
        });
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}
public function searchnewFollowups(Request $request)
{
    $userId = $this->getCurrentUserId();
    $today = Carbon::today()->toDateString();
    $search = $request->input('search');

    $query = DB::table('sales_records')
        ->leftJoin('sales_status', 'sales_records.status_id', '=', 'sales_status.id')
        ->leftJoin('prospectuses', 'sales_records.prospectus_id', '=', 'prospectuses.id')
        ->leftJoin('sales_business_types', 'sales_records.business_type_id', '=', 'sales_business_types.id')
        ->leftJoin('sales_lead_sources', 'sales_records.lead_source_id', '=', 'sales_lead_sources.id')
        ->leftJoin('sales_products', 'sales_records.products_id', '=', 'sales_products.id')
        ->leftJoin('states', 'sales_records.state_id', '=', 'states.id')
        ->leftJoin('cities', 'sales_records.city_id', '=', 'cities.id')
        ->leftJoin('remarks', function ($join) {
            $join->on('remarks.sales_remark_id', '=', 'sales_records.id')
                ->whereRaw('remarks.remark_date = (select max(r2.remark_date) from remarks r2 where r2.sales_remark_id = sales_records.id)');
        })
        ->select(
            'sales_records.*',
            'sales_status.status_name',
            'prospectuses.prospectus_name',
            'sales_business_types.business_name',
            'sales_lead_sources.source_name',
            'sales_products.product_name',
            'states.state_name',
            'cities.city_name',
            'remarks.remark as latest_remark'
        )
          ->where('sales_records.user_id', $userId)
    ->whereDate('sales_records.createdat', '=', $today)
    ->whereNotIn('sales_records.status_id', [1, 2, 15, 20])
    ->orderBy('sales_records.next_follow_up_date', 'asc');

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('sales_records.leads_name', 'LIKE', "%{$search}%")
              ->orWhere('sales_records.address', 'LIKE', "%{$search}%");
        });
    }

    $records = $query->orderBy('sales_records.next_follow_up_date')->get();

    return response()->json($records);
}











    /**
     * Get current user ID for both Auth and session-based authentication
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        return session('user_id');
    }
}