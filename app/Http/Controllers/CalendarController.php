<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        // Legacy minimal endpoint retained for compatibility; prefer grid()
        if (!Schema::hasTable('calendar_events') || !Schema::hasColumn('calendar_events','event_date')) {
            return response()->json([]);
        }
        $from = $request->query('from');
        $to = $request->query('to');
        $q = DB::table('calendar_events')->select('id','name','event_date');
        if ($from) $q->where('event_date','>=',$from);
        if ($to) $q->where('event_date','<=',$to);
        return response()->json($q->orderBy('event_date')->get());
    }

    public function eventDetails($id)
    {
        if (!Schema::hasTable('calendar_events')) {
            return response()->json(['success' => false, 'message' => 'Table not found'], 404);
        }

        $event = DB::table('calendar_events')->where('id', (int)$id)->first(['id', 'name', 'event_date']);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        // Get related clients
        $clients = [];
        $clientIds = [];
        if (Schema::hasTable('calendar_event_client')) {
            $clientIds = DB::table('calendar_event_client')
                ->where('event_id', (int)$id)
                ->pluck('client_id')
                ->toArray();
            
            if (!empty($clientIds) && Schema::hasTable('calendar_clients')) {
                $clients = DB::table('calendar_clients')
                    ->whereIn('id', $clientIds)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            }
        }

        // Get social handles grouped client-wise (calendar_client_social)
        $socialHandles = [];
        $clientHandles = [];
        if (!empty($clientIds) && Schema::hasTable('calendar_client_social') && Schema::hasTable('calendar_social_handles')) {
            // Build mapping per client
            $rows = DB::table('calendar_client_social as ccs')
                ->join('calendar_social_handles as csh', 'csh.id', '=', 'ccs.social_handle_id')
                ->whereIn('ccs.client_id', $clientIds)
                ->orderBy('csh.name')
                ->get(['ccs.client_id', 'csh.id as id', 'csh.name as name']);

            foreach ($rows as $row) {
                $clientHandles[$row->client_id] = $clientHandles[$row->client_id] ?? [];
                $clientHandles[$row->client_id][] = (object)[ 'id' => $row->id, 'name' => $row->name ];
                $socialHandles[$row->id] = (object)[ 'id' => $row->id, 'name' => $row->name ]; // for aggregated list if needed
            }
            // Re-index aggregated list
            $socialHandles = array_values($socialHandles);
        }

        
        return response()->json([
            'success' => true,
            'event' => $event,
            'clients' => $clients,
            'social_handles' => $socialHandles,
            'client_handles' => $clientHandles,
            
        ]);
    }

    public function statusChecklists($id)
    {
        if (!Schema::hasTable('calendar_status_checklist')) {
            return response()->json(['checklists' => [], 'options' => []]);
        }

        // get active checklists linked to status
        $checklistIds = DB::table('calendar_status_checklist')
            ->where('status_id', (int)$id)
            ->pluck('checklist_id');

        if ($checklistIds->isEmpty()) {
            return response()->json(['checklists' => [], 'options' => []]);
        }

        $checklists = DB::table('checklists')
            ->whereIn('id', $checklistIds)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id','name']);

        // get active options
        $options = DB::table('checklist_options')
            ->whereIn('checklist_id', $checklistIds)
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id','checklist_id','name','sort_order']);

        return response()->json([
            'checklists' => $checklists,
            'options' => $options,
        ]);
    }

    public function grid(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $results = [];

        // Event-client links (calendar events)
        if (Schema::hasTable('calendar_event_client') && Schema::hasTable('calendar_events') && Schema::hasTable('calendar_clients') && Schema::hasColumn('calendar_events','event_date')) {
            $q = DB::table('calendar_event_client as cec')
                ->join('calendar_events as ce', 'ce.id', '=', 'cec.event_id')
                ->join('calendar_clients as cc', 'cc.id', '=', 'cec.client_id')
                ->select(
                    DB::raw("CONCAT(cc.name, ' - ', ce.name) as title"),
                    'ce.event_date as event_date',
                    DB::raw("'event' as type")
                );
            if ($from) $q->where('ce.event_date','>=',$from);
            if ($to) $q->where('ce.event_date','<=',$to);
            $results = array_merge($results, $q->get()->toArray());
        }

        // Client common events with dates
        if (Schema::hasTable('calendar_client_common_events') && Schema::hasTable('common_events') && Schema::hasTable('calendar_clients')) {
            $q2 = DB::table('calendar_client_common_events as ccce')
                ->join('common_events as cem', 'cem.id', '=', 'ccce.common_event_id')
                ->join('calendar_clients as cc', 'cc.id', '=', 'ccce.client_id')
                ->select(
                    DB::raw("CONCAT(cc.name, ' - ', cem.name) as title"),
                    'ccce.event_date as event_date',
                    DB::raw("'common' as type")
                );
            if ($from) $q2->where('ccce.event_date','>=',$from);
            if ($to) $q2->where('ccce.event_date','<=',$to);
            $results = array_merge($results, $q2->get()->toArray());
        }

        // Return normalized list
        return response()->json($results);
    }

    public function dateHandles($date)
    {
        $date = trim($date);
        $clientIds = [];

        // Clients from event-client links on this date
        if (Schema::hasTable('calendar_event_client') && Schema::hasTable('calendar_events') && Schema::hasColumn('calendar_events','event_date')) {
            $ids = DB::table('calendar_event_client as cec')
                ->join('calendar_events as ce', 'ce.id', '=', 'cec.event_id')
                ->where('ce.event_date', $date)
                ->pluck('cec.client_id')
                ->toArray();
            $clientIds = array_merge($clientIds, $ids);
        }

        // Clients from common events on this date
        if (Schema::hasTable('calendar_client_common_events')) {
            $ids = DB::table('calendar_client_common_events')
                ->where('event_date', $date)
                ->pluck('client_id')
                ->toArray();
            $clientIds = array_merge($clientIds, $ids);
        }

        $clientIds = array_values(array_unique(array_map('intval', $clientIds)));
        if (empty($clientIds)) {
            return response()->json(['success' => true, 'clients' => [], 'client_handles' => []]);
        }

        // fetch clients
        $clients = DB::table('calendar_clients')
            ->whereIn('id', $clientIds)
            ->orderBy('name')
            ->get(['id','name']);

        // fetch their social handles
        $clientHandles = [];
        if (Schema::hasTable('calendar_client_social') && Schema::hasTable('calendar_social_handles')) {
            $rows = DB::table('calendar_client_social as ccs')
                ->join('calendar_social_handles as csh', 'csh.id', '=', 'ccs.social_handle_id')
                ->whereIn('ccs.client_id', $clientIds)
                ->orderBy('csh.name')
                ->get(['ccs.client_id','csh.id','csh.name']);
            foreach ($rows as $r) {
                $clientHandles[$r->client_id] = $clientHandles[$r->client_id] ?? [];
                $clientHandles[$r->client_id][] = (object)['id'=>$r->id,'name'=>$r->name];
            }
        }

        // Load checked handles for this date
        $checkedHandles = [];
        if (Schema::hasTable('calendar_date_client_posts')) {
            $checkedRows = DB::table('calendar_date_client_posts')
                ->where('event_date', $date)
                ->whereIn('client_id', $clientIds)
                ->get(['client_id', 'social_handle_id']);
            foreach ($checkedRows as $row) {
                $checkedHandles[$row->client_id] = $checkedHandles[$row->client_id] ?? [];
                $checkedHandles[$row->client_id][] = (int)$row->social_handle_id;
            }
        }

        // Fetch all missed reasons
        $missedReasons = [];
        if (Schema::hasTable('calendar_missed_reasons')) {
            $missedReasons = DB::table('calendar_missed_reasons')
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        // Fetch all calendar statuses
        $statuses = [];
        if (Schema::hasTable('calendar_statuses')) {
            $statuses = DB::table('calendar_statuses')
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        // Load client statuses for this date
        $clientStatuses = [];
        $clientMissedReasons = [];
        $clientDescriptions = [];
        if (Schema::hasTable('calendar_date_client_statuses')) {
            $statusRows = DB::table('calendar_date_client_statuses')
                ->where('event_date', $date)
                ->whereIn('client_id', $clientIds)
                ->get(['client_id', 'status_id', 'missed_reason_id', 'descriptions']);
            foreach ($statusRows as $row) {
                $clientStatuses[$row->client_id] = $row->status_id;
                if ($row->missed_reason_id) {
                    $clientMissedReasons[$row->client_id] = $row->missed_reason_id;
                }
                if ($row->descriptions) {
                    $clientDescriptions[$row->client_id] = $row->descriptions;
                }
            }
        }

        // Load checked checklist options for this date
        $checkedChecklistOptions = [];
        if (Schema::hasTable('calendar_date_client_checklist_options')) {
            $checklistRows = DB::table('calendar_date_client_checklist_options')
                ->where('event_date', $date)
                ->whereIn('client_id', $clientIds)
                ->where('is_done', 1)
                ->get(['client_id', 'option_id']);
            foreach ($checklistRows as $row) {
                $checkedChecklistOptions[$row->client_id] = $checkedChecklistOptions[$row->client_id] ?? [];
                $checkedChecklistOptions[$row->client_id][] = (int)$row->option_id;
            }
        }

        return response()->json([
            'success' => true, 
            'clients' => $clients, 
            'client_handles' => $clientHandles, 
            'checked_handles' => $checkedHandles,
            'statuses' => $statuses,
            'client_statuses' => $clientStatuses,
            'client_missed_reasons' => $clientMissedReasons,
            'client_descriptions' => $clientDescriptions,
            'checked_checklist_options' => $checkedChecklistOptions,
            'missed_reasons' => $missedReasons
        ]);
    }

    public function toggleDateHandle(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'client_id' => 'required|integer',
                'social_handle_id' => 'required|integer',
                'is_checked' => 'nullable',
            ]);

            // Convert is_checked to boolean (handle 1, 0, true, false, "true", "false")
            $isCheckedInput = $request->input('is_checked');
            if ($isCheckedInput === '1' || $isCheckedInput === 1 || $isCheckedInput === true || $isCheckedInput === 'true') {
                $isChecked = true;
            } else {
                $isChecked = false;
            }

            // Check if social handle exists only if table exists
            if (Schema::hasTable('calendar_social_handles')) {
                $exists = DB::table('calendar_social_handles')
                    ->where('id', (int)$validated['social_handle_id'])
                    ->exists();
                if (!$exists) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Social handle not found'
                    ], 422);
                }
            }

            if (!Schema::hasTable('calendar_date_client_posts')) {
                return response()->json(['success' => false, 'message' => 'Table not found'], 500);
            }

            $date = $validated['date'];
            $clientId = (int)$validated['client_id'];
            $socialHandleId = (int)$validated['social_handle_id'];

            if ($isChecked) {
                // Insert if not exists
                DB::table('calendar_date_client_posts')->updateOrInsert(
                    [
                        'event_date' => $date,
                        'client_id' => $clientId,
                        'social_handle_id' => $socialHandleId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            } else {
                // Delete if unchecked
                DB::table('calendar_date_client_posts')
                    ->where('event_date', $date)
                    ->where('client_id', $clientId)
                    ->where('social_handle_id', $socialHandleId)
                    ->delete();
            }

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveDateClientStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'client_id' => 'required|integer',
                'status_id' => 'nullable|integer',
                'checklist_option_ids' => 'nullable|array',
                'checklist_option_ids.*' => 'integer',
                'missed_reason_id' => 'nullable|integer',
                'descriptions' => 'nullable|string|max:65535',
            ]);

            $date = $validated['date'];
            $clientId = (int)$validated['client_id'];
            $statusId = !empty($validated['status_id']) ? (int)$validated['status_id'] : null;
            $selectedOptionIds = array_map('intval', $validated['checklist_option_ids'] ?? []);
            $missedReasonId = !empty($validated['missed_reason_id']) ? (int)$validated['missed_reason_id'] : null;
            $descriptions = $validated['descriptions'] ?? null;

            // Check if status exists if status_id is provided
            if ($statusId && Schema::hasTable('calendar_statuses')) {
                $exists = DB::table('calendar_statuses')
                    ->where('id', $statusId)
                    ->exists();
                if (!$exists) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Status not found'
                    ], 422);
                }

                // If status has linked checklists, validate all options are checked
                if (Schema::hasTable('calendar_status_checklist')) {
                    $checklistIds = DB::table('calendar_status_checklist')
                        ->where('status_id', $statusId)
                        ->pluck('checklist_id');
                    
                    if ($checklistIds->isNotEmpty()) {
                        $requiredOptions = DB::table('checklist_options')
                            ->whereIn('checklist_id', $checklistIds)
                            ->where('is_active', 1)
                            ->pluck('id');
                        
                        $requiredOptionIds = $requiredOptions->map(fn($x)=>(int)$x)->toArray();
                        
                        // Check if all required options are selected
                        $missing = array_values(array_diff($requiredOptionIds, $selectedOptionIds));
                        if (!empty($missing)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Please check all required checklist options for the selected status.'
                            ], 422);
                        }
                    }
                }
            }

            if (!Schema::hasTable('calendar_date_client_statuses')) {
                return response()->json(['success' => false, 'message' => 'Status table not found'], 500);
            }

            DB::beginTransaction();

            // Save or update status
            if ($statusId) {
                $updateData = [
                    'status_id' => $statusId,
                    'updated_at' => now(),
                ];
                
                // Add descriptions if provided
                if ($descriptions !== null) {
                    $updateData['descriptions'] = trim($descriptions) ?: null;
                }
                
                // Only set missed_reason_id if status is "missed" (case-insensitive check)
                $statusName = DB::table('calendar_statuses')->where('id', $statusId)->value('name');
                $isMissedStatus = $statusName && strtolower(trim($statusName)) === 'missed';

                if ($isMissedStatus) {
                    if (!$missedReasonId) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please select a missed reason for the missed status.'
                        ], 422);
                    }

                    $reasonExists = DB::table('calendar_missed_reasons')
                        ->where('id', $missedReasonId)
                        ->where('is_active', 1)
                        ->exists();

                    if (!$reasonExists) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected missed reason is invalid.'
                        ], 422);
                    }

                    $updateData['missed_reason_id'] = $missedReasonId;
                } else {
                    // Clear missed_reason_id if status is not missed
                    $updateData['missed_reason_id'] = null;
                }
                
                // Set created_at only on insert
                $existing = DB::table('calendar_date_client_statuses')
                    ->where('event_date', $date)
                    ->where('client_id', $clientId)
                    ->exists();
                
                if (!$existing) {
                    $updateData['created_at'] = now();
                }
                
                DB::table('calendar_date_client_statuses')->updateOrInsert(
                    [
                        'event_date' => $date,
                        'client_id' => $clientId,
                    ],
                    $updateData
                );
            } else {
                // If no status but descriptions provided, create/update record with just descriptions
                if ($descriptions !== null && trim($descriptions)) {
                    $updateData = [
                        'descriptions' => trim($descriptions),
                        'updated_at' => now(),
                    ];
                    
                    $existing = DB::table('calendar_date_client_statuses')
                        ->where('event_date', $date)
                        ->where('client_id', $clientId)
                        ->exists();
                    
                    if (!$existing) {
                        $updateData['created_at'] = now();
                    }
                    
                    DB::table('calendar_date_client_statuses')->updateOrInsert(
                        [
                            'event_date' => $date,
                            'client_id' => $clientId,
                        ],
                        $updateData
                    );
                } else {
                    // Delete status if cleared and no descriptions
                    DB::table('calendar_date_client_statuses')
                        ->where('event_date', $date)
                        ->where('client_id', $clientId)
                        ->delete();
                }
            }

            // Save checklist options if status has checklists
            if ($statusId && Schema::hasTable('calendar_date_client_checklist_options') && Schema::hasTable('calendar_status_checklist')) {
                $checklistIds = DB::table('calendar_status_checklist')
                    ->where('status_id', $statusId)
                    ->pluck('checklist_id');
                
                if ($checklistIds->isNotEmpty()) {
                    $requiredOptionIds = DB::table('checklist_options')
                        ->whereIn('checklist_id', $checklistIds)
                        ->where('is_active', 1)
                        ->pluck('id')
                        ->map(fn($x)=>(int)$x)
                        ->toArray();

                    // Delete existing checklist options for this date/client/status
                    DB::table('calendar_date_client_checklist_options')
                        ->where('event_date', $date)
                        ->where('client_id', $clientId)
                        ->whereIn('checklist_id', $checklistIds)
                        ->delete();

                    // Insert checklist options with is_done status
                    $rows = [];
                    foreach ($requiredOptionIds as $optId) {
                        $clId = DB::table('checklist_options')->where('id', $optId)->value('checklist_id');
                        $rows[] = [
                            'event_date' => $date,
                            'client_id' => $clientId,
                            'checklist_id' => (int)$clId,
                            'option_id' => (int)$optId,
                            'is_done' => in_array($optId, $selectedOptionIds, true) ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($rows)) {
                        DB::table('calendar_date_client_checklist_options')->insert($rows);
                    }
                }
            } else if (!$statusId && Schema::hasTable('calendar_date_client_checklist_options')) {
                // Delete all checklist options if status is cleared
                DB::table('calendar_date_client_checklist_options')
                    ->where('event_date', $date)
                    ->where('client_id', $clientId)
                    ->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }
}


