<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CalendarApiController extends Controller
{
    /**
     * Get unified calendar events grouped by date with metadata tags
     */
    public function getEvents(Request $request): JsonResponse
    {
        $fromStr = $request->query('from');
        $toStr = $request->query('to');

        if (!$fromStr || !$toStr) {
            $now = Carbon::now();
            $from = $now->copy()->startOfMonth()->subDays(7);
            $to = $now->copy()->endOfMonth()->addDays(7);
        } else {
            try {
                $from = Carbon::parse($fromStr);
                $to = Carbon::parse($toStr);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Invalid dates supplied.'], 400);
            }
        }

        $events = [];
        $markedDates = [];

        // 1. FETCH SOCIAL MEDIA CAMPAIGN EVENTS
        if (Schema::hasTable('calendar_event_client') && Schema::hasTable('calendar_events') && Schema::hasTable('calendar_clients') && Schema::hasColumn('calendar_events','event_date')) {
            $cEvents = DB::table('calendar_event_client as cec')
                ->join('calendar_events as ce', 'ce.id', '=', 'cec.event_id')
                ->join('calendar_clients as cc', 'cc.id', '=', 'cec.client_id')
                ->select(
                    'ce.id',
                    DB::raw("CONCAT(cc.name, ' - ', ce.name) as title"),
                    'ce.event_date as event_date'
                )
                ->where('ce.event_date', '>=', $from->toDateString())
                ->where('ce.event_date', '<=', $to->toDateString())
                ->get();

            foreach ($cEvents as $cEvent) {
                $dateKey = Carbon::parse($cEvent->event_date)->toDateString();
                $eventItem = [
                    'id' => 'client_event_' . $cEvent->id,
                    'title' => $cEvent->title,
                    'type' => 'client_event',
                    'status' => 'scheduled',
                    'start_time' => null,
                    'end_time' => null,
                    'color' => '#434AFA', // Theme Purple/Blue
                    'description' => 'Social media client campaign execution.',
                    'date' => $dateKey
                ];
                $events[] = $eventItem;
                $this->addMarkedDate($markedDates, $dateKey, '#434AFA');
            }
        }

        // 2. FETCH CLIENT COMMON EVENTS
        if (Schema::hasTable('calendar_client_common_events') && Schema::hasTable('common_events') && Schema::hasTable('calendar_clients')) {
            $commonEvents = DB::table('calendar_client_common_events as ccce')
                ->join('common_events as cem', 'cem.id', '=', 'ccce.common_event_id')
                ->join('calendar_clients as cc', 'cc.id', '=', 'ccce.client_id')
                ->select(
                    'ccce.id',
                    DB::raw("CONCAT(cc.name, ' - ', cem.name) as title"),
                    'ccce.event_date as event_date'
                )
                ->where('ccce.event_date', '>=', $from->toDateString())
                ->where('ccce.event_date', '<=', $to->toDateString())
                ->get();

            foreach ($commonEvents as $comEv) {
                $dateKey = Carbon::parse($comEv->event_date)->toDateString();
                $eventItem = [
                    'id' => 'common_event_' . $comEv->id,
                    'title' => $comEv->title,
                    'type' => 'common',
                    'status' => 'scheduled',
                    'start_time' => null,
                    'end_time' => null,
                    'color' => '#8B5CF6', // Violet for common group events
                    'description' => 'Group social common campaign schedule.',
                    'date' => $dateKey
                ];
                $events[] = $eventItem;
                $this->addMarkedDate($markedDates, $dateKey, '#8B5CF6');
            }
        }

        // Sort by time/title
        usort($events, function($a, $b) {
            return strcmp($a['date'] . $a['title'], $b['date'] . $b['title']);
        });

        return response()->json([
            'success' => true,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'markedDates' => $markedDates,
            'events' => $events,
        ]);
    }

    /**
     * Load precise clients, status configs, social handles, and checkbox values for a date.
     */
    public function dateHandles($date): JsonResponse
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
            return response()->json([
                'success' => true, 
                'clients' => [], 
                'client_handles' => [],
                'statuses' => [],
                'missed_reasons' => []
            ]);
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
                $clientHandles[$r->client_id][] = ['id' => $r->id, 'name' => $r->name];
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

    /**
     * Toggle checked status for a client's social media post handle
     */
    public function toggleDateHandle(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'client_id' => 'required|integer',
                'social_handle_id' => 'required|integer',
                'is_checked' => 'nullable',
            ]);

            $isCheckedInput = $request->input('is_checked');
            $isChecked = ($isCheckedInput === '1' || $isCheckedInput === 1 || $isCheckedInput === true || $isCheckedInput === 'true');

            if (Schema::hasTable('calendar_social_handles')) {
                $exists = DB::table('calendar_social_handles')->where('id', (int)$validated['social_handle_id'])->exists();
                if (!$exists) {
                    return response()->json(['success' => false, 'message' => 'Social handle not found'], 422);
                }
            }

            if (!Schema::hasTable('calendar_date_client_posts')) {
                return response()->json(['success' => false, 'message' => 'Table not found'], 500);
            }

            $date = $validated['date'];
            $clientId = (int)$validated['client_id'];
            $socialHandleId = (int)$validated['social_handle_id'];

            if ($isChecked) {
                DB::table('calendar_date_client_posts')->updateOrInsert(
                    ['event_date' => $date, 'client_id' => $clientId, 'social_handle_id' => $socialHandleId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            } else {
                DB::table('calendar_date_client_posts')
                    ->where('event_date', $date)
                    ->where('client_id', $clientId)
                    ->where('social_handle_id', $socialHandleId)
                    ->delete();
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get checklists linked with specific calendar status
     */
    public function statusChecklists($id): JsonResponse
    {
        if (!Schema::hasTable('calendar_status_checklist')) {
            return response()->json(['checklists' => [], 'options' => []]);
        }

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

    /**
     * Update/Save comprehensive client daily post status, checklists, missed reason, and meta.
     */
    public function saveDateClientStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'client_id' => 'required|integer',
                'status_id' => 'nullable|integer',
                'checklist_option_ids' => 'nullable|array',
                'missed_reason_id' => 'nullable|integer',
                'descriptions' => 'nullable|string|max:65535',
            ]);

            $date = $validated['date'];
            $clientId = (int)$validated['client_id'];
            $statusId = !empty($validated['status_id']) ? (int)$validated['status_id'] : null;
            $selectedOptionIds = array_map('intval', $validated['checklist_option_ids'] ?? []);
            $missedReasonId = !empty($validated['missed_reason_id']) ? (int)$validated['missed_reason_id'] : null;
            $descriptions = $validated['descriptions'] ?? null;

            if ($statusId && Schema::hasTable('calendar_statuses')) {
                $exists = DB::table('calendar_statuses')->where('id', $statusId)->exists();
                if (!$exists) {
                    return response()->json(['success' => false, 'message' => 'Status not found'], 422);
                }

                if (Schema::hasTable('calendar_status_checklist')) {
                    $checklistIds = DB::table('calendar_status_checklist')->where('status_id', $statusId)->pluck('checklist_id');
                    
                    if ($checklistIds->isNotEmpty()) {
                        $requiredOptions = DB::table('checklist_options')
                            ->whereIn('checklist_id', $checklistIds)
                            ->where('is_active', 1)
                            ->pluck('id');
                        
                        $requiredOptionIds = $requiredOptions->map(fn($x)=>(int)$x)->toArray();
                        
                        $missing = array_values(array_diff($requiredOptionIds, $selectedOptionIds));
                        if (!empty($missing)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Please check all required checklist options.'
                            ], 422);
                        }
                    }
                }
            }

            if (!Schema::hasTable('calendar_date_client_statuses')) {
                return response()->json(['success' => false, 'message' => 'Table configuration error.'], 500);
            }

            DB::beginTransaction();

            if ($statusId) {
                $updateData = [
                    'status_id' => $statusId,
                    'updated_at' => now(),
                ];
                
                if ($descriptions !== null) {
                    $updateData['descriptions'] = trim($descriptions) ?: null;
                }
                
                $statusName = DB::table('calendar_statuses')->where('id', $statusId)->value('name');
                $isMissedStatus = $statusName && strtolower(trim($statusName)) === 'missed';

                if ($isMissedStatus) {
                    if (!$missedReasonId) {
                        return response()->json(['success' => false, 'message' => 'Please select a missed reason.'], 422);
                    }
                    $updateData['missed_reason_id'] = $missedReasonId;
                } else {
                    $updateData['missed_reason_id'] = null;
                }
                
                $existing = DB::table('calendar_date_client_statuses')->where('event_date', $date)->where('client_id', $clientId)->exists();
                if (!$existing) {
                    $updateData['created_at'] = now();
                }
                
                DB::table('calendar_date_client_statuses')->updateOrInsert(
                    ['event_date' => $date, 'client_id' => $clientId],
                    $updateData
                );
            } else {
                if ($descriptions !== null && trim($descriptions)) {
                    $updateData = ['descriptions' => trim($descriptions), 'updated_at' => now()];
                    $existing = DB::table('calendar_date_client_statuses')->where('event_date', $date)->where('client_id', $clientId)->exists();
                    if (!$existing) {
                        $updateData['created_at'] = now();
                    }
                    DB::table('calendar_date_client_statuses')->updateOrInsert(
                        ['event_date' => $date, 'client_id' => $clientId],
                        $updateData
                    );
                } else {
                    DB::table('calendar_date_client_statuses')->where('event_date', $date)->where('client_id', $clientId)->delete();
                }
            }

            if ($statusId && Schema::hasTable('calendar_date_client_checklist_options') && Schema::hasTable('calendar_status_checklist')) {
                $checklistIds = DB::table('calendar_status_checklist')->where('status_id', $statusId)->pluck('checklist_id');
                
                if ($checklistIds->isNotEmpty()) {
                    $requiredOptionIds = DB::table('checklist_options')
                        ->whereIn('checklist_id', $checklistIds)
                        ->where('is_active', 1)
                        ->pluck('id')
                        ->map(fn($x)=>(int)$x)
                        ->toArray();

                    DB::table('calendar_date_client_checklist_options')
                        ->where('event_date', $date)
                        ->where('client_id', $clientId)
                        ->whereIn('checklist_id', $checklistIds)
                        ->delete();

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
                DB::table('calendar_date_client_checklist_options')->where('event_date', $date)->where('client_id', $clientId)->delete();
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper to build react-native-calendars standard markedDates dots configuration
     */
    private function addMarkedDate(&$markedDates, $dateKey, $color)
    {
        if (!isset($markedDates[$dateKey])) {
            $markedDates[$dateKey] = [
                'marked' => true,
                'dots' => []
            ];
        }

        // Limit to max 4 dots to prevent overflow display glitches
        if (count($markedDates[$dateKey]['dots']) < 4) {
            // Check if dot color already exists to keep it neat
            $exists = false;
            foreach ($markedDates[$dateKey]['dots'] as $dot) {
                if ($dot['color'] === $color) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $markedDates[$dateKey]['dots'][] = [
                    'key' => uniqid(),
                    'color' => $color,
                    'selectedDotColor' => '#FFFFFF'
                ];
            }
        }
    }
}
