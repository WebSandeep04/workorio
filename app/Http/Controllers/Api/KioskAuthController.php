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
use App\Services\MenuBuilder;

class KioskAuthController extends Controller
{
    public function kioskLogin(Request $request)
    {
        $data = $request->all();
        // Fallback: Manually parse JSON if body exists but inputs are empty
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

        // Search through all tenant databases
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            try {
                if (!TenantDatabaseService::connectionExists($tenant->id)) {
                    TenantDatabaseService::createConnection($tenant);
                }
                TenantDatabaseService::setDefaultConnection($tenant->id);

                $user = User::where('email', $data['email'])->first();
                $passwordOk = $user ? Hash::check($data['password'], $user->password) : false;
                $isLoginAllowed = $user ? ($user->is_login ?? 1) : 0;

                if ($user && $passwordOk && $isLoginAllowed) {
                    $token = $user->createToken('API Token')->plainTextToken;

                    $employee = $user->employee()->with('shiftHistory.shift')->first();
                    $shiftDetails = null;
                    if ($employee && ($shift = $employee->getShiftForDate(today()))) {
                        // $shift is assigned in the if condition above
                        try {
                            $startTime = $shift->start_time ? \Carbon\Carbon::parse($shift->start_time)->format('H:i:s') : null;
                            $endTime = $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('H:i:s') : null;
                        } catch (\Exception $e) {
                            $startTime = $shift->start_time;
                            $endTime = $shift->end_time;
                        }

                        $shiftDetails = [
                            'id' => $shift->id,
                            'name' => $shift->name,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                        ];
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Kiosk logged in successfully to tenant ' . $tenant->tenant_name,
                        'data' => [
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
                                'is_sales_enabled' => $tenant->is_sales_enabled ?? 0,
                                'is_tally_calling_enabled' => $tenant->is_tally_calling_enabled ?? 0,
                                'is_leadgen_enabled' => $tenant->is_leadgen_enabled ?? 0,
                                'is_projects_enabled' => $tenant->is_projects_enabled ?? 0,
                                'is_subscription_enabled' => $tenant->is_subscription_enabled ?? 0,
                                'is_tracking_enabled' => $tenant->is_tracking_enabled ?? 0,
                                'is_worklog_enabled' => $tenant->is_worklog_enabled ?? 0,
                                'is_workflow_enabled' => $tenant->is_workflow_enabled ?? 0,
                                'is_social_media_calendar_enabled' => $tenant->is_social_media_calendar_enabled ?? 0,
                                'is_setup_enabled' => $tenant->is_setup_enabled ?? 0,
                                'is_task_reminders_enabled' => $tenant->is_task_reminders_enabled ?? 0,
                                'is_attendance_enabled' => $tenant->is_attendance_enabled ?? 0,
                                'is_reports_enabled' => $tenant->is_reports_enabled ?? 0,
                                'is_document_management_enabled' => $tenant->is_document_management_enabled ?? 0,
                                'is_petty_cash_enable' => $tenant->is_petty_cash_enable ?? 0,
                                'is_approval_enabled' => $tenant->is_approval_enabled ?? 0,
                                'is_contact_management' => $tenant->is_contact_management ?? 0,
                                'is_asset_management_enable' => $tenant->is_asset_management_enable ?? 0,
                                'is_email_marketing_enable' => $tenant->is_email_marketing_enable ?? 0,
                                'is_core_setup_enabled' => $tenant->is_core_setup_enabled ?? 0,
                                'is_user_setup_enabled' => $tenant->is_user_setup_enabled ?? 0,
                                'is_master_setup_enabled' => $tenant->is_master_setup_enabled ?? 0,
                                'is_sales_setup_enabled' => $tenant->is_sales_setup_enabled ?? 0,
                                'is_tally_calling_setup_enabled' => $tenant->is_tally_calling_setup_enabled ?? 0,
                                'is_petty_cash_setup_enabled' => $tenant->is_petty_cash_setup_enabled ?? 0,
                                'is_projects_setup_enabled' => $tenant->is_projects_setup_enabled ?? 0,
                                'is_work_setup_enabled' => $tenant->is_work_setup_enabled ?? 0,
                                'is_attendance_setup_enabled' => $tenant->is_attendance_setup_enabled ?? 0,
                                'is_task_setup_enabled' => $tenant->is_task_setup_enabled ?? 0,
                                'is_subscription_setup_enabled' => $tenant->is_subscription_setup_enabled ?? 0,
                                'is_calendar_setup_enabled' => $tenant->is_calendar_setup_enabled ?? 0,
                                'is_asset_management_setup_enabled' => $tenant->is_asset_management_setup_enabled ?? 0,
                            ],
                            'menus' => MenuBuilder::build($user)
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                // Continue to the next tenant if there is a database error on this one
                continue;
            }
        }

        // If loop completes without returning, credentials didn't match any tenant
        return response()->json([
            'success' => false,
            'message' => 'The provided credentials do not match our records.'
        ], 401);
    }
}
