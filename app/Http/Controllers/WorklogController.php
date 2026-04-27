<?php

namespace App\Http\Controllers;

use App\Models\Worklog;
use App\Models\EntryType;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Module;
use App\Models\CustomerProject;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\WorklogApproval;
use App\Services\DailyStatusService;

class WorklogController extends Controller
{
    protected $statusService;

    public function __construct(DailyStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        
        // Check if user has worklog permission (allow if is_worklog is true OR user is admin/role_id=1)
        if (!$user || (!$user->is_worklog && $user->role_id != 1)) {
            return redirect()->back()->with('error', 'You do not have permission to access worklog functionality.');
        }
        
        return view('worklog.index');
    }

    /**
     * Get current user from Auth or session
     */
    private function getCurrentUser()
    {
        // Check if user is authenticated via Auth facade (super admin)
        if (Auth::check()) {
            return Auth::user();
        }
        
        // Check if user is authenticated via session (tenant users)
        if (session()->has('user_id')) {
            $userId = session('user_id');
            $userName = session('user_name');
            $userRole = session('user_role');
            $tenantId = session('tenant_id');
            
            // Load actual user data from tenant database
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    return $user; // Return the actual user model with real is_worklog value
                }
            } catch (\Exception $e) {
                // If user not found, create a mock user with is_worklog = false
            }
            
            // Create a mock user object for tenant users (fallback)
            return new class($userId, $userName, $userRole, $tenantId) {
                public $id;
                public $name;
                public $role_id;
                public $tenant_id;
                public $is_worklog;
                public $created_at;
                
                public function __construct($id, $name, $roleId, $tenantId) {
                    $this->id = $id;
                    $this->name = $name;
                    $this->role_id = $roleId;
                    $this->tenant_id = $tenantId;
                    $this->is_worklog = false; // Default to false for safety
                    $this->created_at = now(); // Default to current time
                }
            };
        }
        
        return null;
    }



    public function getEntryTypes()
    {
        $entryTypes = EntryType::orderBy('working_hours', 'desc')
            ->get();

        return response()->json($entryTypes);
    }

    public function getCustomers()
    {
        $customers = Customer::orderBy('name')
            ->get();

        return response()->json($customers);
    }

    public function getProjects()
    {
        $projects = Service::orderBy('name')
            ->get();

        return response()->json($projects);
    }

    // New: get distinct projects by customer from customer_projects
    public function getProjectsByCustomerOnly($customerId)
    {
        $projects = CustomerProject::where('customer_id', $customerId)
            ->where('status', 'Ongoing')
            ->select('project_name')
            ->whereNotNull('project_name')
            ->distinct()
            ->orderBy('project_name')
            ->get()
            ->map(function($row){ return ['name' => $row->project_name]; });

        return response()->json($projects);
    }

    // New: list services by customer and selected project name
    public function getServicesByCustomerProject($customerId, Request $request)
    {
        $projectName = $request->query('project_name');
        $services = CustomerProject::where('customer_id', $customerId)
            ->where('status', 'Ongoing')
            ->when($projectName, function($q) use ($projectName) {
                $q->where('project_name', $projectName);
            })
            ->with('service')
            ->get()
            ->pluck('service')
            ->filter()
            ->unique('id')
            ->values();

        return response()->json($services);
    }
    public function getProjectsByCustomer($customerId)
    {
        $projects = CustomerProject::where('customer_id', $customerId)
            ->where('status', 'Ongoing')
            ->with('service')
            ->get()
            ->pluck('service')
            ->unique('id')
            ->values();

        return response()->json($projects);
    }

    public function getModulesByService($serviceId)
    {
        $modules = Module::where('service_id', $serviceId)
            ->orderBy('name')
            ->get();

        return response()->json($modules);
    }

    public function addToSession(Request $request)
    {
        $user = $this->getCurrentUser();
        
        // Check if user has worklog permission
        if (!$user || !$user->is_worklog) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to submit worklog entries.'
            ], 403);
        }

        $request->validate([
            'work_date' => 'required|date',
            'entry_type_id' => 'required|exists:entry_types,id',
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'module_id' => 'required|exists:modules,id',
            'hours' => 'required|integer|min:0|max:24',
            'minutes' => 'required|integer|min:0|max:59',
            'description' => 'required|string|max:1000']);

        // Check date validation
        $dateValidation = $this->checkDateValidationInternal($request->work_date);
        if (!$dateValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $dateValidation['message']
            ], 422);
        }

        // Get entry type for session storage (validation moved to final submit)
        $entryType = EntryType::find($request->entry_type_id);
        $totalMinutes = ($request->hours * 60) + $request->minutes;

        // Check for duplicate entry
        $existingWorklog = Worklog::where('work_date', $request->work_date)
            ->where('entry_type_id', $request->entry_type_id)
            ->where('customer_id', $request->customer_id)
            ->where('service_id', $request->service_id)
            ->where('module_id', $request->module_id)
            ->where('user_id', $user->id)
            ->where('description', $request->description)
            ->first();

        if ($existingWorklog) {
            return response()->json([
                'success' => false,
                'message' => 'This entry already exists in the database.'
            ], 422);
        }

        // Add to session
        $sessionKey = 'worklog_entries_' . $user->id;
        $entries = Session::get($sessionKey, []);
        
        // Find matching customer_project for traceability
        $customerProject = CustomerProject::where('customer_id', $request->customer_id)
            ->where('service_id', $request->service_id)
            ->first();
        $customerProjectId = $customerProject->id ?? null;
        $customerProjectName = $customerProject->project_name ?? null;

        // Get related models with null checks
        $customer = Customer::find($request->customer_id);
        $service = Service::find($request->service_id);
        $module = Module::find($request->module_id);

        $newEntry = [
            'id' => uniqid(),
            'work_date' => $request->work_date,
            'entry_type_id' => $request->entry_type_id,
            'entry_type_name' => $entryType->name ?? 'N/A',
            'customer_id' => $request->customer_id,
            'customer_name' => $customer->name ?? 'N/A',
            'service_id' => $request->service_id,
            'service_name' => $service->name ?? 'N/A',
            'module_id' => $request->module_id,
            'module_name' => $module->name ?? 'N/A',
            'hours' => $request->hours,
            'minutes' => $request->minutes,
            'description' => $request->description,
            'total_minutes' => $totalMinutes,
            'customer_project_id' => $customerProjectId,
            'customer_project_name' => $customerProjectName ?? 'N/A'];


        $entries[] = $newEntry;
        Session::put($sessionKey, $entries);

        return response()->json([
            'success' => true,
            'message' => 'Entry added to session successfully.',
            'entry' => $newEntry,
            'total_entries' => count($entries)
        ]);
    }

    public function getSessionEntries()
    {
        $user = $this->getCurrentUser();
        $sessionKey = 'worklog_entries_' . $user->id;
        $entries = Session::get($sessionKey, []);

        return response()->json($entries);
    }

    public function removeFromSession(Request $request)
    {
        $request->validate([
            'entry_id' => 'required|string'
        ]);

        $user = $this->getCurrentUser();
        $sessionKey = 'worklog_entries_' . $user->id;
        $entries = Session::get($sessionKey, []);

        $entries = array_filter($entries, function($entry) use ($request) {
            return $entry['id'] !== $request->entry_id;
        });

        Session::put($sessionKey, array_values($entries));

        return response()->json([
            'success' => true,
            'message' => 'Entry removed from session.',
            'total_entries' => count($entries)
        ]);
    }

    public function clearSession()
    {
        $user = $this->getCurrentUser();
        $sessionKey = 'worklog_entries_' . $user->id;
        Session::forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => 'Session cleared successfully.'
        ]);
    }

    public function submitWorklog(Request $request)
    {
        $request->validate([
            'work_date' => 'required|date',
            'entry_type_id' => 'required|exists:entry_types,id']);

        $user = $this->getCurrentUser();
        $sessionKey = 'worklog_entries_' . $user->id;
        $entries = Session::get($sessionKey, []);

        if (empty($entries)) {
            return response()->json([
                'success' => false,
                'message' => 'No entries to submit.'
            ], 422);
        }

        // Get entry type
        $entryType = EntryType::find($request->entry_type_id);
        $expectedMinutes = $entryType->working_hours * 60;

        // Calculate total minutes from session entries
        $totalMinutes = 0;
        foreach ($entries as $entry) {
            $totalMinutes += $entry['total_minutes'];
        }

        // Check if total time is at least equal to entry type (but allow exceeding)
        if ($totalMinutes < $expectedMinutes) {
            return response()->json([
                'success' => false,
                'message' => "Total logged time ({$this->formatMinutes($totalMinutes)}) is less than {$entryType->name} working hours ({$entryType->working_hours}h). Please add more entries."
            ], 422);
        }

        // Validate Punch Out / Field Out for today's worklog
        $submittedDate = $entries[0]['work_date']; // All entries share the same date due to session logic
        if ($submittedDate === date('Y-m-d')) {
            $attendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $submittedDate)
                ->first();

            if ($attendance) {
                // Get the very last movement action
               $lastMovement = $attendance->movements()->orderBy('id', 'desc')->first();
               
               // The valid actions in DB are: 'in', 'out', 'start', 'end'
               // User is considered "Punched Out" ONLY if the last action is 'out'.
               // If last action is 'in' (Working), 'start' (On Break), or 'end' (Back from Break), they are not fully Punched Out.
               
               if (!$lastMovement || $lastMovement->movement_action !== 'out') {
                    return response()->json([
                        'success' => false,
                        'message' => "You must Punch Out or Field Out before submitting today's worklog."
                    ], 422);
               }
            } else {
                 // No attendance record for today implies they never punched in. 
                 return response()->json([
                        'success' => false,
                        'message' => "No attendance record found for today. Please Punch In and Punch Out before submitting worklog."
                    ], 422);
            }
        }

        DB::beginTransaction();
        try {
            foreach ($entries as $entry) {
                // Check for duplicate before inserting
                $existingWorklog = Worklog::where('work_date', $entry['work_date'])
                    ->where('entry_type_id', $entry['entry_type_id'])
                    ->where('customer_id', $entry['customer_id'])
                    ->where('service_id', $entry['service_id'])
                    ->where('module_id', $entry['module_id'])
                    ->where('user_id', $user->id)
                    ->where('description', $entry['description'])
                    ->first();

                if ($existingWorklog) {
                    DB::rollback();
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more entries already exist in the database. Please refresh and try again.'
                    ], 422);
                }

                // Determine status based on whether user has a manager
                // If user has no manager, worklog goes to admin for approval
                $status = $user->managers()->exists() ? 'pending' : 'pending';
                
                
                Worklog::create([
                    'work_date' => $entry['work_date'],
                    'entry_type_id' => $entry['entry_type_id'],
                    'entry_type_name' => $entry['entry_type_name'] ?? ($entryType->name ?? null),
                    'customer_id' => $entry['customer_id'],
                    'customer_name' => $entry['customer_name'] ?? null,
                    'service_id' => $entry['service_id'],
                    'service_name' => $entry['service_name'] ?? null,
                    'module_id' => $entry['module_id'],
                    'module_name' => $entry['module_name'] ?? null,
                    'hours' => $entry['hours'],
                    'minutes' => $entry['minutes'],
                    'description' => $entry['description'],
                    'customer_project_id' => $entry['customer_project_id'] ?? null,
                    'customer_project_name' => $entry['customer_project_name'] ?? null,
                    'status' => $status,
                    'user_id' => $user->id]);
            }

            // Clear session after successful save
            Session::forget($sessionKey);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Worklog submitted successfully! Session cleared.',
                'total_entries' => count($entries)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error saving worklog entries: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return "{$hours}h {$mins}m";
    }

    public function destroy($id)
    {
        $user = $this->getCurrentUser();
        $worklog = Worklog::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Prevent deletion if worklog is approved or rejected
        if (in_array($worklog->status, ['approved', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete worklog that has been approved or rejected.'
            ], 422);
        }

        $worklog->delete();

        return response()->json(['success' => true]);
    }

    public function checkDateValidation(Request $request)
    {
        $selectedDate = $request->input('date');
        $user = $this->getCurrentUser();
        
        // For tenant users, we don't have created_at, so skip this validation
        if (!isset($user->created_at)) {
            return response()->json([
                'valid' => true,
                'message' => 'Date validation passed.'
            ]);
        }
        
        // Get the current user's creation date
        $userCreatedDate = $user->created_at ? $user->created_at->format('Y-m-d') : date('Y-m-d');
        
        // Check if selected date is before the current user's creation date
        if ($selectedDate < $userCreatedDate) {
            return response()->json([
                'valid' => false,
                'message' => "You cannot log work for dates before your account creation date ({$userCreatedDate})."
            ]);
        }
        
        // Enforce per-user chronological order starting from the user's LAST filled worklog date
        // Find the most recent worklog entry before the selected date
        $lastEntryDate = \App\Models\Worklog::where('user_id', $user->id)
            ->where('work_date', '<', $selectedDate)
            ->max('work_date');

        // If user has no prior entries, allow submitting for any date (post account creation)
        if (!$lastEntryDate) {
            return response()->json([
                'valid' => true,
                'message' => 'Date is valid for worklog entry.'
            ]);
        }

        // Require that there are no gaps from last entry date up to the selected date
        $startDateForCheck = date('Y-m-d', strtotime($lastEntryDate . ' +1 day'));
        $missingDates = $this->getMissingDates($startDateForCheck, $selectedDate);

        if (!empty($missingDates)) {
            $firstMissingDate = $missingDates[0];
            return response()->json([
                'valid' => false,
                'message' => "Please complete your missing worklog entry for {$firstMissingDate} before filling {$selectedDate}."
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'message' => 'Date is valid for worklog entry.'
        ]);
    }

    private function getMissingDates($startDate, $endDate)
    {
        $user = $this->getCurrentUser();
        $missingDates = [];
        
        $currentDate = $startDate;
        while ($currentDate < $endDate) { 
            // Use Unified Status Service
            if ($this->statusService->isWorklogMissing($user->id, $currentDate)) {
                $missingDates[] = $currentDate;
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        return $missingDates;
    }

    /**
     * Check if ALL users with isWorklog = 1 have filled their entries for a given date
     * This is used for global validation to ensure chronological order across all users
     */
    private function checkAllUsersWorklogCompletion($date)
    {
        // Get all users with isWorklog = 1
        $worklogUsers = \App\Models\User::where('is_worklog', 1)
            ->get();
        
        if ($worklogUsers->isEmpty()) {
            return true;
        }
        
        foreach ($worklogUsers as $user) {
            // Use Unified Status Service to check if log is missing
            if ($this->statusService->isWorklogMissing($user->id, $date)) {
                return false;
            }
        }
        
        return true; 
    }

    /**
     * Check if ALL users with isWorklog = 1 (who were created on or before the given date) have completed this date
     * This ensures users are only required to fill entries from their own creation date onwards
     */
    private function checkAllUsersWorklogCompletionForDate($date)
    {
        
        // Get all users with isWorklog = 1 who were created on or before this date
        $worklogUsers = \App\Models\User::where('is_worklog', 1)
            ->where('created_at', '<=', $date . ' 23:59:59') // Include the entire day
            ->get();
        
        if ($worklogUsers->isEmpty()) {
            return true; // No worklog users existed on this date, so validation passes
        }
        
        // Check if all eligible worklog users have entries for this date
        foreach ($worklogUsers as $user) {
            // Check if user has attendance (if not, they are exempt)
            $hasAttendance = \App\Models\Attendance::where('user_id', $user->id)
                ->where('date', $date)
                ->exists();
                
            if (!$hasAttendance) continue;

            // Check if user has worklog entry
            $hasWorklogEntry = Worklog::where('user_id', $user->id)
                ->where('work_date', $date)
                ->exists();
            
            // Check if user has approved leave for this date
            $hasLeave = LeaveRequest::where('user_id', $user->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->where('status', 'approved')
                ->exists();
            
            // User must have a worklog entry if they have attendance
            // Approved leave (half-day/short leave) is not an exemption if they worked
            if (!$hasWorklogEntry) {
                return false; // At least one eligible user is missing an entry
            }
        }
        
        return true; // All eligible users have entries or approved leave for this date
    }

    /**
     * Get missing dates considering ALL users with isWorklog = 1
     * This ensures that no one can fill next day's entry until ALL users complete previous days
     * Each user is only required to fill entries from their own creation date onwards
     */
    private function getGlobalMissingDates($startDate, $endDate)
    {
        $missingDates = [];
        
        $currentDate = $startDate;
        while ($currentDate < $endDate) {
            // Check if ALL worklog users (who were created on or before this date) have completed this date
            // The checkAllUsersWorklogCompletionForDate function already skips users without attendance
            // so this will only block Sundays/Holidays if at least one user worked and hasn't filled a log.
            if (!$this->checkAllUsersWorklogCompletionForDate($currentDate)) {
                // If it's a Sunday or Holiday, we only add to missing if someone actually has attendance
                // which is exactly what checkAllUsersWorklogCompletionForDate checks.
                $missingDates[] = $currentDate;
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        return $missingDates;
    }

    /**
     * Get the earliest creation date among all users with isWorklog = 1
     * This ensures global validation starts from the earliest user's creation date
     */
    private function getEarliestWorklogUserCreationDate()
    {
        
        $earliestUser = \App\Models\User::where('is_worklog', 1)
            ->orderBy('created_at', 'asc')
            ->first();
        
        return $earliestUser && $earliestUser->created_at ? $earliestUser->created_at->format('Y-m-d') : date('Y-m-d');
    }

    /**
     * Get list of users who are missing worklog entries for a specific date
     * This helps users understand who needs to complete their entries
     */
    public function getMissingUsersForDate(Request $request)
    {
        $date = $request->input('date');
        
        // Get all users with isWorklog = 1 who were created on or before this date and don't have entries or leave
        $missingUsers = \App\Models\User::where('is_worklog', 1)
            ->where('created_at', '<=', $date . ' 23:59:59') // Only users who existed on this date
            ->whereHas('attendance', function($query) use ($date) {
                $query->where('date', $date); // Must have attendance (not absent)
            })
            ->whereDoesntHave('worklogs', function($query) use ($date) {
                $query->where('work_date', $date);
            })
            ->whereDoesntHave('leaveRequests', function($query) use ($date) {
                $query->where('start_date', '<=', $date)
                      ->where('end_date', '>=', $date)
                      ->where('status', 'approved');
            })
            ->select('id', 'name', 'email')
            ->get();
        
        return response()->json([
            'date' => $date,
            'missing_users' => $missingUsers,
            'count' => $missingUsers->count()
        ]);
    }

    /**
     * Check if the current user can submit worklog entries for a specific date
     * This is a helper method for frontend validation
     */
    public function canSubmitWorklog(Request $request)
    {
        $selectedDate = $request->input('date');
        $validation = $this->checkDateValidationInternal($selectedDate);
        
        return response()->json([
            'can_submit' => $validation['valid'],
            'message' => $validation['message'],
            'date' => $selectedDate
        ]);
    }

    /**
     * Get a summary of missing worklog entries across all users
     * This helps team leaders understand what needs to be completed
     */
    public function getMissingEntriesSummary()
    {
        
        // Get all users with isWorklog = 1
        $worklogUsers = \App\Models\User::where('is_worklog', 1)
            ->get();
        
        if ($worklogUsers->isEmpty()) {
            return response()->json([
                'message' => 'No users with worklog access found.',
                'summary' => []
            ]);
        }
        
        // Get the earliest creation date
        $earliestDate = $this->getEarliestWorklogUserCreationDate();
        $today = date('Y-m-d');
        
        $summary = [];
        $currentDate = $earliestDate;
        
        while ($currentDate <= $today) {
            // Check if the date is a holiday
            $isHoliday = Holiday::where('holiday_date', $currentDate)
                ->exists();
            
            // Check if the date is a Sunday (0 = Sunday)
            // (Note: This global summary check still uses Sunday as a default reference 
            // since it loops through all users who might have different weekoffs)
            $isSunday = date('w', strtotime($currentDate)) == 0;
            
            // Now checking all days.
            // The logic below (line 782) will correctly skip Sundays/Holidays if no attendance exists.
            if (true) {
                $missingUsers = [];
                
                // Only check users who were created on or before this date
                $eligibleUsers = $worklogUsers->filter(function($user) use ($currentDate) {
                    return $user->created_at && $user->created_at->format('Y-m-d') <= $currentDate;
                });
                
                foreach ($eligibleUsers as $user) {
                    // Check if user has attendance (if not, they are exempt)
                    $hasAttendance = \App\Models\Attendance::where('user_id', $user->id)
                        ->where('date', $currentDate)
                        ->exists();
                        
                    if (!$hasAttendance) continue;

                    $hasEntry = Worklog::where('user_id', $user->id)
                        ->where('work_date', $currentDate)
                        ->exists();
                    
                    // Check if user has approved leave for this date
                    $hasLeave = LeaveRequest::where('user_id', $user->id)
                        ->where('start_date', '<=', $currentDate)
                        ->where('end_date', '>=', $currentDate)
                        ->where('status', 'approved')
                        ->exists();
                    
                    // User is missing worklog if they have attendance and no entry
                    // Leave is not an exemption if attendance exists
                    if (!$hasEntry) {
                        $missingUsers[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email
                        ];
                    }
                }
                
                if (!empty($missingUsers)) {
                    $summary[] = [
                        'date' => $currentDate,
                        'missing_users' => $missingUsers,
                        'count' => count($missingUsers)
                    ];
                }
            }
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        return response()->json([
            'summary' => $summary,
            'total_missing_dates' => count($summary)
        ]);
    }

    private function checkDateValidationInternal($selectedDate)
    {
        $user = $this->getCurrentUser();
        
        // For tenant users without created_at, skip date validation
        if (!isset($user->created_at)) {
            return [
                'valid' => true,
                'message' => 'Date is valid for worklog entry.'
            ];
        }
        
        // Get the current user's creation date
        $userCreatedDate = $user && $user->created_at ? $user->created_at->format('Y-m-d') : date('Y-m-d');
        
        // Check if selected date is before the current user's creation date
        if ($selectedDate < $userCreatedDate) {
            return [
                'valid' => false,
                'message' => "You cannot log work for dates before your account creation date ({$userCreatedDate})."
            ];
        }
        
        // Enforce per-user chronological order starting from the user's LAST filled worklog date
        // Find the most recent worklog entry before the selected date
        $lastEntryDate = Worklog::where('user_id', $user->id)
            ->where('work_date', '<', $selectedDate)
            ->max('work_date');

        // If user has no prior entries before selected date, allow submitting
        if (!$lastEntryDate) {
            // Check if there are any missing dates from user creation to selected date
            $missingDates = $this->getMissingDates($userCreatedDate, $selectedDate);
            
            if (!empty($missingDates)) {
                $firstMissingDate = $missingDates[0];
                return [
                    'valid' => false,
                    'message' => "Please complete your missing worklog entry for {$firstMissingDate} before filling {$selectedDate}."
                ];
            }
            
            return [
                'valid' => true,
                'message' => 'Date is valid for worklog entry.'
            ];
        }

        // Require that there are no gaps from last entry date up to the selected date
        $startDateForCheck = date('Y-m-d', strtotime($lastEntryDate . ' +1 day'));
        $missingDates = $this->getMissingDates($startDateForCheck, $selectedDate);

        if (!empty($missingDates)) {
            $firstMissingDate = $missingDates[0];
            return [
                'valid' => false,
                'message' => "Please complete your missing worklog entry for {$firstMissingDate} before filling {$selectedDate}."
            ];
        }

        // If no gaps between last filled date and selected date, it's valid
        return [
            'valid' => true,
            'message' => 'Date is valid for worklog entry.'
        ];
    }

    public function approveWorklog($id)
    {
        $user = $this->getCurrentUser();
        
        if ($user->role_id == 1) {
            // Admin: Can approve worklogs from users without managers
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) {
                    $query->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                ->firstOrFail();
        } else {
            // Manager: Can approve worklogs from their subordinates
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('managers', function($q) use ($user) {
                        $q->where('manager_id', $user->id);
                    });
                })
                ->firstOrFail();
        }

        // Require rating and remark
        request()->validate([
            'rating' => 'required|in:met,below,exceeded',
            'remark' => 'required|string|min:2|max:1000']);

        DB::transaction(function () use ($worklog, $user) {
            $worklog->update(['status' => 'approved']);
            WorklogApproval::create([
                'worklog_id' => $worklog->id,
                'approved_by' => $user->id,
                'status' => 'approved',
                'rating' => request('rating'),
                'remark' => request('remark')]);
        });

        return response()->json(['success' => true, 'message' => 'Worklog approved successfully.']);
    }

    public function rejectWorklog($id)
    {
        $user = $this->getCurrentUser();
        
        if ($user->role_id == 1) {
            // Admin: Can reject worklogs from users without managers
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) {
                    $query->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                ->firstOrFail();
        } else {
            // Manager: Can reject worklogs from their subordinates
            $worklog = Worklog::where('id', $id)
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('managers', function($q) use ($user) {
                        $q->where('manager_id', $user->id);
                    });
                })
                ->firstOrFail();
        }

        // Require rejection reason
        request()->validate([
            'remark' => 'required|string|min:2|max:1000']);

        DB::transaction(function () use ($worklog, $user) {
            $worklog->update(['status' => 'rejected']);
            WorklogApproval::create([
                'worklog_id' => $worklog->id,
                'approved_by' => $user->id,
                'status' => 'rejected',
                'rating' => null,
                'remark' => request('remark')]);
        });

        return response()->json(['success' => true, 'message' => 'Worklog rejected successfully.']);
    }

    public function getPendingApprovals()
    {
        $user = $this->getCurrentUser();
        
        // If user is admin (role_id = 1), show worklogs from users without managers
        // If user is manager, show worklogs from their subordinates
        if ($user->role_id == 1) {
            // Admin: Show worklogs from users who have no manager
            $pendingWorklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) {
                    $query->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                
                ->with(['user', 'entryType', 'customer', 'project', 'module'])
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
                
                ->with(['user', 'entryType', 'customer', 'project', 'module'])
                ->orderBy('user_id')
                ->orderBy('work_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Group worklogs by user and date
        $groupedWorklogs = $pendingWorklogs->groupBy(function($worklog) {
            return $worklog->user->name . '|' . $worklog->work_date;
        })->map(function($group) {
            return [
                'user_name' => $group->first()->user->name,
                'work_date' => $group->first()->work_date,
                'entries' => $group->values()
            ];
        })->values();

        return response()->json($groupedWorklogs);
    }

    public function approveGroup(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'work_date' => 'required|date',
            'rating' => 'required|in:met,below,exceeded',
            'remark' => 'required|string|min:2|max:2000']);

        $user = $this->getCurrentUser();
        
        if ($user->role_id == 1) {
            // Admin: Can approve worklogs from users without managers
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request) {
                    $query->where('name', $request->user_name)
                          ->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                ->where('work_date', $request->work_date)
                ->get();
        } else {
            // Manager: Can approve worklogs from their subordinates
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request, $user) {
                    $query->where('name', $request->user_name)
                          ->whereHas('managers', function($q) use ($user) {
                              $q->where('manager_id', $user->id);
                          });
                })
                ->where('work_date', $request->work_date)
                ->get();
        }

        DB::transaction(function () use ($worklogs, $user, $request) {
            $worklogs->each(function($worklog) use ($user, $request) {
                $worklog->update(['status' => 'approved']);
                WorklogApproval::create([
                    'worklog_id' => $worklog->id,
                    'approved_by' => $user->id,
                    'status' => 'approved',
                    'rating' => $request->rating,
                    'remark' => $request->remark]);
            });
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been approved."
        ]);
    }

    public function rejectGroup(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'work_date' => 'required|date',
            'remark' => 'required|string|min:2|max:2000']);

        $user = $this->getCurrentUser();
        
        if ($user->role_id == 1) {
            // Admin: Can reject worklogs from users without managers
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request) {
                    $query->where('name', $request->user_name)
                          ->whereDoesntHave('managers')
                          ->where('is_worklog', 1);
                })
                ->where('work_date', $request->work_date)
                ->get();
        } else {
            // Manager: Can reject worklogs from their subordinates
            $worklogs = Worklog::where('status', 'pending')
                ->whereHas('user', function($query) use ($request, $user) {
                    $query->where('name', $request->user_name)
                          ->whereHas('managers', function($q) use ($user) {
                              $q->where('manager_id', $user->id);
                          });
                })
                ->where('work_date', $request->work_date)
                ->get();
        }

        DB::transaction(function () use ($worklogs, $user, $request) {
            $worklogs->each(function($worklog) use ($user, $request) {
                $worklog->update(['status' => 'rejected']);
                WorklogApproval::create([
                    'worklog_id' => $worklog->id,
                    'approved_by' => $user->id,
                    'status' => 'rejected',
                    'rating' => null,
                    'remark' => $request->remark]);
            });
        });

        return response()->json([
            'success' => true, 
            'message' => "All entries for {$request->user_name} on {$request->work_date} have been rejected."
        ]);
    }
}
