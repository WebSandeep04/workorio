<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calling;
use App\Models\CallingCampaign;
use App\Models\CallingType;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

class CallingController extends Controller
{
    /**
     * Show the master list of all contacts.
     */
    public function index()
    {
        $user = $this->getCurrentUser();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('sales.calling')) {
            abort(403, 'Unauthorized');
        }
        return view('calling.index');
    }

    /**
     * Show the Lock Calling page for campaign-specific work.
     */
    public function lockIndex()
    {
        $user = $this->getCurrentUser();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('sales.calling.lock')) {
            abort(403, 'Unauthorized');
        }
        return view('calling.lock');
    }

    /**
     * Fetch all records via AJAX.
     */
    public function getCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = Calling::query();
        if ($junkTypeId) {
            $query->whereDoesntHave('campaigns', function($q) use ($junkTypeId) {
                $q->where('calling_campaign_calling.calling_type_id', $junkTypeId);
            });
        }
        
        $callings = $query->orderBy('id', 'desc')->paginate($perPage);
            
        return response()->json($callings);
    }

    /**
     * Get list of all campaigns.
     */
    public function getCampaigns()
    {
        return response()->json(CallingCampaign::orderBy('id', 'desc')->get());
    }

    /**
     * Filter records via AJAX.
     */
    public function filterCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        
        $query = Calling::query();

        if ($junkTypeId) {
            $query->whereDoesntHave('campaigns', function($q) use ($junkTypeId) {
                $q->where('calling_campaign_calling.calling_type_id', $junkTypeId);
            });
        }

        // Filter by Campaign if provided
        if ($request->filled('campaign_id')) {
            $query->whereHas('campaigns', function($q) use ($request) {
                $q->where('calling_campaigns.id', $request->campaign_id);
                // If on the Lock Calling page, filter out leads already locked
                if ($request->header('referer') && str_contains($request->header('referer'), '/calling/lock')) {
                    // Show only AVAILABLE leads (is_locked = 0)
                    $q->where('calling_campaign_calling.is_locked', 0);
                }
            });
        }
            
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
            $query->where('state', 'like', '%' . $request->state_id . '%');
        }

        if ($request->filled('city_id')) {
            $query->where('city', 'like', '%' . $request->city_id . '%');
        }

        if ($request->filled('list_id')) {
            $query->where('list_id', $request->list_id);
        }

        $paginated = $query->orderBy('id', 'desc')->paginate($perPage);

        // Inject selection status
        $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
        $paginated->getCollection()->transform(function($item) use ($selection) {
            $item->is_selected = $selection['all_matching'] || in_array($item->id, $selection['ids']);
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Provide filter options for the search box.
     */
    public function getFilterOptions()
    {
        return response()->json([
            'states' => Calling::distinct()
                ->whereNotNull('state')
                ->orderBy('state')
                ->get(['state as id', 'state as name']),
            'lists' => \App\Models\CallingList::orderBy('name')->get(['id', 'name']),
            'calling_types' => CallingType::where('name', '!=', 'Junk')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the page with all callings (admin/manager view).
     */
    public function allCallings()
    {
        $user = $this->getCurrentUser();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('sales.calling')) {
            abort(403, 'Unauthorized');
        }
        return view('calling.all');
    }

    /**
     * Get all callings data for the 'All' page.
     */
    public function getAllCallingsData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = DB::table('callings')
            ->leftJoin('calling_campaign_calling', function($join) {
                $join->on('callings.id', '=', 'calling_campaign_calling.calling_id')
                    ->whereRaw('calling_campaign_calling.id = (SELECT MAX(id) FROM calling_campaign_calling WHERE calling_id = callings.id)');
            })
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('calling_remarks', function($join) {
                $join->on('callings.id', '=', 'calling_remarks.calling_id')
                    ->whereRaw('calling_remarks.id = (SELECT MAX(id) FROM calling_remarks WHERE calling_id = callings.id)');
            })
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            })
            ->select(
                'callings.*',
                'calling_types.name as calling_type_name',
                'calling_campaign_calling.calling_type_id',
                'calling_campaign_calling.next_followup_date as next_follow_up_date',
                'calling_remarks.remark as latest_remark_text'
            );

        $paginated = $query->orderBy('callings.id', 'desc')->paginate($perPage);

        // Inject selection status and format for view
        $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
        
        $paginated->getCollection()->transform(function($item) use ($selection) {
            $item->is_selected = $selection['all_matching'] || in_array($item->id, $selection['ids']);
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->calling_type_name ? (object)['name' => $item->calling_type_name] : null;
            $item->status = $item->calling_type_name ? (object)['status_name' => $item->calling_type_name] : null;
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Filter all callings for the 'All' page.
     */
    public function filterAllCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = DB::table('callings')
            ->leftJoin('calling_campaign_calling', function($join) {
                $join->on('callings.id', '=', 'calling_campaign_calling.calling_id')
                    ->whereRaw('calling_campaign_calling.id = (SELECT MAX(id) FROM calling_campaign_calling WHERE calling_id = callings.id)');
            })
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('calling_remarks', function($join) {
                $join->on('callings.id', '=', 'calling_remarks.calling_id')
                    ->whereRaw('calling_remarks.id = (SELECT MAX(id) FROM calling_remarks WHERE calling_id = callings.id)');
            })
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            });

        if ($request->filled('name')) {
            $term = trim((string) $request->name);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.company_name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        if ($request->filled('state_id')) {
            $query->where('callings.state', $request->state_id);
        }

        if ($request->filled('city_id')) {
            $query->where('callings.city', $request->city_id);
        }

        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        $query->select(
            'callings.*',
            'calling_types.name as calling_type_name',
            'calling_campaign_calling.calling_type_id',
            'calling_campaign_calling.next_followup_date as next_follow_up_date',
            'calling_remarks.remark as latest_remark_text'
        );

        $paginated = $query->orderBy('callings.id', 'desc')->paginate($perPage);

        // Inject selection status and format for view
        $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
        
        $paginated->getCollection()->transform(function($item) use ($selection) {
            $item->is_selected = $selection['all_matching'] || in_array($item->id, $selection['ids']);
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->calling_type_name ? (object)['name' => $item->calling_type_name] : null;
            $item->status = $item->calling_type_name ? (object)['status_name' => $item->calling_type_name] : null;
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Create a new campaign and assign selected callings to it.
     */
    public function createCampaign(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'use_session_selection' => 'nullable',
            'calling_ids' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();
            $idList = [];
            if ($request->use_session_selection) {
                $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
                $idList = $selection['all_matching'] ? $this->getMatchingIds($selection['filters']) : $selection['ids'];
            } else {
                $idList = $request->calling_ids;
            }

            if (empty($idList)) return response()->json(['success' => false, 'message' => 'No contacts selected.']);

            $campaign = CallingCampaign::create(['name' => $request->campaign_name]);
            $pivotData = array_map(fn($id) => [
                'calling_id' => $id, 'calling_campaign_id' => $campaign->id, 'user_id' => null, 'is_locked' => 0, 'calling_type_id' => null, 'created_at' => now(), 'updated_at' => now()
            ], $idList);

            foreach (array_chunk($pivotData, 1000) as $chunk) DB::table('calling_campaign_calling')->insert($chunk);

            Session::forget('calling_selection');
            DB::commit();
            return response()->json(['success' => true, 'message' => "Campaign '{$campaign->name}' created with " . count($idList) . " contacts!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleSelection(Request $request)
    {
        $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
        if ($selection['all_matching']) $selection['all_matching'] = false; 

        if ($request->checked) {
            if (!in_array($request->id, $selection['ids'])) $selection['ids'][] = $request->id;
        } else {
            $selection['ids'] = array_values(array_filter($selection['ids'], fn($i) => $i != $request->id));
        }
        Session::put('calling_selection', $selection);
        return response()->json(['success' => true, 'count' => $this->getSelectionCount($selection)]);
    }

    public function selectAllMatching(Request $request)
    {
        Session::put('calling_selection', ['ids' => [], 'all_matching' => true, 'filters' => $request->filters ?? []]);
        return response()->json(['success' => true, 'count' => $this->getMatchingCount($request->filters ?? [])]);
    }

    public function clearSelection()
    {
        Session::forget('calling_selection');
        return response()->json(['success' => true, 'count' => 0]);
    }

    public function getSelectionStatus()
    {
        $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false]);
        return response()->json(['count' => $this->getSelectionCount($selection), 'all_matching' => $selection['all_matching']]);
    }

    private function getSelectionCount($selection)
    {
        return $selection['all_matching'] ? $this->getMatchingCount($selection['filters']) : count($selection['ids']);
    }

    private function getMatchingCount($filters)
    {
        $query = Calling::query();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        if ($junkTypeId) $query->whereDoesntHave('campaigns', fn($q) => $q->where('calling_campaign_calling.calling_type_id', $junkTypeId));
        
        if (!empty($filters['campaign_id'])) {
            $query->whereHas('campaigns', function($q) use ($filters) {
                $q->where('calling_campaigns.id', $filters['campaign_id']);
                if (!empty($filters['is_locking'])) {
                    $q->where('calling_campaign_calling.is_locked', 0);
                }
            });
        }
        if (!empty($filters['name'])) {
            $term = trim((string) $filters['name']); $like = '%' . $term . '%';
            $query->where(fn($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like));
        }
        if (!empty($filters['state_id'])) $query->where('state', 'like', '%' . $filters['state_id'] . '%');
        if (!empty($filters['city_id'])) $query->where('city', 'like', '%' . $filters['city_id'] . '%');
        if (!empty($filters['list_id'])) $query->where('list_id', $filters['list_id']);

        return $query->count();
    }

    private function getMatchingIds($filters)
    {
        $query = Calling::query();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        if ($junkTypeId) $query->whereDoesntHave('campaigns', fn($q) => $q->where('calling_campaign_calling.calling_type_id', $junkTypeId));
        
        if (!empty($filters['campaign_id'])) {
            $query->whereHas('campaigns', function($q) use ($filters) {
                $q->where('calling_campaigns.id', $filters['campaign_id']);
                if (!empty($filters['is_locking'])) {
                    $q->where('calling_campaign_calling.is_locked', 0);
                }
            });
        }
        if (!empty($filters['name'])) {
            $term = trim((string) $filters['name']); $like = '%' . $term . '%';
            $query->where(fn($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like));
        }
        if (!empty($filters['state_id'])) $query->where('state', 'like', '%' . $filters['state_id'] . '%');
        if (!empty($filters['city_id'])) $query->where('city', 'like', '%' . $filters['city_id'] . '%');
        if (!empty($filters['list_id'])) $query->where('list_id', $filters['list_id']);

        return $query->pluck('id')->toArray();
    }

    /**
     * Lock selected leads for the current user.
     */
    public function lockLeads(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:calling_campaigns,id',
            'use_session_selection' => 'nullable',
            'calling_ids' => 'nullable|array',
            'calling_ids.*' => 'integer|exists:callings,id'
        ]);

        $user = $this->getCurrentUser();
        if ($user && $user->role && $user->role->role_name !== 'admin' && !$user->hasPermission('sales.calling.lock')) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        try {
            $userId = $this->getCurrentUserId();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to identify user. Please re-login.'
                ], 401);
            }

            $idList = [];
            if ($request->use_session_selection) {
                $selection = Session::get('calling_selection', ['ids' => [], 'all_matching' => false, 'filters' => []]);
                // If locking, we must ensure we only lock leads that belong to this campaign and are unassigned.
                // The filters in the session might already include the campaign_id.
                $idList = $selection['all_matching'] ? $this->getMatchingIds($selection['filters']) : $selection['ids'];
            } else {
                $idList = $request->calling_ids;
            }

            if (empty($idList)) {
                return response()->json(['success' => false, 'message' => 'No contacts selected.']);
            }

            DB::table('calling_campaign_calling')
                ->where('calling_campaign_id', $request->campaign_id)
                ->whereIn('calling_id', $idList)
                ->update([
                    'user_id' => $userId,
                    'is_locked' => 1,
                    'updated_at' => now()
                ]);

            Session::forget('calling_selection');

            return response()->json([
                'success' => true,
                'message' => count($idList) . ' leads have been locked and assigned to you.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user ID from Auth or Session.
     */
    private function getCurrentUserId(): ?int
    {
        return auth()->id() ?? (session()->has('user_id') ? (int) session('user_id') : null);
    }

    /**
     * Get current user model.
     */
    private function getCurrentUser()
    {
        $userId = $this->getCurrentUserId();
        return $userId ? \App\Models\User::find($userId) : null;
    }

    /**
     * Show the remarks and interaction history for a lead.
     */
    public function remarks($id)
    {
        $calling = Calling::findOrFail($id);
        $campaignId = request('campaign_id');
        $currentCampaign = null;
        $pivotData = null;

        if ($campaignId) {
            $currentCampaign = CallingCampaign::find($campaignId);
            $pivotData = DB::table('calling_campaign_calling')
                ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
                ->where('calling_id', $calling->id)
                ->where('calling_campaign_id', $campaignId)
                ->select('calling_campaign_calling.*', 'calling_types.name as status_name')
                ->first();
        }

        $callingTypes = CallingType::orderBy('name')->get();
        $whatsappTemplates = WhatsappTemplate::orderBy('name')->get();
        $currentUserId = $this->getCurrentUserId();

        return view('calling.remarks', compact('calling', 'currentCampaign', 'pivotData', 'callingTypes', 'whatsappTemplates', 'currentUserId'));
    }

    /**
     * Store a new remark and update campaign status.
     */
    public function storeRemark(Request $request, $id)
    {
        $calling = Calling::findOrFail($id);
        
        $request->validate([
            'remark' => 'required|string',
            'calling_type_id' => 'nullable|exists:calling_types,id',
            'next_followup_date' => 'nullable|date',
            'campaign_id' => 'nullable|integer|exists:calling_campaigns,id'
        ]);

        $userId = $this->getCurrentUserId();

        try {
            DB::beginTransaction();

            // 1. Create the interaction log (global)
            $newRemarkId = DB::table('calling_remarks')->insertGetId([
                'calling_id' => $calling->id,
                'calling_campaign_id' => $request->campaign_id,
                'user_id' => $userId,
                'remark' => $request->remark,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Update campaign specific context in pivot table
            if ($request->filled('campaign_id')) {
                DB::table('calling_campaign_calling')
                    ->where('calling_id', $calling->id)
                    ->where('calling_campaign_id', $request->campaign_id)
                    ->update([
                        'calling_type_id' => $request->calling_type_id,
                        'next_followup_date' => $request->next_followup_date,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Remark saved successfully.',
                    'remark' => [
                        'id' => $newRemarkId,
                        'text' => $request->remark,
                        'user_id' => $userId,
                        'created_at' => now()->format('d M Y, h:i A')
                    ],
                    'pivot' => [
                        'status' => DB::table('calling_types')->where('id', $request->calling_type_id)->value('name') ?? 'Not Set',
                        'next_date' => $request->next_followup_date
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Remark saved and campaign status updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save remark: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing remark via AJAX.
     */
    public function updateRemark(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string',
            'calling_type_id' => 'nullable|exists:calling_types,id',
            'next_followup_date' => 'nullable|date',
            'campaign_id' => 'nullable|integer|exists:calling_campaigns,id'
        ]);

        try {
            DB::beginTransaction();

            $remarkRow = DB::table('calling_remarks')->where('id', $id)->first();
            if (!$remarkRow) throw new \Exception("Remark not found.");

            // 1. Update the interaction log
            DB::table('calling_remarks')
                ->where('id', $id)
                ->update([
                    'remark' => $request->remark,
                    'updated_at' => now()
                ]);

            // 2. Update campaign specific context in pivot table
            if ($request->filled('campaign_id')) {
                DB::table('calling_campaign_calling')
                    ->where('calling_id', $remarkRow->calling_id)
                    ->where('calling_campaign_id', $request->campaign_id)
                    ->update([
                        'calling_type_id' => $request->calling_type_id,
                        'next_followup_date' => $request->next_followup_date,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Remark updated successfully.',
                'pivot' => [
                    'status' => DB::table('calling_types')->where('id', $request->calling_type_id)->value('name') ?? 'Not Set',
                    'next_date' => $request->next_followup_date
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX helper for Cities.
     */
    public function getCitiesByState($stateName)
    {
        return response()->json(
            Calling::distinct()
                ->where('state', $stateName)
                ->whereNotNull('city')
                ->orderBy('city')
                ->get(['city as id', 'city as name'])
        );
    }
}
