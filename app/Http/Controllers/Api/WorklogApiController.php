<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Worklog;
use App\Models\EntryType;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Module;
use App\Models\CustomerProject;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\WorklogApproval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WorklogApiController extends Controller
{
    /**
     * Get Worklog Form Metadata (Entry Types, Customers)
     */
    public function getFormData(): JsonResponse
    {
        $user = auth()->user();
        if (!$user->is_worklog && $user->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $entryTypes = EntryType::orderBy('working_hours', 'desc')->get();
        $customers = Customer::orderBy('name')->select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'entry_types' => $entryTypes,
            'customers' => $customers
        ]);
    }

    /**
     * Get Projects by Customer
     */
    public function getProjects($customerId): JsonResponse
    {
        $projects = CustomerProject::where('customer_id', $customerId)
            ->select('project_name')
            ->whereNotNull('project_name')
            ->distinct()
            ->orderBy('project_name')
            ->get()
            ->map(function($row){ return ['name' => $row->project_name]; });

        return response()->json(['success' => true, 'projects' => $projects]);
    }

    /**
     * Get Services by Customer (and optional Project)
     */
    public function getServices(Request $request, $customerId): JsonResponse
    {
        $projectName = $request->query('project_name');
        
        $services = CustomerProject::where('customer_id', $customerId)
            ->when($projectName, function($q) use ($projectName) {
                $q->where('project_name', $projectName);
            })
            ->with('service')
            ->get()
            ->pluck('service')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json(['success' => true, 'services' => $services]);
    }

    /**
     * Get Modules by Service
     */
    public function getModules($serviceId): JsonResponse
    {
        $modules = Module::where('service_id', $serviceId)
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'modules' => $modules]);
    }

    /**
     * Validate Date for Worklog Submission
     */
    public function validateDate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $check = $this->checkDateValidationInternal($request->date);

        return response()->json([
            'success' => $check['valid'],
            'message' => $check['message']
        ]);
    }

    /**
     * Submit Worklog (Multiple Entries)
     */
    public function submit(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        // Basic structure validation
        $validator = Validator::make($request->all(), [
            'work_date' => 'required|date',
            'entries' => 'required|array|min:1',
            'entries.*.entry_type_id' => 'required|exists:entry_types,id',
            'entries.*.customer_id' => 'required|exists:customers,id',
            'entries.*.service_id' => 'required|exists:services,id',
            'entries.*.module_id' => 'required|exists:modules,id',
            'entries.*.hours' => 'required|integer|min:0|max:24',
            'entries.*.minutes' => 'required|integer|min:0|max:59',
            'entries.*.description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        // 1. Date Validation
        $dateCheck = $this->checkDateValidationInternal($request->work_date);
        if (!$dateCheck['valid']) {
            return response()->json(['success' => false, 'message' => $dateCheck['message']], 422);
        }

        // 2. Attendance Validation (Must be punched out/field out)
        if ($request->work_date === date('Y-m-d')) {
            $attendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $request->work_date)
                ->first();

            if ($attendance) {
               $lastMovement = $attendance->movements()->orderBy('id', 'desc')->first();
               if (!$lastMovement || $lastMovement->movement_action !== 'out') {
                    return response()->json([
                        'success' => false,
                        'message' => "You must Punch Out or Field Out before submitting today's worklog."
                    ], 422);
               }
            } else {
                 return response()->json([
                        'success' => false,
                        'message' => "No attendance record found for today. Please Punch In and Out first."
                    ], 422);
            }
        }

        // 3. Entry Type & Duration Validation
        // Assuming all entries for same day use same entry type (as per web logic implied by single dropdown outside loop, 
        // though API allows mixed, we should check against the first one or sum up).
        // The web controller checks total time vs Expected time of the Entry Type.
        // We will take the first entry's entry_type_id as the primary one for validation
        $entryTypeId = $request->entries[0]['entry_type_id'];
        $entryType = EntryType::find($entryTypeId);
        
        $totalMinutes = 0;
        foreach ($request->entries as $entry) {
            $totalMinutes += ($entry['hours'] * 60) + $entry['minutes'];
        }

        $expectedMinutes = $entryType->working_hours * 60;
        if ($totalMinutes < $expectedMinutes) {
            $hours = floor($totalMinutes / 60);
            $mins = $totalMinutes % 60;
            return response()->json([
                'success' => false,
                'message' => "Total logged time ({$hours}h {$mins}m) is less than {$entryType->name} requirement ({$entryType->working_hours}h)."
            ], 422);
        }

        // 4. Save Entries
        DB::beginTransaction();
        try {
            foreach ($request->entries as $entryData) {
                // Check duplicate
                $exists = Worklog::where('work_date', $request->work_date)
                    ->where('entry_type_id', $entryData['entry_type_id'])
                    ->where('customer_id', $entryData['customer_id'])
                    ->where('service_id', $entryData['service_id'])
                    ->where('module_id', $entryData['module_id'])
                    ->where('user_id', $user->id)
                    ->where('description', $entryData['description'])
                    ->exists();

                if ($exists) {
                    DB::rollback();
                    return response()->json(['success' => false, 'message' => 'Duplicate entry detected.'], 422);
                }

                $customerProject = CustomerProject::where('customer_id', $entryData['customer_id'])
                    ->where('service_id', $entryData['service_id'])
                    ->first();
                
                // Fetch names for denormalized columns
                $entryTypeName = EntryType::find($entryData['entry_type_id'])->name ?? null;
                $customerName = Customer::find($entryData['customer_id'])->name ?? null;
                $serviceName = Service::find($entryData['service_id'])->name ?? null;
                $moduleName = Module::find($entryData['module_id'])->name ?? null;

                Worklog::create([
                    'work_date' => $request->work_date,
                    'entry_type_id' => $entryData['entry_type_id'],
                    'entry_type_name' => $entryTypeName,
                    'customer_id' => $entryData['customer_id'],
                    'customer_name' => $customerName,
                    'service_id' => $entryData['service_id'],
                    'service_name' => $serviceName,
                    'module_id' => $entryData['module_id'],
                    'module_name' => $moduleName,
                    'hours' => $entryData['hours'],
                    'minutes' => $entryData['minutes'],
                    'description' => $entryData['description'],
                    'customer_project_id' => $customerProject->id ?? null,
                    'customer_project_name' => $customerProject->project_name ?? null,
                    'status' => 'pending', // Default
                    'user_id' => $user->id
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Worklog submitted successfully']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Worklog History
     */
    public function history(Request $request): JsonResponse
    {
        $user = auth()->user();
        $perPage = $request->input('per_page', 20);

        $query = Worklog::with(['entryType', 'customer'])
            ->where('user_id', $user->id)
            ->orderBy('work_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('month') && $request->filled('year')) {
             $query->whereMonth('work_date', $request->month)
                   ->whereYear('work_date', $request->year);
        }

        $worklogs = $query->paginate($perPage);

        return response()->json($worklogs);
    }

    /**
     * Delete Worklog Entry
     */
    public function destroy($id): JsonResponse
    {
        $user = auth()->user();
        $worklog = Worklog::where('id', $id)->where('user_id', $user->id)->first();

        if (!$worklog) {
            return response()->json(['success' => false, 'message' => 'Entry not found'], 404);
        }

        if (in_array($worklog->status, ['approved', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Cannot delete processed worklog'], 422);
        }

        $worklog->delete();
        return response()->json(['success' => true, 'message' => 'Entry deleted']);
    }

    // ==========================================
    // PRIVATE HELPER METHODS (Ported & Adapted)
    // ==========================================

    private function checkDateValidationInternal($selectedDate)
    {
        $user = auth()->user();
        
        if (!isset($user->created_at)) {
             return ['valid' => true, 'message' => 'Date is valid.'];
        }
        
        $userCreatedDate = $user->created_at->format('Y-m-d');
        if ($selectedDate < $userCreatedDate) {
            return ['valid' => false, 'message' => "Cannot log before account creation ($userCreatedDate)."];
        }

        $lastEntryDate = Worklog::where('user_id', $user->id)
            ->where('work_date', '<', $selectedDate)
            ->max('work_date');

        if (!$lastEntryDate) {
            return ['valid' => true, 'message' => 'Date is valid.'];
        }

        $startDateForCheck = date('Y-m-d', strtotime($lastEntryDate . ' +1 day'));
        $missingDates = $this->getMissingDates($startDateForCheck, $selectedDate);

        if (!empty($missingDates)) {
            $firstMissing = $missingDates[0];
            return ['valid' => false, 'message' => "Please complete missing worklog for {$firstMissing} first."];
        }

        return ['valid' => true, 'message' => 'Date is valid.'];
    }

    private function getMissingDates($startDate, $endDate)
    {
        $user = auth()->user();
        $missingDates = [];
        $currentDate = $startDate;

        while ($currentDate < $endDate) {
            $hasEntry = Worklog::where('user_id', $user->id)->where('work_date', $currentDate)->exists();
            $hasLeave = LeaveRequest::where('user_id', $user->id)
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->where('status', 'approved')
                ->exists();
            
            $hasAttendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $currentDate)
                ->exists();

            // Logic: If attendance exists (any day), user must have a worklog.
            // Leave is not an exemption if they worked (punched in).
            if ($hasAttendance && !$hasEntry) {
                $missingDates[] = $currentDate;
            }

            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        return $missingDates;
    }

    /**
     * Get Pending Worklog Approvals Grouped by User and Work Date
     */
    public function getPendingApprovals(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->role_id == 1) {
            // Admin: Show worklogs from users who have no manager
            $pendingWorklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) {
                    $query->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                ->with(['user', 'entryType', 'customer', 'customerProject', 'service', 'module'])
                ->orderBy('user_id')
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Manager: Show worklogs from their subordinates
            $pendingWorklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('managers', function($q) use ($user) {
                        $q->where('manager_id', $user->id);
                    });
                })
                ->with(['user', 'entryType', 'customer', 'customerProject', 'service', 'module'])
                ->orderBy('user_id')
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Group worklogs by user and date
        $groupedWorklogs = $pendingWorklogs->groupBy(function($worklog) {
            return ($worklog->user->name ?? 'Unknown') . '|' . $worklog->work_date;
        })->map(function($group) {
            return [
                'user_name' => $group->first()->user->name ?? 'Unknown',
                'work_date' => $group->first()->work_date,
                'entries' => $group->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $groupedWorklogs
        ]);
    }

    /**
     * Approve Individual Worklog
     */
    public function approveWorklog(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'rating' => 'required|in:met,below,exceeded',
            'remark' => 'required|string|min:2|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $query = Worklog::where('id', $id)->where('status', 'pending');
        if ($user->role_id == 1) {
            $worklog = $query->whereHas('user', function($q) {
                $q->whereDoesntHave('managers')->where('is_worklog', 1);
            })->first();
        } else {
            $worklog = $query->whereHas('user', function($q) use ($user) {
                $q->whereHas('managers', function($qm) use ($user) {
                    $qm->where('manager_id', $user->id);
                });
            })->first();
        }

        if (!$worklog) {
            return response()->json(['success' => false, 'message' => 'Worklog not found or access denied.'], 404);
        }

        DB::transaction(function () use ($worklog, $user, $request) {
            $worklog->update(['status' => 'approved']);
            WorklogApproval::create([
                'worklog_id' => $worklog->id,
                'approved_by' => $user->id,
                'status' => 'approved',
                'rating' => $request->rating,
                'remark' => $request->remark
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Worklog approved successfully.']);
    }

    /**
     * Reject Individual Worklog
     */
    public function rejectWorklog(Request $request, $id): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'remark' => 'required|string|min:2|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $query = Worklog::where('id', $id)->where('status', 'pending');
        if ($user->role_id == 1) {
            $worklog = $query->whereHas('user', function($q) {
                $q->whereDoesntHave('managers')->where('is_worklog', 1);
            })->first();
        } else {
            $worklog = $query->whereHas('user', function($q) use ($user) {
                $q->whereHas('managers', function($qm) use ($user) {
                    $qm->where('manager_id', $user->id);
                });
            })->first();
        }

        if (!$worklog) {
            return response()->json(['success' => false, 'message' => 'Worklog not found or access denied.'], 404);
        }

        DB::transaction(function () use ($worklog, $user, $request) {
            $worklog->update(['status' => 'rejected']);
            WorklogApproval::create([
                'worklog_id' => $worklog->id,
                'approved_by' => $user->id,
                'status' => 'rejected',
                'rating' => null,
                'remark' => $request->remark
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Worklog rejected successfully.']);
    }

    /**
     * Approve Group of Worklogs
     */
    public function approveGroup(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'work_date' => 'required|date',
            'rating' => 'required|in:met,below,exceeded',
            'remark' => 'required|string|min:2|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $query = Worklog::where('status', 'pending')->where('work_date', $request->work_date);
        if ($user->role_id == 1) {
            $worklogs = $query->whereHas('user', function($q) use ($request) {
                $q->where('name', $request->user_name)
                  ->whereDoesntHave('managers')
                  ->where('is_worklog', 1);
            })->get();
        } else {
            $worklogs = $query->whereHas('user', function($q) use ($request, $user) {
                $q->where('name', $request->user_name)
                  ->whereHas('managers', function($qm) use ($user) {
                      $qm->where('manager_id', $user->id);
                  });
            })->get();
        }

        if ($worklogs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No pending worklogs found for this date and user.'], 404);
        }

        DB::transaction(function () use ($worklogs, $user, $request) {
            $worklogs->each(function($worklog) use ($user, $request) {
                $worklog->update(['status' => 'approved']);
                WorklogApproval::create([
                    'worklog_id' => $worklog->id,
                    'approved_by' => $user->id,
                    'status' => 'approved',
                    'rating' => $request->rating,
                    'remark' => $request->remark
                ]);
            });
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been approved."
        ]);
    }

    /**
     * Reject Group of Worklogs
     */
    public function rejectGroup(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string',
            'work_date' => 'required|date',
            'remark' => 'required|string|min:2|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $query = Worklog::where('status', 'pending')->where('work_date', $request->work_date);
        if ($user->role_id == 1) {
            $worklogs = $query->whereHas('user', function($q) use ($request) {
                $q->where('name', $request->user_name)
                  ->whereDoesntHave('managers')
                  ->where('is_worklog', 1);
            })->get();
        } else {
            $worklogs = $query->whereHas('user', function($q) use ($request, $user) {
                $q->where('name', $request->user_name)
                  ->whereHas('managers', function($qm) use ($user) {
                      $qm->where('manager_id', $user->id);
                  });
            })->get();
        }

        if ($worklogs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No pending worklogs found for this date and user.'], 404);
        }

        DB::transaction(function () use ($worklogs, $user, $request) {
            $worklogs->each(function($worklog) use ($user, $request) {
                $worklog->update(['status' => 'rejected']);
                WorklogApproval::create([
                    'worklog_id' => $worklog->id,
                    'approved_by' => $user->id,
                    'status' => 'rejected',
                    'rating' => null,
                    'remark' => $request->remark
                ]);
            });
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been rejected."
        ]);
    }
}
