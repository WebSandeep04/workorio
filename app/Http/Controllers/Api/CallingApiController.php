<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Calling;
use App\Models\State;
use App\Models\City;
use App\Models\CallingType;
use App\Models\CallingList;
use App\Models\CallingCampaign;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\WhatsappTemplate;
use App\Models\Prospectus;
use App\Models\SalesRecord;
use App\Models\LeadAssignmentLog;
use App\Models\Remark;
use App\Models\SalesLeadSource;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewLeadNotification;

class CallingApiController extends Controller
{
    /**
     * Securely create segmented lists from Multipart CSV/TXT streams uploaded from mobile client.
     */
    public function storeCallingList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            
            $list = CallingList::create([
                'name' => $request->name,
                'total_records' => 0
            ]);

            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();
            
            $records = [];
            $header = null;
            $total = 0;

            // Restrict to CSV/TXT parsing similar to master Web controller
            $extension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($extension), ['csv', 'txt'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Parsing failure: Only CSV and TXT files are natively supported for parsing.'
                ], 400);
            }

            if (($handle = fopen($filePath, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (empty(array_filter($data))) continue; // Skip empty rows

                    if (!$header) {
                        $header = array_map('trim', $data);
                        continue;
                    }
                    
                    if (count($header) !== count($data)) {
                        if (count($data) < count($header)) {
                            $data = array_pad($data, count($header), null);
                        } else {
                            $data = array_slice($data, 0, count($header));
                        }
                    }

                    $row = array_combine($header, $data);
                    
                    $records[] = [
                        'list_id' => $list->id,
                        'name'    => $row['Name'] ?? ($row['name'] ?? null),
                        'company_name'   => $row['Company Name'] ?? ($row['company_name'] ?? ($row['Company'] ?? ($row['company'] ?? null))),
                        'contact_person' => $row['Contact Person'] ?? ($row['contact_person'] ?? ($row['Contact person'] ?? null)),
                        'email'   => $row['Email'] ?? ($row['email'] ?? null),
                        'phone'   => $row['Phone'] ?? ($row['phone'] ?? ($row['Contact'] ?? ($row['Mobile'] ?? null))),
                        'address' => $row['Address'] ?? ($row['address'] ?? null),
                        'city'    => $row['City'] ?? ($row['city'] ?? null),
                        'state'   => $row['State'] ?? ($row['state'] ?? null),
                        'legal_status' => $row['Legal Status'] ?? ($row['legal_status'] ?? null),
                        'gst_number'   => $row['GST Number'] ?? ($row['gst_number'] ?? ($row['GST'] ?? null)),
                        'turnover'     => $row['Turnover'] ?? ($row['turnover'] ?? ($row['Turn Over'] ?? null)),
                    ];
                    $total++;
                    
                    if (count($records) >= 500) {
                        Calling::insert($records);
                        $records = [];
                    }
                }
                fclose($handle);
            }

            if (!empty($records)) {
                Calling::insert($records);
            }

            $list->update(['total_records' => $total]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Lead segment '$request->name' imported successfully with $total records."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Import breakdown: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve dynamic segments lists with pagination and record aggregates.
     */
    public function getCallingLists(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $lists = CallingList::orderBy('id', 'desc')->paginate($perPage);
        $totalLeads = CallingList::sum('total_records');

        return response()->json([
            'lists' => $lists,
            'total_leads' => $totalLeads
        ]);
    }

    /**
     * Perform cascaded deletion of segmented list and its associated leads.
     */
    public function deleteCallingList($id)
    {
        try {
            DB::beginTransaction();
            $list = CallingList::findOrFail($id);
            // Cascading deletion of associated child records
            Calling::where('list_id', $id)->delete();
            $list->delete();
            
            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Segment and associated lead records deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to remove segment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch and filter All Callings data.
     */
    public function getAllCallings(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        // Building basic query equivalent to getAllCallingsData on web
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

        // Apply Search Filter
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.company_name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        // Apply Categorical Filters
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }

        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }

        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        // Selection Columns
        $query->select(
            'callings.*',
            'calling_types.name as calling_type_name',
            'calling_campaign_calling.calling_type_id',
            'calling_campaign_calling.next_followup_date as next_follow_up_date',
            'calling_remarks.remark as latest_remark_text'
        );

        $paginated = $query->orderBy('callings.id', 'desc')->paginate($perPage);

        // Map items structure for direct unified consumption in mobile components
        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->calling_type_name ? (object)['name' => $item->calling_type_name] : null;
            $item->status = $item->calling_type_name ? (object)['status_name' => $item->calling_type_name] : null;
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Load distinct states, cities, and calling types for UI filtering.
     */
    public function getFilterOptions()
    {
        $states = Calling::distinct()
            ->whereNotNull('state')
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        $callingTypes = CallingType::where('name', '!=', 'Junk')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'states' => $states,
            'cities' => $cities,
            'calling_types' => $callingTypes
        ]);
    }

    /**
     * Retrieve lead details and historical interaction logs.
     */
    public function getRemarks(Request $request, $id)
    {
        $campaignId = $request->get('campaign_id');
        
        $query = DB::table('callings')
            ->leftJoin('calling_campaign_calling', function($join) use ($campaignId) {
                $join->on('callings.id', '=', 'calling_campaign_calling.calling_id');
                if ($campaignId) {
                    $join->where('calling_campaign_calling.calling_campaign_id', '=', $campaignId);
                }
            })
            ->leftJoin('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('callings.id', $id);

        $lead = $query->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
                'calling_campaign_calling.calling_campaign_id',
                'calling_campaign_calling.calling_type_id',
                'calling_campaign_calling.next_followup_date',
                'calling_types.name as status_name'
            )
            ->first();

        if (!$lead) {
            return response()->json(['error' => 'Calling record not found'], 404);
        }

        $remarks = DB::table('calling_remarks')
            ->leftJoin('users', 'calling_remarks.user_id', '=', 'users.id')
            ->where('calling_id', $id)
            ->orderBy('calling_remarks.created_at', 'desc')
            ->select('calling_remarks.*', 'users.name as user_name')
            ->get()
            ->map(function($r) {
                return [
                    'id' => $r->id,
                    'date' => Carbon::parse($r->created_at)->format('d M Y, h:i A'),
                    'remark' => $r->remark,
                    'user' => $r->user_name ?? 'System'
                ];
            });

        return response()->json([
            'lead' => $lead,
            'remarks' => $remarks
        ]);
    }

    /**
     * Fetch Filter Options tailored for Campaigns creation (Includes active Campaigns, Lists, and unique States)
     */
    public function getCampaignFilterOptions()
    {
        $campaigns = CallingCampaign::orderBy('id', 'desc')->get(['id', 'name']);
        $lists = CallingList::orderBy('name', 'asc')->get(['id', 'name']);

        $states = Calling::distinct()
            ->whereNotNull('state')
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        return response()->json([
            'campaigns' => $campaigns,
            'lists' => $lists,
            'states' => $states,
            'cities' => $cities
        ]);
    }

    /**
     * Fetch and Filter Master Lead Pool for the Selection Engine.
     */
    public function getCallingMaster(Request $request)
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
                if ($request->boolean('is_locking')) {
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
            
        return response()->json($paginated);
    }

    /**
     * Secure creation of a Calling Campaign with direct parameter selections.
     */
    public function createCampaignMobile(Request $request)
    {
        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'all_matching' => 'nullable|boolean',
            'filters' => 'nullable|array',
            'calling_ids' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();
            
            $idList = [];
            
            if ($request->all_matching) {
                $idList = $this->getMatchingIdsForMobile($request->filters ?? []);
            } else {
                $idList = $request->calling_ids ?? [];
            }

            if (empty($idList)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Constraint violation: You must select at least 1 contact to create a campaign.'
                ], 400);
            }

            // 1. Persist campaign record
            $campaign = CallingCampaign::create([
                'name' => $request->campaign_name
            ]);

            // 2. Map and transactional bulk insert pivot associations
            $pivotData = array_map(fn($id) => [
                'calling_id' => $id, 
                'calling_campaign_id' => $campaign->id, 
                'user_id' => null, 
                'is_locked' => 0, 
                'calling_type_id' => null, 
                'created_at' => now(), 
                'updated_at' => now()
            ], $idList);

            foreach (array_chunk($pivotData, 500) as $chunk) {
                DB::table('calling_campaign_calling')->insert($chunk);
            }

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => "Campaign '{$campaign->name}' established successfully with " . count($idList) . " contacts."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Campaign establishment aborted: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Internal filter resolving matching Contact IDs globally.
     */
    private function getMatchingIdsForMobile($filters)
    {
        $query = Calling::query();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        if ($junkTypeId) {
            $query->whereDoesntHave('campaigns', fn($q) => $q->where('calling_campaign_calling.calling_type_id', $junkTypeId));
        }
        
        if (!empty($filters['campaign_id'])) {
            $query->whereHas('campaigns', function($q) use ($filters) {
                $q->where('calling_campaigns.id', $filters['campaign_id']);
                if (!empty($filters['is_locking'])) {
                    $q->where('calling_campaign_calling.is_locked', 0);
                }
            });
        }
        if (!empty($filters['name'])) {
            $term = trim((string) $filters['name']); 
            $like = '%' . $term . '%';
            $query->where(fn($q) => $q->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('phone', 'like', $like));
        }
        if (!empty($filters['state_id'])) $query->where('state', 'like', '%' . $filters['state_id'] . '%');
        if (!empty($filters['city_id'])) $query->where('city', 'like', '%' . $filters['city_id'] . '%');
        if (!empty($filters['list_id'])) $query->where('list_id', $filters['list_id']);

        return $query->pluck('id')->toArray();
    }

    /**
     * Lock selected campaign leads assigning ownership to current user.
     */
    public function lockLeadsMobile(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:calling_campaigns,id',
            'all_matching' => 'nullable|boolean',
            'filters' => 'nullable|array',
            'calling_ids' => 'nullable|array'
        ]);

        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Auth required.'], 401);
            }
            $userId = $user->id;
            
            $idList = [];
            if ($request->all_matching) {
                $filters = $request->filters ?? [];
                $filters['campaign_id'] = $request->campaign_id; 
                $filters['is_locking'] = true;
                $idList = $this->getMatchingIdsForMobile($filters);
            } else {
                $idList = $request->calling_ids ?? [];
            }

            if (empty($idList)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Operation invalid: You must select at least 1 contact to lock.'
                ], 400);
            }

            DB::table('calling_campaign_calling')
                ->where('calling_campaign_id', $request->campaign_id)
                ->whereIn('calling_id', $idList)
                ->update([
                    'user_id' => $userId,
                    'is_locked' => 1,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => count($idList) . ' campaign leads successfully assigned and locked to your session.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock leads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch specific leads locked and assigned to current authenticated session (My Calling).
     */
    public function getMyCalls(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);
        
        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $user->id)
            ->where('calling_campaign_calling.is_locked', 1)
            ->where(function($q) {
                $q->where('calling_campaign_calling.is_assigned', 0)
                  ->orWhereNull('calling_campaign_calling.is_assigned');
            })
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            });

        // Apply dynamic filters
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }
        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }
        if ($request->filled('calling_type_id')) {
            $query->where('calling_campaign_calling.calling_type_id', $request->calling_type_id);
        }

        $query->select(
            'callings.*',
            'calling_campaigns.name as campaign_name',
            'calling_campaign_calling.calling_campaign_id',
            'calling_types.name as pivot_status',
            'calling_campaign_calling.next_followup_date as pivot_followup',
            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark_text')
        );

        $paginated = $query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage);

        // Standardize collection mapping for seamless frontend interop
        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->pivot_status ? (object)['name' => $item->pivot_status] : null;
            $item->calling_type_name = $item->pivot_status; 
            $item->next_follow_up_date = $item->pivot_followup; 
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Load distinct filter items tailored for current agent's claims.
     */
    public function getMyCallsFilterOptions(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        // Fetch campaigns where user actually has claims
        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->where('calling_campaign_calling.user_id', $user->id)
            ->where('calling_campaign_calling.is_locked', 1)
            ->select('calling_campaigns.id', 'calling_campaigns.name')
            ->distinct()
            ->get();

        // Fetch agent scoped states & cities
        $leadIds = DB::table('calling_campaign_calling')
            ->where('user_id', $user->id)
            ->where('is_locked', 1)
            ->pluck('calling_id');

        $states = Calling::whereIn('id', $leadIds)
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::whereIn('id', $leadIds)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        $callingTypes = CallingType::where('name', '!=', 'Junk')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'campaigns' => $campaigns,
            'states' => $states,
            'cities' => $cities,
            'calling_types' => $callingTypes
        ]);
    }

    /**
     * Load Statuses, WhatsApp templates and user assignment context for interaction tracking.
     */
    public function getRemarksMeta(Request $request)
    {
        $callingTypes = CallingType::orderBy('name')->get(['id', 'name']);
        $whatsappTemplates = WhatsappTemplate::orderBy('name')->get(['id', 'name', 'text']);
        
        $salesUsers = User::where('is_sales', 1)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'calling_types' => $callingTypes,
            'whatsapp_templates' => $whatsappTemplates,
            'sales_users' => $salesUsers
        ]);
    }

    /**
     * Store standard remark interaction log and trigger lead conversions/handover workflow.
     */
    public function storeRemarkMobile(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $calling = Calling::findOrFail($id);

        $request->validate([
            'remark' => 'required|string',
            'calling_type_id' => 'nullable|exists:calling_types,id',
            'next_followup_date' => 'nullable|date',
            'campaign_id' => 'nullable|integer|exists:calling_campaigns,id',
            'assign_user_id' => 'nullable|integer|exists:users,id'
        ]);

        $userId = $user->id;

        try {
            DB::beginTransaction();

            $callingStatusName = '';
            if ($request->calling_type_id) {
                $callingStatusName = DB::table('calling_types')->where('id', $request->calling_type_id)->value('name');
            }

            // Auto conversion logic for Interested Statuses
            if (strtolower(trim((string)$callingStatusName)) === 'interested') {
                $alreadyAssigned = false;
                if ($request->filled('campaign_id')) {
                    $alreadyAssigned = DB::table('calling_campaign_calling')
                        ->where('calling_id', $calling->id)
                        ->where('calling_campaign_id', $request->campaign_id)
                        ->where('is_assigned', 1)
                        ->exists();
                }

                if (!$alreadyAssigned) {
                    if (!$request->assign_user_id) {
                        throw new \Exception("Please select an assignee to convert the lead.");
                    }

                    // 1. Create Prospectus
                    $prospectus = Prospectus::create([
                        'prospectus_name' => $calling->company_name ?: ($calling->name ?: 'New Prospect (Auto)'),
                        'contact_person'  => $calling->name,
                        'contact_number'  => $calling->phone,
                        'email'           => $calling->email,
                        'address'         => $calling->address,
                    ]);

                    // 1.1 Build / Retrieve Tele Calling Source
                    $leadSourceId = SalesLeadSource::where('source_name', 'Tele Calling')->value('id');
                    if (!$leadSourceId) {
                        $leadSourceId = SalesLeadSource::create(['source_name' => 'Tele Calling'])->id;
                    }

                    // 2. Create SalesRecord (Lead)
                    $salesRecord = SalesRecord::create([
                        'prospectus_id'       => $prospectus->id,
                        'user_id'             => $request->assign_user_id,
                        'status_id'           => '19',
                        'leads_name'          => $calling->company_name ?: ($calling->name ?: 'New Prospect (Auto)'),
                        'contact_person'      => $calling->name,
                        'contact_number'      => $calling->phone,
                        'email'               => $calling->email,
                        'address'             => $calling->address,
                        'lead_source_id'      => $leadSourceId,
                        'next_follow_up_date' => $request->next_followup_date ?: now()->addDays(1)->toDateString(),
                        'createdat'           => now(),
                    ]);

                    // 3. Create LeadAssignmentLog
                    LeadAssignmentLog::create([
                        'sales_record_id' => $salesRecord->id,
                        'from_user_id'    => null,
                        'to_user_id'      => $request->assign_user_id,
                        'assigned_by'     => $userId,
                        'remark'          => 'Auto-assigned from Calling system (Interested)'
                    ]);

                    Remark::create([
                        'remark_date'     => now()->toDateString(),
                        'remark'          => $request->remark,
                        'sales_remark_id' => $salesRecord->id
                    ]);

                    // Send Email Notification
                    $this->sendNewLeadEmail($salesRecord, $request->remark, $user);
                }
            }

            // 4. Store standard activity log
            $newRemarkId = DB::table('calling_remarks')->insertGetId([
                'calling_id' => $calling->id,
                'calling_campaign_id' => $request->campaign_id,
                'user_id' => $userId,
                'remark' => $request->remark,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 5. Propagate values to pivot
            if ($request->filled('campaign_id')) {
                $updateData = [
                    'calling_type_id' => $request->calling_type_id,
                    'next_followup_date' => $request->next_followup_date,
                    'updated_at' => now()
                ];

                if (strtolower(trim((string)$callingStatusName)) === 'interested' && $request->assign_user_id) {
                    $updateData['is_assigned'] = 1;

                    DB::table('calling_assign_logs')->insert([
                        'calling_id' => $calling->id,
                        'calling_campaign_id' => $request->campaign_id,
                        'sales_record_id' => isset($salesRecord) ? $salesRecord->id : null,
                        'prospectus_id' => isset($prospectus) ? $prospectus->id : null,
                        'assigned_by' => $userId,
                        'assigned_to' => $request->assign_user_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('calling_campaign_calling')
                    ->where('calling_id', $calling->id)
                    ->where('calling_campaign_id', $request->campaign_id)
                    ->update($updateData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Remark successfully recorded.',
                'remark' => [
                    'id' => $newRemarkId,
                    'remark' => $request->remark,
                    'user' => $user->name,
                    'date' => now()->format('d M Y, h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to register logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dispatch email notification for converted leads.
     */
    private function sendNewLeadEmail($salesRecord, $remarkText, $creator = null)
    {
        $assignedTo = User::find($salesRecord->user_id);
        if (!$creator) $creator = $assignedTo;

        $recipientEmails = User::whereHas('employee', function ($q) {
                $q->where('status', 'active');
            })
            ->where('is_sales', 1)
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();
            
        if ($creator && $creator->email && !in_array($creator->email, $recipientEmails)) {
            $recipientEmails[] = $creator->email;
        }
        if ($assignedTo && $assignedTo->email && !in_array($assignedTo->email, $recipientEmails)) {
            $recipientEmails[] = $assignedTo->email;
        }

        $recipientEmails = array_filter($recipientEmails, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        if (!empty($recipientEmails)) {
            try {
                Mail::to($recipientEmails)->send(new NewLeadNotification($salesRecord, $creator, $assignedTo, $remarkText));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send lead conversion alert: " . $e->getMessage());
            }
        }
    }

    /**
     * Fetch agent's scheduled leads that are due today or outstanding (<= today).
     */
    public function getTodaysCalls(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);
        
        $perPage = $request->get('per_page', 10);
        $today = now()->toDateString();
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('calling_campaign_calling.user_id', $user->id)
            ->where('calling_campaign_calling.is_locked', 1)
            ->whereNotNull('calling_campaign_calling.next_followup_date')
            ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today)
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            });

        // Standard search filters
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }
        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }

        $query->select(
            'callings.*',
            'calling_campaigns.name as campaign_name',
            'calling_campaign_calling.calling_campaign_id',
            'calling_types.name as pivot_status',
            'calling_campaign_calling.next_followup_date as pivot_followup',
            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark_text')
        );

        $paginated = $query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage);

        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->pivot_status ? (object)['name' => $item->pivot_status] : null;
            $item->calling_type_name = $item->pivot_status; 
            $item->next_follow_up_date = $item->pivot_followup; 
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Load filters specifically within the agent's outstanding schedules.
     */
    public function getTodaysCallsFilterOptions(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $today = now()->toDateString();

        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->where('calling_campaign_calling.user_id', $user->id)
            ->where('calling_campaign_calling.is_locked', 1)
            ->whereNotNull('calling_campaign_calling.next_followup_date')
            ->whereDate('calling_campaign_calling.next_followup_date', '<=', $today)
            ->select('calling_campaigns.id', 'calling_campaigns.name')
            ->distinct()
            ->get();

        $leadIds = DB::table('calling_campaign_calling')
            ->where('user_id', $user->id)
            ->where('is_locked', 1)
            ->whereNotNull('next_followup_date')
            ->whereDate('next_followup_date', '<=', $today)
            ->pluck('calling_id');

        $states = Calling::whereIn('id', $leadIds)
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::whereIn('id', $leadIds)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        return response()->json([
            'campaigns' => $campaigns,
            'states' => $states,
            'cities' => $cities
        ]);
    }

    /**
     * Retrieve segregated leads explicitly designated as Junk inside campaigns.
     */
    public function getJunkCalls(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        if (!$junkTypeId) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->where('calling_campaign_calling.calling_type_id', $junkTypeId);

        // Multi-search query support
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%'.$term.'%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like);
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }
        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }

        $query->select(
            'callings.*',
            'calling_campaigns.name as campaign_name',
            'calling_campaign_calling.calling_campaign_id',
            'users.name as agent_name',
            'calling_types.name as pivot_status',
            'calling_campaign_calling.id as pivot_id',
            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark_text')
        );

        $paginated = $query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage);

        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->pivot_status ? (object)['name' => $item->pivot_status] : null;
            $item->calling_type_name = $item->pivot_status; 
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Load distinct state/campaign values specifically within the Junk collection.
     */
    public function getJunkCallsFilterOptions(Request $request)
    {
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');
        if (!$junkTypeId) {
            return response()->json(['campaigns' => [], 'states' => [], 'cities' => []]);
        }

        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->where('calling_campaign_calling.calling_type_id', $junkTypeId)
            ->select('calling_campaigns.id', 'calling_campaigns.name')
            ->distinct()
            ->get();

        $leadIds = DB::table('calling_campaign_calling')
            ->where('calling_type_id', $junkTypeId)
            ->pluck('calling_id');

        $states = Calling::whereIn('id', $leadIds)
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::whereIn('id', $leadIds)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        return response()->json([
            'campaigns' => $campaigns,
            'states' => $states,
            'cities' => $cities
        ]);
    }

    /**
     * Restore designated Junk pivot lead back to standard call cycle.
     */
    public function restoreJunkLeadMobile($pivotId)
    {
        try {
            $pivot = DB::table('calling_campaign_calling')->where('id', $pivotId)->first();
            if (!$pivot) return response()->json(['success' => false, 'message' => 'Mapping index not found.'], 404);

            $defaultType = CallingType::where('name', '!=', 'Junk')->orderBy('id')->value('id');
            
            DB::table('calling_campaign_calling')
                ->where('id', $pivotId)
                ->update([
                    'calling_type_id' => $defaultType,
                    'updated_at' => now()
                ]);

            return response()->json(['success' => true, 'message' => 'Lead recovered into standard pipeline.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Permanently remove designated Junk entry, its trace remarks and underlying lead if orphan.
     */
    public function deleteJunkLeadMobile($pivotId)
    {
        try {
            $pivot = DB::table('calling_campaign_calling')->where('id', $pivotId)->first();
            if (!$pivot) return response()->json(['success' => false, 'message' => 'Mapping index not found.'], 404);

            DB::transaction(function() use ($pivot, $pivotId) {
                // 1. Wipe global interaction traces
                DB::table('calling_remarks')->where('calling_id', $pivot->calling_id)->delete();
                
                // 2. Wipe pivot anchor
                DB::table('calling_campaign_calling')->where('id', $pivotId)->delete();
                
                // 3. Garbage collect underlying lead if orphaned across ALL campaigns
                $stillExists = DB::table('calling_campaign_calling')->where('calling_id', $pivot->calling_id)->exists();
                if (!$stillExists) {
                    DB::table('callings')->where('id', $pivot->calling_id)->delete();
                }
            });

            return response()->json(['success' => true, 'message' => 'Lead and its assets purged permanently.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get locked leads currently held by the manager's subordinates.
     */
    public function getTeamCalls(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        // Fetch subordinate array
        $subordinateIds = User::whereHas('managers', function($q) use ($user) {
            $q->where('manager_id', $user->id);
        })->pluck('id');

        $query = DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->whereIn('calling_campaign_calling.user_id', $subordinateIds)
            ->where(function($q) use ($junkTypeId) {
                if ($junkTypeId) {
                    $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                      ->orWhereNull('calling_campaign_calling.calling_type_id');
                }
            });

        // Multi-key search (supports name, phone, or specific agent name)
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like)
                  ->orWhere('users.name', 'like', $like);
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }
        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }
        if ($request->filled('agent_id')) {
            $query->where('calling_campaign_calling.user_id', $request->agent_id);
        }

        $query->select(
            'callings.*',
            'calling_campaigns.name as campaign_name',
            'calling_campaign_calling.calling_campaign_id',
            'calling_campaign_calling.user_id as agent_id',
            'users.name as agent_name',
            'calling_types.name as pivot_status',
            'calling_campaign_calling.id as pivot_id',
            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark_text')
        );

        $paginated = $query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage);

        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->pivot_status ? (object)['name' => $item->pivot_status] : null;
            $item->calling_type_name = $item->pivot_status; 
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Extract distinct filter keys within the scope of subordinates' datasets.
     */
    public function getTeamCallsFilterOptions(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $subordinateIds = User::whereHas('managers', function($q) use ($user) {
            $q->where('manager_id', $user->id);
        })->pluck('id');

        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->whereIn('calling_campaign_calling.user_id', $subordinateIds)
            ->select('calling_campaigns.id', 'calling_campaigns.name')
            ->distinct()
            ->get();

        $agents = User::whereIn('id', $subordinateIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $leadIds = DB::table('calling_campaign_calling')
            ->whereIn('user_id', $subordinateIds)
            ->pluck('calling_id');

        $states = Calling::whereIn('id', $leadIds)
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::whereIn('id', $leadIds)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        return response()->json([
            'campaigns' => $campaigns,
            'agents' => $agents,
            'states' => $states,
            'cities' => $cities
        ]);
    }

    /**
     * Manager Action: Reassign locked calling resource between subordinates.
     */
    public function reassignTeamCallMobile(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $pivotId = $request->get('pivot_id');
        $newUserId = $request->get('new_user_id');

        if (!$pivotId || !$newUserId) {
            return response()->json(['success' => false, 'message' => 'Missing target lead or agent details.'], 400);
        }

        // 1. Find target pivot
        $pivot = DB::table('calling_campaign_calling')->where('id', $pivotId)->first();
        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Assignment locator not found.'], 404);
        }

        // 2. Authorize target users are manager subordinates
        $subordinateIds = User::whereHas('managers', function($q) use ($user) {
            $q->where('manager_id', $user->id);
        })->pluck('id');

        if (!$subordinateIds->contains($pivot->user_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized resource access.'], 403);
        }

        if (!$subordinateIds->contains($newUserId)) {
            return response()->json(['success' => false, 'message' => 'Target agent must be within your managed group.'], 403);
        }

        $oldUserId = $pivot->user_id;

        try {
            DB::transaction(function() use ($pivotId, $pivot, $oldUserId, $newUserId, $user) {
                // 1. Perform assignment rewrite
                DB::table('calling_campaign_calling')
                    ->where('id', $pivotId)
                    ->update([
                        'user_id' => $newUserId,
                        'updated_at' => now()
                    ]);

                // 2. Commit tracking trace
                \App\Models\CallingAssignmentLog::create([
                    'calling_id' => $pivot->calling_id,
                    'calling_campaign_id' => $pivot->calling_campaign_id,
                    'from_user_id' => $oldUserId,
                    'to_user_id' => $newUserId,
                    'assigned_by' => $user->id,
                    'remark' => 'Manager mobile reassignment'
                ]);
            });

            $agentName = User::where('id', $newUserId)->value('name');
            return response()->json(['success' => true, 'message' => 'Lead successfully reassigned to ' . ($agentName ?? 'Agent')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Reassignment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get calls explicitly assigned by the current user to others.
     */
    public function getAssignedCalls(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $perPage = $request->get('per_page', 10);
        $junkTypeId = CallingType::where('name', 'Junk')->value('id');

        $query = DB::table('calling_campaign_calling')
            ->join('callings', 'calling_campaign_calling.calling_id', '=', 'callings.id')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->leftJoin('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->whereExists(function ($subQuery) use ($user) {
                $subQuery->select(DB::raw(1))
                    ->from('calling_assignment_logs')
                    ->whereColumn('calling_assignment_logs.calling_id', 'callings.id')
                    ->where('calling_assignment_logs.assigned_by', $user->id);
            })
            ->where('calling_campaign_calling.user_id', '!=', $user->id);

        if ($junkTypeId) {
            $query->where(function($q) use ($junkTypeId) {
                $q->where('calling_campaign_calling.calling_type_id', '!=', $junkTypeId)
                  ->orWhereNull('calling_campaign_calling.calling_type_id');
            });
        }

        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $query->where(function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('callings.name', 'like', $like)
                  ->orWhere('callings.email', 'like', $like)
                  ->orWhere('callings.phone', 'like', $like)
                  ->orWhere('users.name', 'like', $like);
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('calling_campaign_calling.calling_campaign_id', $request->campaign_id);
        }
        if ($request->filled('state_name')) {
            $query->where('callings.state', $request->state_name);
        }
        if ($request->filled('city_name')) {
            $query->where('callings.city', $request->city_name);
        }
        if ($request->filled('current_owner_id')) {
            $query->where('calling_campaign_calling.user_id', $request->current_owner_id);
        }

        $query->select(
            'callings.*',
            'calling_campaigns.name as campaign_name',
            'calling_campaign_calling.calling_campaign_id',
            'calling_campaign_calling.user_id as current_owner_id',
            'users.name as current_owner_name',
            'calling_types.name as pivot_status',
            'calling_campaign_calling.id as pivot_id',
            DB::raw('(SELECT remark FROM calling_remarks WHERE calling_id = callings.id ORDER BY id DESC LIMIT 1) as latest_remark_text')
        );

        $paginated = $query->orderBy('calling_campaign_calling.id', 'desc')->paginate($perPage);

        $paginated->getCollection()->transform(function($item) {
            $item->latest_remark = $item->latest_remark_text ? (object)['remark' => $item->latest_remark_text] : null;
            $item->calling_type = $item->pivot_status ? (object)['name' => $item->pivot_status] : null;
            $item->calling_type_name = $item->pivot_status; 
            return $item;
        });

        return response()->json($paginated);
    }

    /**
     * Extract filtering taxonomies scoped inside assigned sets.
     */
    public function getAssignedCallsFilterOptions(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $assignedLeadIds = DB::table('calling_assignment_logs')
            ->where('assigned_by', $user->id)
            ->pluck('calling_id')
            ->unique();

        $campaigns = DB::table('calling_campaign_calling')
            ->join('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->whereIn('calling_campaign_calling.calling_id', $assignedLeadIds)
            ->select('calling_campaigns.id', 'calling_campaigns.name')
            ->distinct()
            ->get();

        $owners = DB::table('calling_campaign_calling')
            ->join('users', 'calling_campaign_calling.user_id', '=', 'users.id')
            ->whereIn('calling_campaign_calling.calling_id', $assignedLeadIds)
            ->where('users.id', '!=', $user->id)
            ->select('users.id', 'users.name')
            ->distinct()
            ->orderBy('users.name')
            ->get();

        $states = Calling::whereIn('id', $assignedLeadIds)
            ->whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        $cities = Calling::whereIn('id', $assignedLeadIds)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->toArray();

        return response()->json([
            'campaigns' => $campaigns,
            'owners' => $owners,
            'states' => $states,
            'cities' => $cities
        ]);
    }

    /**
     * Reassign an active item from assigned stack to another team member.
     */
    public function reassignAssignedCallMobile(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

        $pivotId = $request->get('pivot_id');
        $newUserId = $request->get('new_user_id');

        if (!$pivotId || !$newUserId) {
            return response()->json(['success' => false, 'message' => 'Parameters invalid.'], 400);
        }

        $pivot = DB::table('calling_campaign_calling')->where('id', $pivotId)->first();
        if (!$pivot) {
            return response()->json(['success' => false, 'message' => 'Assignment reference not located.'], 404);
        }

        // Verify the assignment link was originally performed by the authenticated user
        $logExists = \App\Models\CallingAssignmentLog::where('calling_id', $pivot->calling_id)
            ->where('assigned_by', $user->id)
            ->exists();

        if (!$logExists) {
            return response()->json(['success' => false, 'message' => 'Management authority not established.'], 403);
        }

        $oldUserId = $pivot->user_id;

        try {
            DB::transaction(function() use ($pivotId, $pivot, $oldUserId, $newUserId, $user) {
                DB::table('calling_campaign_calling')
                    ->where('id', $pivotId)
                    ->update([
                        'user_id' => $newUserId,
                        'updated_at' => now()
                    ]);

                \App\Models\CallingAssignmentLog::create([
                    'calling_id' => $pivot->calling_id,
                    'calling_campaign_id' => $pivot->calling_campaign_id,
                    'from_user_id' => $oldUserId,
                    'to_user_id' => $newUserId,
                    'assigned_by' => $user->id,
                    'remark' => 'Re-assigned via Assigned mobile view'
                ]);
            });

            $name = User::where('id', $newUserId)->value('name');
            return response()->json([
                'success' => true,
                'message' => 'Lead successfully reassigned to ' . ($name ?? 'Executive')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed saving rewrite: ' . $e->getMessage()], 500);
        }
    }
}
