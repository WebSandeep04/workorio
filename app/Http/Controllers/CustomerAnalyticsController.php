<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesRecord;
use App\Models\SalesStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerAnalyticsController extends Controller
{
    public function index()
    {
        return view('customer-analytics.index');
    }

    public function getCustomers()
    {
        $customers = Customer::orderBy('name')
            ->get(['id', 'name', 'company_name', 'email']);

        return response()->json($customers);
    }

    public function getCustomerAnalytics($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        // Get all leads for this customer (via prospectus_id)
        $leads = SalesRecord::where('prospectus_id', $customer->prospectus_id)
            ->with(['status', 'user', 'prospectus'])
            ->get();

        // Calculate statistics
        $totalLeads = $leads->count();
        $closeWinLeads = $leads->where('status_id', 1)->count();
        $closeLostLeads = $leads->where('status_id', 2)->count();
        $activeLeads = $leads->whereNotIn('status_id', [1, 2])->count();

        // Get leads by status
        $leadsByStatus = $leads->groupBy('status.status_name')
            ->map(function($group) {
                return $group->count();
            });

        // Get recent leads (last 30 days)
        $recentLeads = $leads->where('createdat', '>=', Carbon::now()->subDays(30))->count();

        // Get leads by month (last 6 months)
        $leadsByMonth = $leads->groupBy(function($lead) {
            return Carbon::parse($lead->createdat)->format('Y-m');
        })->take(6);

        // Get detailed leads list
        $detailedLeads = $leads->map(function($lead) {
            return [
                'id' => $lead->id,
                'leads_name' => $lead->leads_name,
                'contact_person' => $lead->contact_person,
                'contact_number' => $lead->contact_number,
                'email' => $lead->email,
                'status_name' => $lead->status->status_name ?? 'Unknown',
                'status_id' => $lead->status_id,
                'created_date' => $lead->createdat ? Carbon::parse($lead->createdat)->format('Y-m-d') : 'N/A',
                'next_follow_up' => $lead->next_follow_up_date ? Carbon::parse($lead->next_follow_up_date)->format('Y-m-d') : 'N/A',
                'user_name' => $lead->user->name ?? 'N/A',
                'ticket_value' => $lead->ticket_value ?? 0,
                'is_close_win' => $lead->status_id == 1,
                'is_close_lost' => $lead->status_id == 2];
        });

        // Calculate conversion rate
        $conversionRate = $totalLeads > 0 ? round(($closeWinLeads / $totalLeads) * 100, 2) : 0;

        // Get total ticket value
        $totalTicketValue = $leads->sum('ticket_value');
        $closeWinTicketValue = $leads->where('status_id', 1)->sum('ticket_value');

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'company_name' => $customer->company_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address],
            'statistics' => [
                'total_leads' => $totalLeads,
                'close_win_leads' => $closeWinLeads,
                'close_lost_leads' => $closeLostLeads,
                'active_leads' => $activeLeads,
                'recent_leads' => $recentLeads,
                'conversion_rate' => $conversionRate,
                'total_ticket_value' => $totalTicketValue,
                'close_win_ticket_value' => $closeWinTicketValue],
            'leads_by_status' => $leadsByStatus,
            'leads_by_month' => $leadsByMonth,
            'detailed_leads' => $detailedLeads]);
    }

    public function getCustomerLeadDetails($customerId, Request $request)
    {
        $customer = Customer::findOrFail($customerId);

        $query = SalesRecord::where('prospectus_id', $customer->prospectus_id)
            ->with(['status', 'user', 'prospectus', 'state', 'city', 'businessType', 'leadSource', 'product']);

        // Apply filters
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('createdat', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('createdat', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('leads_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('contact_number', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $leads = $query->orderBy('createdat', 'desc')->paginate(10);

        // Transform the data to include all necessary fields
        $transformedLeads = $leads->getCollection()->map(function($lead) {
            return [
                'id' => $lead->id,
                'leads_name' => $lead->leads_name,
                'contact_person' => $lead->contact_person,
                'contact_number' => $lead->contact_number,
                'email' => $lead->email,
                'status_name' => $lead->status->status_name ?? 'Unknown',
                'status_id' => $lead->status_id,
                'created_date' => $lead->createdat ? Carbon::parse($lead->createdat)->format('Y-m-d') : 'N/A',
                'next_follow_up' => $lead->next_follow_up_date ? Carbon::parse($lead->next_follow_up_date)->format('Y-m-d') : 'N/A',
                'user_name' => $lead->user->name ?? 'N/A',
                'ticket_value' => $lead->ticket_value ?? 0,
                'is_close_win' => $lead->status_id == 1,
                'is_close_lost' => $lead->status_id == 2];
        });

        // Create a new paginator with transformed data
        $leads->setCollection($transformedLeads);

        return response()->json($leads);
    }
}
