<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentType;
use App\Models\Shift;
use App\Models\State;
use App\Models\City;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Traits\TenantAwareStorage;

class EmployeeController extends Controller
{
    use TenantAwareStorage;

    /**
     * Get all active employees with Name and DOB
     */
    public function getActiveEmployeesBirthdayList()
    {
        $employees = Employee::where('status', 'active')
            ->whereNotNull('date_of_birth')
            ->select('id', 'name', 'date_of_birth', 'employee_code', 'profile_picture')
            ->get();

        $today = now()->startOfDay();

        $sortedEmployees = $employees->sortBy(function ($employee) use ($today) {
            $dob = \Carbon\Carbon::parse($employee->date_of_birth);
            $birthdayThisYear = $dob->copy()->year($today->year);
            if ($birthdayThisYear->lt($today)) {
                return $birthdayThisYear->addYear()->timestamp;
            }
            return $birthdayThisYear->timestamp;
        });

        $data = $sortedEmployees->values()->map(function ($employee) {
            return [
                'name' => $employee->name,
                'dob' => $employee->date_of_birth,
                'image' => $employee->profile_picture 
                            ? asset('storage/' . $employee->profile_picture) 
                            : 'https://ui-avatars.com/api/?background=random&name=' . urlencode($employee->name),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data
        ]);
    }

    /**
     * Get Master Employees List for Mobile Directory
     */
    public function index(Request $request)
    {
        $query = Employee::with([
            'branch',
            'departmentRelation',
            'designationRelation',
            'employmentTypeRelation',
            'shiftHistory.shift',
            'stateRelation',
            'cityRelation',
            'countryRelation',
            'places',
            'documents' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->withCount('documents');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $employees = $query->orderBy('name')->get();

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'inactive' => Employee::where('status', '!=', 'active')->count(),
        ];

        $employees->transform(function($emp) {
            $emp->profile_pic_url = $emp->profile_picture 
                ? asset('storage/' . $emp->profile_picture) 
                : 'https://ui-avatars.com/api/?background=random&name=' . urlencode($emp->name);
            return $emp;
        });

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'data' => $employees
        ]);
    }

    /**
     * Fetch all dynamic form master options needed for mobile pickers
     */
    public function getFormOptions()
    {
        return response()->json([
            'success' => true,
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'departments' => Department::orderBy('name')->get(['id', 'name', 'branch_id']),
            'designations' => Designation::orderBy('title')->get(['id', 'title']),
            'employmentTypes' => EmploymentType::orderBy('name')->get(['id', 'name']),
            'shifts' => Shift::orderBy('name')->get(['id', 'name']),
            'states' => State::orderBy('state_name')->get(['id', 'state_name']),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
            'roles' => DB::table('roles')->whereNotIn('role_name', ['Super Admin', 'super_admin'])->orderBy('role_name')->get(['id', 'role_name as name']),
            'places' => DB::table('places')->orderBy('placename')->get(['id', 'placename as name']),
        ]);
    }

    /**
     * Store a new employee from Mobile API
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->boolean('is_login')) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role_id' => 'required'
            ]);
        }

        $data = $this->validateEmployee($request);
        $autoGenerate = empty($data['employee_code']);
        
        if ($autoGenerate) {
            $data['employee_code'] = $this->generateTempCode('emp');
        }

        // Extract relations before creating
        $places = $request->input('places', []);

        $employee = Employee::create($data);

        if ($autoGenerate) {
            $employee->employee_code = $this->formatEmployeeCode($employee->id);
            $employee->save();
        }

        // Sync places
        if ($request->boolean('is_place_allowed') && !empty($places)) {
            $employee->places()->sync($places);
        } else {
            $employee->places()->detach();
        }

        if ($request->boolean('is_login')) {
            User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make($request->input('password')),
                'role_id' => $request->input('role_id'),
                'employee_id' => $employee->id,
                'is_login' => 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'employee' => $employee
        ]);
    }

    /**
     * Update existing employee via Mobile API
     */
    public function update(Request $request, $employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        
        if (!$request->filled('employee_code')) {
            $request->merge(['employee_code' => $employee->employee_code]);
        }

        $places = $request->input('places', []);
        
        $data = $this->validateEmployee($request, $employee->id);
        $employee->update($data);

        if (str_starts_with($employee->employee_code, 'emp-temp-')) {
            $employee->employee_code = $this->formatEmployeeCode($employee->id);
            $employee->save();
        }

        // Sync places
        if ($request->boolean('is_place_allowed')) {
            $employee->places()->sync($places);
        } else {
            $employee->places()->detach();
        }

        try {
            if (Schema::hasTable('users')) {
                $linkedUser = User::where('employee_id', $employee->id)->first();
                if ($linkedUser) {
                    app(\App\Services\LeaveBalanceService::class)->initializePrefillLeaves($linkedUser);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Mobile API Error initializing leaves upon employee update: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'employee' => $employee
        ]);
    }

    /**
     * Delete employee
     */
    public function destroy($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        
        foreach ($employee->documents as $document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();
        }

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee removed successfully.',
        ]);
    }

    /**
     * Helper to fetch cities based on State ID
     */
    public function cityOptions(Request $request): JsonResponse
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
        ]);

        $cities = City::where('state_id', $request->state_id)
            ->orderBy('city_name')
            ->get(['id', 'city_name', 'state_id']);

        return response()->json($cities);
    }

    /**
     * Core Validation Shared Logic
     */
    private function validateEmployee(Request $request, ?int $employeeId = null): array
    {
        $data = $request->validate([
            'employee_code' => 'nullable|string|max:50|unique:employees,employee_code,' . $employeeId,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employeeId,
            'personal_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'employment_type_id' => 'nullable|exists:employment_types,id',
            'shift_id' => 'nullable|exists:shifts,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'country_id' => 'nullable|exists:countries,id',
            'designation' => 'nullable|string|max:150',
            'department' => 'nullable|string|max:150',
            'employment_type' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:150',
            'date_of_joining' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:10',
            'marital_status' => 'nullable|string|max:50',
            'spouse_name' => 'nullable|string|max:255',
            'number_of_dependents' => 'nullable|integer|min:0',
            'status' => 'nullable|string|max:50',
            'work_location' => 'nullable|string|max:150',
            'address_line' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:150',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'highest_qualification' => 'nullable|string|max:150',
            'institution_name' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:150',
            'graduation_year' => 'nullable|string|max:10',
            'grade' => 'nullable|string|max:50',
            'previous_employer' => 'nullable|string|max:255',
            'previous_job_title' => 'nullable|string|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'skills' => 'nullable|string',
            'bank_name' => 'nullable|string|max:150',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'uan_number' => 'nullable|string|max:30',
            'pf_number' => 'nullable|string|max:30',
            'esi_number' => 'nullable|string|max:30',
            'insurance_provider' => 'nullable|string|max:150',
            'insurance_policy_number' => 'nullable|string|max:100',
            'insurance_valid_till' => 'nullable|date',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
            'is_place_allowed' => 'boolean',
            'is_tracking' => 'boolean',
            'working_type' => 'nullable|string|in:Office,Remote',
            'profile_picture' => 'nullable|string',
            'places' => 'nullable|array',
            'places.*' => 'exists:places,id',
        ]);

        // Handled as Base64 from API or direct storage if it has profile_picture in string
        // Web handles multi-part requests, on API we'll just take what is saved as image string
        // and store if it exists, else keep current.
        
        if (!empty($data['department_id'])) {
            $department = Department::find($data['department_id']);
            if ($department) {
                $data['branch_id'] = $department->branch_id;
                $data['department'] = $department->name;
            }
        }

        if (!empty($data['designation_id'])) {
            $designation = Designation::find($data['designation_id']);
            if ($designation) {
                $data['designation'] = $designation->title;
            }
        }

        if (!empty($data['employment_type_id'])) {
            $employmentType = EmploymentType::find($data['employment_type_id']);
            if ($employmentType) {
                $data['employment_type'] = $employmentType->name;
            }
        }

        if (!empty($data['state_id'])) {
            $state = State::find($data['state_id']);
            if ($state) {
                $data['state'] = $state->state_name;
            }
        }

        if (!empty($data['city_id'])) {
            $city = City::find($data['city_id']);
            if ($city) {
                $data['city'] = $city->city_name;
                $data['state_id'] = $city->state_id;
            }
        }

        if (!empty($data['country_id'])) {
            $country = Country::find($data['country_id']);
            if ($country) {
                $data['country'] = $country->name;
            }
        }

        return $data;
    }

    private function generateTempCode(string $prefix): string
    {
        return $prefix . '-temp-' . Str::uuid()->toString();
    }

    private function formatEmployeeCode(int $id): string
    {
        return 'Emp-' . $id;
    }
}
