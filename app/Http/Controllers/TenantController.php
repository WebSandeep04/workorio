<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index()
    {
        return view('tenant');
    }

    public function fetchTenants()
    {
        // Ensure we're using the master database for tenant operations
        DB::setDefaultConnection('mysql');
        $tenants = Tenant::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $tenants
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name',
            'is_setup_enabled' => 'nullable|boolean',
            'is_sales_enabled' => 'nullable|boolean',
            'is_worklog_enabled' => 'nullable|boolean',
            'is_attendance_enabled' => 'nullable|boolean',
            'is_subscription_enabled' => 'nullable|boolean',
            'is_document_management_enabled' => 'nullable|boolean',
            'is_sales_setup_enabled' => 'nullable|boolean',
            'is_work_setup_enabled' => 'nullable|boolean',
            'is_user_setup_enabled' => 'nullable|boolean',
            'is_petty_cash_enable' => 'nullable|boolean',
            'is_approval_enabled' => 'nullable|boolean']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ensure we're using the master database for tenant operations
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::create([
                'tenant_name' => $request->tenant_name,
                'is_setup_enabled' => $request->boolean('is_setup_enabled', true),
                'is_sales_enabled' => $request->boolean('is_sales_enabled', true),
                'is_worklog_enabled' => $request->boolean('is_worklog_enabled', true),
                'is_attendance_enabled' => $request->boolean('is_attendance_enabled', true),
                'is_subscription_enabled' => $request->boolean('is_subscription_enabled', true),
                'is_document_management_enabled' => $request->boolean('is_document_management_enabled', true),
                'is_sales_setup_enabled' => $request->boolean('is_sales_setup_enabled', true),
                'is_work_setup_enabled' => $request->boolean('is_work_setup_enabled', true),
                'is_user_setup_enabled' => $request->boolean('is_user_setup_enabled', true),
                'is_petty_cash_enable' => $request->boolean('is_petty_cash_enable', true),
                'is_approval_enabled' => $request->boolean('is_approval_enabled', true),]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tenant_name' => 'required|string|max:255|unique:tenants,tenant_name,' . $id,
            'is_setup_enabled' => 'nullable|boolean',
            'is_sales_enabled' => 'nullable|boolean',
            'is_worklog_enabled' => 'nullable|boolean',
            'is_attendance_enabled' => 'nullable|boolean',
            'is_document_management_enabled' => 'nullable|boolean',
            'is_sales_setup_enabled' => 'nullable|boolean',
            'is_work_setup_enabled' => 'nullable|boolean',
            'is_user_setup_enabled' => 'nullable|boolean',
            'is_petty_cash_enable' => 'nullable|boolean',
            'is_approval_enabled' => 'nullable|boolean']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Ensure we're using the master database for tenant operations
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'tenant_name' => $request->tenant_name,
                'is_setup_enabled' => $request->boolean('is_setup_enabled', $tenant->is_setup_enabled),
                'is_sales_enabled' => $request->boolean('is_sales_enabled', $tenant->is_sales_enabled),
                'is_worklog_enabled' => $request->boolean('is_worklog_enabled', $tenant->is_worklog_enabled),
                'is_attendance_enabled' => $request->boolean('is_attendance_enabled', $tenant->is_attendance_enabled),
                'is_subscription_enabled' => $request->boolean('is_subscription_enabled', $tenant->is_subscription_enabled ?? true),
                'is_document_management_enabled' => $request->boolean('is_document_management_enabled', $tenant->is_document_management_enabled),
                'is_petty_cash_enable' => $request->boolean('is_petty_cash_enable', $tenant->is_petty_cash_enable),
                'is_approval_enabled' => $request->boolean('is_approval_enabled', $tenant->is_approval_enabled),
                'is_sales_setup_enabled' => $request->boolean('is_sales_setup_enabled', $tenant->is_sales_setup_enabled),
                'is_work_setup_enabled' => $request->boolean('is_work_setup_enabled', $tenant->is_work_setup_enabled),
                'is_user_setup_enabled' => $request->boolean('is_user_setup_enabled', $tenant->is_user_setup_enabled)]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Ensure we're using the master database for tenant operations
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant. Please try again.'
            ], 500);
        }
    }

    public function regenerateCode($id)
    {
        try {
            // Ensure we're using the master database for tenant operations
            DB::setDefaultConnection('mysql');
            $tenant = Tenant::findOrFail($id);
            $tenant->update([
                'tenant_code' => 'TEN-' . strtoupper(\Illuminate\Support\Str::random(6))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant code regenerated successfully!',
                'data' => $tenant
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate tenant code. Please try again.'
            ], 500);
        }
    }
}
