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
    public function getRemarks($id)
    {
        $lead = DB::table('callings')
            ->leftJoin('calling_campaign_calling', 'callings.id', '=', 'calling_campaign_calling.calling_id')
            ->leftJoin('calling_campaigns', 'calling_campaign_calling.calling_campaign_id', '=', 'calling_campaigns.id')
            ->leftJoin('calling_types', 'calling_campaign_calling.calling_type_id', '=', 'calling_types.id')
            ->where('callings.id', $id)
            ->select(
                'callings.*',
                'calling_campaigns.name as campaign_name',
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
}
