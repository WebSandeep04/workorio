<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;

class AuthController extends Controller
{
    public function login(Request $request)
    {


        $data = $request->all();
        // Fallback: Manually parse JSON if body exists but inputs are empty (missing Header case)
        if (empty($data) && $content = $request->getContent()) {
            $data = json_decode($content, true) ?? [];
        }

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->findUserInTenantDatabases($data['email'], $data['password']);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => $user
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }

    /**
     * Find user in tenant databases
     */
    private function findUserInTenantDatabases($email, $password)
    {
        // Get all tenants from master database
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            try {
                // Create tenant connection if it doesn't exist
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                
                // Set tenant connection
                TenantDatabaseService::setDefaultConnection($tenant->id);
                // We use Log logic similar to AuthController if needed, but for API maybe not required to spam logs
                
                // Try to find user in this tenant database
                $user = User::where('email', $email)->first();
                $passwordOk = $user ? Hash::check($password, $user->password) : false;
                
                // Check if user has login permission (default is 1 if column exists, but treat null as true for backward compatibility if needed, though migration sets default 1)
                $isLoginAllowed = $user ? ($user->is_login ?? 1) : 0;
                
                if ($user && $passwordOk && $isLoginAllowed) {
                    // Generate API Token
                    $token = $user->createToken('API Token')->plainTextToken;
                    
                    // Load employee details with shift
                    $employee = $user->employee()->with('shiftRelation')->first();
                    
                    $shiftDetails = null;
                    if ($employee && $employee->shiftRelation) {
                        $shift = $employee->shiftRelation;
                        // Assuming times are stored as H:i:s and are in UTC (as per request)
                        // parsing them as today's time in UTC then converting to IST
                        try {
                            // Using today's date + time string
                            $startTime = \Carbon\Carbon::parse($shift->start_time, 'UTC')->setTimezone('Asia/Kolkata')->format('H:i:s');
                            $endTime = \Carbon\Carbon::parse($shift->end_time, 'UTC')->setTimezone('Asia/Kolkata')->format('H:i:s');
                        } catch (\Exception $e) {
                            $startTime = $shift->start_time; // Fallback
                            $endTime = $shift->end_time;
                        }

                        $shiftDetails = [
                            'id' => $shift->id,
                            'name' => $shift->name,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                        ];
                    }

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'tenant_id' => $tenant->id,
                        'token' => $token,
                        'employee_details' => $employee ? [
                            'date_of_birth' => $employee->date_of_birth,
                            'shift' => $shiftDetails
                        ] : null
                    ];
                }
            } catch (\Exception $e) {
                // Continue to next tenant if this one fails
                continue;
            }
        }
        
        return null;
    }
}
