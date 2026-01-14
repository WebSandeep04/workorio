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

        $query = User::with(['role', 'manager']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
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
                        ->where('status', 'active')
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
            'role_id' => 'required|exists:roles,id',
            'is_manager' => 'nullable',
            'is_worklog' => 'nullable',
            'employee_id' => 'nullable',
            'is_sales' => 'nullable',
            'is_task' => 'nullable',
            'is_indiaMart' => 'nullable',
            'is_calander' => 'nullable',
            'is_login' => 'nullable'
        ];
        
        // Only validate employee_id exists if employees table exists
        if (\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('employees') && $request->employee_id) {
            $validationRules['employee_id'] = 'nullable|exists:employees,id';
        }
        
        $request->validate($validationRules);

    // Additional validation for manager - ensure manager exists and is not self
    if ($request->is_manager) {
        if ($request->is_manager == $id) {
            return response()->json([
                'message' => 'A user cannot be assigned as their own manager.'
            ], 422);
        }
        
        $managerExists = User::where('id', $request->is_manager)
            ->exists();
        
        if (!$managerExists) {
            return response()->json([
                'message' => 'Selected manager does not exist.'
            ], 422);
        }
    }

    $user = User::findOrFail($id);
    $user->update([
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'is_manager' => $request->is_manager ?: null,
        'is_worklog' => $request->is_worklog,
        'employee_id' => $request->employee_id ?: null,
        'is_sales' => $request->is_sales,
        'is_task' => $request->is_task,
        'is_indiaMart' => $request->is_indiaMart,
        'is_calander' => $request->is_calander,
        'is_login' => $request->is_login]);

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
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_manager' => 'nullable',
            'is_worklog' => 'nullable',
            'employee_id' => 'nullable',
            'is_sales' => 'nullable',
            'is_task' => 'nullable',
            'is_indiaMart' => 'nullable',
            'is_calander' => 'nullable',
            'is_login' => 'nullable'
        ];
        
        // Only validate employee_id exists if employees table exists
        if (\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('employees') && $request->employee_id) {
            $validationRules['employee_id'] = 'nullable|exists:employees,id';
        }
        
        $request->validate($validationRules);

        // Additional validation for manager - ensure manager exists
        if ($request->is_manager) {
            $managerExists = User::where('id', $request->is_manager)
                ->exists();
            
            if (!$managerExists) {
                return response()->json([
                    'message' => 'Selected manager does not exist.'
                ], 422);
            }
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'is_manager' => $request->is_manager ?: null,
            'is_worklog' => $request->is_worklog,
            'employee_id' => $request->employee_id ?: null,
            'is_sales' => $request->is_sales ?? 0,
            'is_task' => $request->is_task ?? 0,
            'is_indiaMart' => $request->is_indiaMart ?? 0,
            'is_calander' => $request->is_calander ?? 0,
            'is_login' => $request->is_login ?? 1];

        User::create($userData);

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


}
