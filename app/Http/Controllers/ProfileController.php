<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Traits\TenantAwareStorage;

class ProfileController extends Controller
{
    use TenantAwareStorage;
    /**
     * Show the profile edit page.
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
             return redirect()->route('login');
        }

        $employee = $user->employee;
        
        if (!$employee) {
            $employee = Employee::where('email', $user->email)->first();
        }

        if (!$employee) {
            return redirect()->back()->with('error', 'No employee record associated with this user.');
        }

        // Load documents
        $employee->load(['documents' => function($query) {
            $query->whereIn('document_type', ['Aadhaar', 'PAN', 'Education'])
                  ->orderBy('created_at', 'desc');
        }]);

        return view('profile.index', compact('employee'));
    }

    public function storeDocument(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);

        $employee = $user->employee;
        if (!$employee) {
            $employee = Employee::where('email', $user->email)->first();
        }
        if (!$employee) return response()->json(['success' => false, 'message' => 'Employee profile not found.'], 404);

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
            'document' => $document
        ]);
    }

    public function destroyDocument($documentId)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);

        $employee = $user->employee;
        if (!$employee) {
            $employee = Employee::where('email', $user->email)->first();
        }
        if (!$employee) return response()->json(['success' => false, 'message' => 'Employee profile not found.'], 404);

        $document = EmployeeDocument::findOrFail($documentId);
        
        if ($document->employee_id !== $employee->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->file_path) {
            $this->deleteTenantFile($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

    /**
     * Update the employee profile via AJAX.
     */
    public function update(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
             return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $employee = $user->employee;
        
        if (!$employee) {
            $employee = Employee::where('email', $user->email)->first();
        }

        if (!$employee) {
             return response()->json(['success' => false, 'message' => 'Employee profile not found.'], 404);
        }

        // define validation rules based on migration columns you want to allow editing
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'address_line' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            
            // Extended Personal Fields
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string|max:10',
            'marital_status' => 'nullable|string|max:50',
            'personal_email' => 'nullable|email|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'number_of_dependents' => 'nullable|integer|min:0',
            'emergency_contact_relation' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry' => 'nullable|date',
            'aadhaar_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'highest_qualification' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|string|max:4',
            'grade' => 'nullable|string|max:50',
            'previous_employer' => 'nullable|string|max:255',
            'previous_job_title' => 'nullable|string|max:255',
            'experience_years' => 'nullable|numeric|min:0',
            'skills' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'uan_number' => 'nullable|string|max:50',
            'pf_number' => 'nullable|string|max:50',
            'esi_number' => 'nullable|string|max:50',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:50',
            'insurance_valid_till' => 'nullable|date',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'profile_picture' => 'nullable|image',
        ];

        $validated = $request->validate($rules);

        try {
            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                // Use tenant-aware storage with isolation
                $path = $this->storeTenantFile($image, 'employee-profiles');
                
                // Delete old profile picture if it exists
                if ($employee->profile_picture) {
                    $this->deleteTenantFile($employee->profile_picture);
                }
                
                $validated['profile_picture'] = $path;
            }

            $employee->update($validated);
            
            return response()->json([
                'success' => true, 
                'message' => 'Profile updated successfully.',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Serve the profile picture.
     */
    /**
     * Serve the profile picture.
     */
    public function getProfilePicture($id)
    {
        $employee = Employee::findOrFail($id);
        
        if (!$employee->profile_picture) {
            abort(404);
        }

        $path = Storage::disk('public')->path($employee->profile_picture);
        
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    private function getAuthenticatedUser()
    {
        if (Auth::check()) {
            return Auth::user();
        }

        if (session()->has('user_id')) {
            return \App\Models\User::find(session('user_id'));
        }

        return null;
    }
}
