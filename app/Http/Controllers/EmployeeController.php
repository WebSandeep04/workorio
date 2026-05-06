<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentType;
use App\Models\Shift;
use App\Models\State;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\TenantAwareStorage;

class EmployeeController extends Controller
{
    use TenantAwareStorage;
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $designations = Designation::orderBy('title')->get();
        $employmentTypes = EmploymentType::orderBy('name')->get();
        $shifts = Shift::orderBy('name')->get();
        $states = State::orderBy('state_name')->get();
        $countries = Country::orderBy('name')->get();
        return view('employees.index', compact('branches', 'departments', 'designations', 'employmentTypes', 'shifts', 'states', 'countries'));
    }

    public function list(): JsonResponse
    {
        $employees = Employee::with([
                'branch',
                'departmentRelation',
                'designationRelation',
                'employmentTypeRelation',
                'shiftRelation',
                'stateRelation',
                'cityRelation',
                'countryRelation',
                'places',
                'documents' => function($query) {
                    $query->whereIn('document_type', ['Aadhaar', 'PAN', 'Education'])
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->withCount('documents')
            ->orderBy('name')
            ->get();

        return response()->json($employees);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->boolean('is_login')) {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role_id' => 'required|exists:roles,id'
            ]);
        }

        $data = $this->validateEmployee($request);
        $autoGenerate = empty($data['employee_code']);
        
        if ($autoGenerate) {
            $data['employee_code'] = $this->generateTempCode('emp');
        }

        $employee = Employee::create($data);

        if ($autoGenerate) {
            $employee->employee_code = $this->formatEmployeeCode($employee->id);
            $employee->save();
        }
        if (isset($data['is_place_allowed']) && $data['is_place_allowed']) {
            if (isset($data['places'])) {
                $employee->places()->sync($data['places']);
            }
        } else {
            $employee->places()->detach();
        }

        if ($request->boolean('is_login')) {
            \App\Models\User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                'role_id' => $request->input('role_id'),
                'employee_id' => $employee->id,
                'is_login' => 1,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully.',
            'employee' => $employee->fresh()
                ->load(['branch', 'departmentRelation', 'designationRelation', 'employmentTypeRelation', 'stateRelation', 'cityRelation', 'countryRelation', 'places', 'documents' => function($query) {
                    $query->whereIn('document_type', ['Aadhaar', 'PAN', 'Education'])
                          ->orderBy('created_at', 'desc');
                }])
                ->loadCount('documents'),
        ]);
    }

    public function update(Request $request, $employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        
        // If employee_code is not provided or empty in request, keep the current one
        if (!$request->filled('employee_code')) {
            $request->merge(['employee_code' => $employee->employee_code]);
        }

        $data = $this->validateEmployee($request, $employee->id);
        $employee->update($data);

        // Fix existing temp codes to the formal 'Emp-{id}' format
        if (str_starts_with($employee->employee_code, 'emp-temp-')) {
            $employee->employee_code = $this->formatEmployeeCode($employee->id);
            $employee->save();
        }

        if (isset($data['is_place_allowed']) && $data['is_place_allowed']) {
            if (isset($data['places'])) {
                $employee->places()->sync($data['places']);
            }
        } else {
             // If not restricted, remove all associated places
             $employee->places()->detach();
        }

        // If the admin changes the employee's Employment Type later, automatically provision the mapped leaves.
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $linkedUser = \App\Models\User::where('employee_id', $employee->id)->first();
                if ($linkedUser) {
                    app(\App\Services\LeaveBalanceService::class)->initializePrefillLeaves($linkedUser);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error initializing leaves upon employee update: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
            'employee' => $employee->fresh()
                ->load(['branch', 'departmentRelation', 'designationRelation', 'employmentTypeRelation', 'stateRelation', 'cityRelation', 'countryRelation', 'places', 'documents' => function($query) {
                    $query->whereIn('document_type', ['Aadhaar', 'PAN', 'Education'])
                          ->orderBy('created_at', 'desc');
                }])
                ->loadCount('documents'),
        ]);
    }

    public function show($employeeId): JsonResponse
    {
        $employee = Employee::with([
                'branch',
                'departmentRelation',
                'designationRelation',
                'employmentTypeRelation',
                'stateRelation',
                'cityRelation',
                'countryRelation',
                'places',
                'documents' => function($query) {
                    $query->whereIn('document_type', ['Aadhaar', 'PAN', 'Education'])
                          ->orderBy('created_at', 'desc');
                }
            ])
            ->withCount('documents')
            ->findOrFail($employeeId);

        return response()->json($employee);
    }

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

    public function documents($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        
        $documents = $employee->documents()
            ->orderByDesc('created_at')
            ->get();

        return response()->json($documents);
    }

    public function storeDocument(Request $request, $employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        
        $validated = $request->validate([
            'document_type' => 'nullable|string|max:150',
            'document_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'issued_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:issued_at',
            'file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        // Use tenant-aware storage with isolation
        $path = $this->storeTenantFile($request->file('file'), 'employee-documents/' . $employee->id);

        $document = $employee->documents()->create([
            'document_type' => $validated['document_type'] ?? null,
            'document_name' => $validated['document_name'],
            'issued_at' => $validated['issued_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'file_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document uploaded successfully.',
            'document' => $document,
        ]);
    }

    public function destroyDocument($employeeId, $documentId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $document = EmployeeDocument::findOrFail($documentId);
        
        if ($document->employee_id !== $employee->id) {
            abort(404);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

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
            'places' => 'array',
            'places.*' => 'exists:places,id',
            'working_type' => 'nullable|string|in:Office,Remote',
            'profile_picture' => 'nullable|image',
        ]);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;

            if ($employeeId) {
                $employee = Employee::find($employeeId);
                if ($employee && $employee->profile_picture) {
                    Storage::disk('public')->delete($employee->profile_picture);
                }
            }
        }

        if (!empty($data['department_id'])) {
            $department = Department::find($data['department_id']);
            if ($department) {
                if (!empty($data['branch_id']) && $department->branch_id !== (int) $data['branch_id']) {
                    abort(response()->json([
                        'message' => 'Department does not belong to selected branch.',
                    ], 422));
                }
                $data['branch_id'] = $department->branch_id;
                $data['department'] = $data['department'] ?: $department->name;
            }
        }

        if (!empty($data['designation_id'])) {
            $designation = Designation::find($data['designation_id']);
            if ($designation) {
                $data['designation'] = $data['designation'] ?: $designation->title;
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
                if (!empty($data['state_id']) && $city->state_id !== (int) $data['state_id']) {
                    abort(response()->json([
                        'message' => 'City does not belong to selected state.',
                    ], 422));
                }
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


    private function generateTempCode(string $prefix): string
    {
        return $prefix . '-temp-' . Str::uuid()->toString();
    }

    private function formatEmployeeCode(int $id): string
    {
        return 'Emp-' . $id;
    }
}

