<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        return view('user');
    }

   public function fetchuser(Request $request)
{
    try {
        // Check if users table exists
        if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('users')) {
            return response()->json([]);
        }

        $query = User::with(['role', 'managers']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json([]);
    }
}

public function fetchUsersForManager()
{
    try {
        // Check if users table exists
        if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('users')) {
            return response()->json([]);
        }

        $columns = ['id', 'name'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'salary_per_month')) {
            $columns[] = 'salary_per_month';
        }

        $users = User::select($columns)
                        ->orderBy('name')
                        ->get();
        return response()->json($users);
    } catch (\Exception $e) {
        return response()->json([]);
    }
}

public function fetchSalesUsers()
{
    $users = User::where('is_sales', 1)
                 ->select('id', 'name')
                 ->orderBy('name')
                 ->get();
    return response()->json($users);
}

public function fetchEmployees()
{
    try {
        // Check if employees table exists in the current database connection
        if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('employees')) {
            return response()->json([]);
        }

        $employees = \App\Models\Employee::select('id', 'name', 'employee_code')
                        ->orderBy('name')
                        ->get();
        return response()->json($employees);
    } catch (\Exception $e) {
        \Log::error('Error fetching employees: ' . $e->getMessage());
        return response()->json([]);
    }
}

public function update(Request $request, $id)
{
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'nullable|string|unique:users,username,' . $id,
            'role_id' => 'required|exists:roles,id',
            'manager_ids' => 'nullable|array',
            'is_worklog' => 'nullable',
            'employee_id' => 'nullable',
            'is_sales' => 'nullable',
            'is_task' => 'nullable',
            'is_indiaMart' => 'nullable',
            'is_calander' => 'nullable',
            'is_login' => 'nullable',
            'is_attendance' => 'nullable',
            'is_subscription' => 'nullable',
            'is_tally_calling' => 'nullable',
            'is_username_login' => 'nullable'
        ];
        
        // Only validate employee_id exists if employees table exists
        if (\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('employees') && $request->employee_id) {
            $validationRules['employee_id'] = 'nullable|exists:employees,id';
        }
        
        $request->validate($validationRules);

    // Multiple managers validation
    if ($request->has('manager_ids') && is_array($request->manager_ids)) {
        foreach ($request->manager_ids as $mId) {
            if ($mId == $id) {
                return response()->json([
                    'message' => 'A user cannot be assigned as their own manager.'
                ], 422);
            }
        }

        $validManagersCount = User::whereIn('id', $request->manager_ids)->count();
        if ($validManagersCount !== count($request->manager_ids)) {
            return response()->json([
                'message' => 'One or more selected managers do not exist.'
            ], 422);
        }
    }

    $user = User::findOrFail($id);
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'username' => $request->username,
        'role_id' => $request->role_id,
        'is_worklog' => $request->is_worklog,
        'employee_id' => $request->employee_id ?: null,
        'is_sales' => $request->is_sales,
        'is_task' => $request->is_task,
        'is_indiaMart' => $request->is_indiaMart,
        'is_calander' => $request->is_calander,
        'is_login' => $request->is_login,
        'is_attendance' => $request->is_attendance,
        'is_subscription' => $request->is_subscription,
        'is_tally_calling' => $request->is_tally_calling,
        'is_username_login' => $request->is_username_login
    ]);

    // Sync managers
    if ($request->has('manager_ids')) {
        $user->managers()->sync($request->manager_ids);
    }

    // Attempt to credit prefilled leaves if user employee assignment changed
    app(\App\Services\LeaveBalanceService::class)->initializePrefillLeaves($user);

    return response()->json(['message' => 'User updated successfully']);
}

public function store(Request $request)
{
    try {
        // Log the incoming data for debugging
        \Log::info('User creation request data:', $request->all());
        
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'manager_ids' => 'nullable|array',
            'is_worklog' => 'nullable',
            'employee_id' => 'nullable',
            'is_sales' => 'nullable',
            'is_task' => 'nullable',
            'is_indiaMart' => 'nullable',
            'is_calander' => 'nullable',
            'is_login' => 'nullable',
            'is_attendance' => 'nullable',
            'is_subscription' => 'nullable',
            'is_tally_calling' => 'nullable',
            'is_username_login' => 'nullable'
        ];
        
        // Only validate employee_id exists if employees table exists
        if (\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('employees') && $request->employee_id) {
            $validationRules['employee_id'] = 'nullable|exists:employees,id';
        }
        
        $request->validate($validationRules);

        // Multiple managers validation
        if ($request->has('manager_ids') && is_array($request->manager_ids)) {
            $validManagersCount = User::whereIn('id', $request->manager_ids)->count();
            if ($validManagersCount !== count($request->manager_ids)) {
                return response()->json([
                    'message' => 'One or more selected managers do not exist.'
                ], 422);
            }
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'is_worklog' => $request->is_worklog,
            'employee_id' => $request->employee_id ?: null,
            'is_sales' => $request->is_sales ?? 0,
            'is_task' => $request->is_task ?? 0,
            'is_indiaMart' => $request->is_indiaMart ?? 0,
            'is_calander' => $request->is_calander ?? 0,
            'is_login' => $request->is_login ?? 1,
            'is_attendance' => $request->is_attendance ?? 0,
            'is_subscription' => $request->is_subscription ?? 0,
            'is_tally_calling' => $request->is_tally_calling ?? 0,
            'is_username_login' => $request->is_username_login ?? 0
        ];

        $user = User::create($userData);

        // Sync managers
        if ($request->has('manager_ids')) {
            $user->managers()->sync($request->manager_ids);
        }

        // Formally fetch initial generation logic (prefill balance)
        app(\App\Services\LeaveBalanceService::class)->initializePrefillLeaves($user);

        return response()->json(['message' => 'User created successfully']);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error creating user: ' . $e->getMessage()
        ], 500);
    }
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}


    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}
