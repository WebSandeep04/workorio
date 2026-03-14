<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Calling;
use App\Models\City;
use App\Models\State;
use App\Models\CallingType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class AssignedCallingController extends Controller
{
    public function index()
    {
        return view('calling.assigned');
    }

    // Get assigned callings with pagination
    public function getAssignedCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);

        $records = Calling::with([
            'state:id,state_name',
            'city:id,city_name',
            'latestRemark',
            'callingType:id,name',
            'status:id,status_name'
        ])
        ->whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
        })
        ->where('user_id', '!=', $userId)
        ->where('calling_type_id', '!=', $junkTypeId)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

        return response()->json($records);
    }

    // Get filtered assigned callings
    public function filterAssignedCallings(Request $request)
    {
        $userId = $this->getCurrentUserId();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        $perPage = $request->get('per_page', 10);

        $query = Calling::with([
            'state:id,state_name',
            'city:id,city_name',
            'latestRemark',
            'callingType:id,name',
            'status:id,status_name'
        ])
        ->whereHas('assignmentLogs', function($aq) use ($userId) {
            $aq->where('assigned_by', $userId);
        })
        ->where('user_id', '!=', $userId)
        ->where('calling_type_id', '!=', $junkTypeId);

        // Apply filters
        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('phone', 'like', $like)
                  ->orWhere('address', 'like', $like);
            });
        }
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_type_id', $request->calling_type_id);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($records);
    }

    public function getFilterOptions()
    {
        return response()->json([
            'states' => State::orderBy('state_name')->get([
                'id',
                \DB::raw('state_name as name')
            ]),
            'calling_types' => CallingType::where('name', '!=', 'Junk')
                ->orderBy('name')->get([
                    'id',
                    'name'
                ]),
        ]);
    }

    public function updateCallingType(Request $request)
    {
        $request->validate([
            'calling_id' => 'required|exists:callings,id',
            'calling_type_id' => 'required|exists:calling_types,id',
        ]);

        $calling = \App\Models\Calling::findOrFail($request->calling_id);
        $calling->calling_type_id = $request->calling_type_id;
        $calling->save();

        return response()->json([
            'success' => true,
            'message' => 'Calling type updated successfully',
        ]);
    }

    public function getCitiesByState($stateId)
    {
        return response()->json(
            City::where('state_id', $stateId)
                ->orderBy('city_name')
                ->get([
                    'id',
                    \DB::raw('city_name as name')
                ])
        );
    }

    /**
     * Get remarks for a calling record (for the modal)
     */
    public function getRemarks(Request $request)
    {
        $callingId = $request->input('sales_record_id'); // reuse same param name as the partial expects

        if (!$callingId) {
            return response()->json(['error' => 'Calling ID is required'], 400);
        }

        $calling = \App\Models\Calling::with(['state', 'city', 'callingType', 'status'])
            ->find($callingId);

        if (!$calling) {
            return response()->json(['error' => 'Calling record not found'], 404);
        }

        $remarks = \App\Models\CallingRemark::where('calling_id', $callingId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($remark) {
                return [
                    'id'         => $remark->id,
                    'date'       => $remark->created_at ? $remark->created_at->format('d/m/Y') : 'N/A',
                    'remark'     => $remark->remark ?? '',
                    'created_at' => $remark->created_at ? $remark->created_at->format('d/m/Y H:i') : 'N/A',
                ];
            });

        return response()->json([
            'sales_record' => [
                'id'                 => $calling->id,
                'leads_name'         => $calling->name ?? '-',
                'contact_person'     => $calling->name ?? '-',
                'contact_number'     => $calling->phone ?? '-',
                'email'              => $calling->email ?? '-',
                'state_name'         => optional($calling->state)->state_name ?? '-',
                'city_name'          => optional($calling->city)->city_name ?? '-',
                'product_name'       => optional($calling->callingType)->name ?? '-',
                'business_name'      => '-',
                'status_name'        => optional($calling->status)->status_name ?? '-',
                'ticket_value'       => '-',
                'next_follow_up_date' => $calling->next_follow_up_date
                    ? \Carbon\Carbon::parse($calling->next_follow_up_date)->format('d/m/Y')
                    : 'N/A',
            ],
            'remarks' => $remarks,
        ]);
    }

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        if (Auth::check()) {
            return Auth::id();
        }
        if (session()->has('user_id')) {
            return session('user_id');
        }
        return null;
    }
}
