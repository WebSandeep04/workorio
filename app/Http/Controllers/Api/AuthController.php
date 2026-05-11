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
                        'role_name' => $user->role ? $user->role->role_name : 'not found',
                        'is_manager' => $user->is_manager ?? 0,
                        'has_subordinates' => $user->subordinates()->exists(),
                        'permissions' => $user->rolePermissions(),
                        'tenant_id' => $tenant->id,
                        'token' => $token,
                        'version' => '1.0',
                        'employee_id' => $user->employee_id,
                        'employee_details' => $employee ? [
                            'date_of_birth' => $employee->date_of_birth,
                            'shift' => $shiftDetails
                        ] : null,
                        'feature_flags' => [
                            'is_sales_enabled' => $user->is_sales ?? 0,
                            'is_tally_calling_enabled' => $user->is_tally_calling ?? 0,
                            'is_leadgen_enabled' => $user->is_sales ?? 0,
                            'is_projects_enabled' => $user->is_projects ?? 0,
                            'is_subscription_enabled' => $user->is_subscription_and_renewal ?? 0,
                            'is_tracking_enabled' => $user->is_tracking ?? 0,
                            'is_worklog_enabled' => $user->is_worklog ?? 0,
                            'is_workflow_enabled' => $user->is_workflow ?? 0,
                            'is_social_media_calendar_enabled' => $user->is_calander ?? 0,
                            'is_setup_enabled' => $user->is_master ?? 0,
                            'is_task_reminders_enabled' => $user->is_task ?? 0,
                            'is_attendance_enabled' => $user->is_attandance ?? 0,
                            'is_reports_enabled' => $user->is_reports ?? 0,
                            'is_document_management_enabled' => $user->is_document ?? 0,
                            'is_petty_cash_enable' => $user->is_petty_cash ?? 0,
                            'is_approval_enabled' => ($user->is_petty_cash || $user->is_worklog || $user->is_attandance) ? 1 : 0,
                            'is_contact_management' => $user->is_contact_management ?? 0,
                            'is_asset_management_enable' => $user->is_asset_management ?? 0,
                            'is_email_marketing_enable' => $user->is_email_marketing ?? 0,
                            'is_core_setup_enabled' => $user->is_core_setup ?? 0,
                            'is_user_setup_enabled' => $user->is_user_setup ?? 0,
                            'is_master_setup_enabled' => $user->is_master_setup ?? 0,
                            'is_sales_setup_enabled' => $user->is_sales_setup ?? 0,
                            'is_tally_calling_setup_enabled' => $user->is_tally_calling_setup ?? 0,
                            'is_petty_cash_setup_enabled' => $user->is_petty_cash_setup ?? 0,
                            'is_projects_setup_enabled' => $user->is_projects_setup ?? 0,
                            'is_work_setup_enabled' => $user->is_work_setup ?? 0,
                            'is_attendance_setup_enabled' => $user->is_attendance_setup ?? 0,
                            'is_task_setup_enabled' => $user->is_task_setup ?? 0,
                            'is_subscription_setup_enabled' => $user->is_subscription_setup ?? 0,
                            'is_calendar_setup_enabled' => $user->is_calendar_setup ?? 0,
                            'is_asset_management_setup_enabled' => $user->is_asset_management_setup ?? 0,
                        ]
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
