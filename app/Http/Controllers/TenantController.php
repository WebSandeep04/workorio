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
            'is_approval_enabled' => 'nullable|boolean',
            'is_contact_management' => 'nullable|boolean',
            'is_asset_management_enable' => 'nullable|boolean',
            'is_email_marketing_enable' => 'nullable|boolean',
            'is_tally_calling_enabled' => 'nullable|boolean',
            'is_leadgen_enabled' => 'nullable|boolean',
            'is_projects_enabled' => 'nullable|boolean',
            'is_tracking_enabled' => 'nullable|boolean',
            'is_workflow_enabled' => 'nullable|boolean',
            'is_social_media_calendar_enabled' => 'nullable|boolean',
            'is_master_enabled' => 'nullable|boolean',
            'is_task_reminders_enabled' => 'nullable|boolean',
            'is_reports_enabled' => 'nullable|boolean',
            'is_core_setup_enabled' => 'nullable|boolean',
            'is_tally_calling_setup_enabled' => 'nullable|boolean',
            'is_leadgen_setup_enabled' => 'nullable|boolean',
            'is_projects_setup_enabled' => 'nullable|boolean',
            'is_subscription_setup_enabled' => 'nullable|boolean',
            'is_tracking_setup_enabled' => 'nullable|boolean',
            'is_workflow_setup_enabled' => 'nullable|boolean',
            'is_calendar_setup_enabled' => 'nullable|boolean',
            'is_master_setup_enabled' => 'nullable|boolean',
            'is_task_setup_enabled' => 'nullable|boolean',
            'is_attendance_setup_enabled' => 'nullable|boolean',
            'is_reports_setup_enabled' => 'nullable|boolean',
            'is_document_setup_enabled' => 'nullable|boolean',
            'is_petty_cash_setup_enabled' => 'nullable|boolean',
            'is_contact_management_setup_enabled' => 'nullable|boolean',
            'is_asset_management_setup_enabled' => 'nullable|boolean',
            'is_email_marketing_setup_enabled' => 'nullable|boolean',
        ]);

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
                'is_approval_enabled' => $request->boolean('is_approval_enabled', true),
                'is_contact_management' => $request->boolean('is_contact_management', true),
                'is_asset_management_enable' => $request->boolean('is_asset_management_enable', true),
                'is_email_marketing_enable' => $request->boolean('is_email_marketing_enable', true),
                'is_tally_calling_enabled' => $request->boolean('is_tally_calling_enabled', true),
                'is_leadgen_enabled' => $request->boolean('is_leadgen_enabled', true),
                'is_projects_enabled' => $request->boolean('is_projects_enabled', true),
                'is_tracking_enabled' => $request->boolean('is_tracking_enabled', true),
                'is_workflow_enabled' => $request->boolean('is_workflow_enabled', true),
                'is_social_media_calendar_enabled' => $request->boolean('is_social_media_calendar_enabled', true),
                'is_master_enabled' => $request->boolean('is_master_enabled', true),
                'is_task_reminders_enabled' => $request->boolean('is_task_reminders_enabled', true),
                'is_reports_enabled' => $request->boolean('is_reports_enabled', true),
                'is_core_setup_enabled' => $request->boolean('is_core_setup_enabled', true),
                'is_tally_calling_setup_enabled' => $request->boolean('is_tally_calling_setup_enabled', true),
                'is_leadgen_setup_enabled' => $request->boolean('is_leadgen_setup_enabled', true),
                'is_projects_setup_enabled' => $request->boolean('is_projects_setup_enabled', true),
                'is_subscription_setup_enabled' => $request->boolean('is_subscription_setup_enabled', true),
                'is_tracking_setup_enabled' => $request->boolean('is_tracking_setup_enabled', true),
                'is_workflow_setup_enabled' => $request->boolean('is_workflow_setup_enabled', true),
                'is_calendar_setup_enabled' => $request->boolean('is_calendar_setup_enabled', true),
                'is_master_setup_enabled' => $request->boolean('is_master_setup_enabled', true),
                'is_task_setup_enabled' => $request->boolean('is_task_setup_enabled', true),
                'is_attendance_setup_enabled' => $request->boolean('is_attendance_setup_enabled', true),
                'is_reports_setup_enabled' => $request->boolean('is_reports_setup_enabled', true),
                'is_document_setup_enabled' => $request->boolean('is_document_setup_enabled', true),
                'is_petty_cash_setup_enabled' => $request->boolean('is_petty_cash_setup_enabled', true),
                'is_contact_management_setup_enabled' => $request->boolean('is_contact_management_setup_enabled', true),
                'is_asset_management_setup_enabled' => $request->boolean('is_asset_management_setup_enabled', true),
                'is_email_marketing_setup_enabled' => $request->boolean('is_email_marketing_setup_enabled', true),
            ]);

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
            'is_approval_enabled' => 'nullable|boolean',
            'is_contact_management' => 'nullable|boolean',
            'is_asset_management_enable' => 'nullable|boolean',
            'is_email_marketing_enable' => 'nullable|boolean',
            'is_tally_calling_enabled' => 'nullable|boolean',
            'is_leadgen_enabled' => 'nullable|boolean',
            'is_projects_enabled' => 'nullable|boolean',
            'is_tracking_enabled' => 'nullable|boolean',
            'is_workflow_enabled' => 'nullable|boolean',
            'is_social_media_calendar_enabled' => 'nullable|boolean',
            'is_master_enabled' => 'nullable|boolean',
            'is_task_reminders_enabled' => 'nullable|boolean',
            'is_reports_enabled' => 'nullable|boolean',
            'is_core_setup_enabled' => 'nullable|boolean',
            'is_tally_calling_setup_enabled' => 'nullable|boolean',
            'is_leadgen_setup_enabled' => 'nullable|boolean',
            'is_projects_setup_enabled' => 'nullable|boolean',
            'is_subscription_setup_enabled' => 'nullable|boolean',
            'is_tracking_setup_enabled' => 'nullable|boolean',
            'is_workflow_setup_enabled' => 'nullable|boolean',
            'is_calendar_setup_enabled' => 'nullable|boolean',
            'is_master_setup_enabled' => 'nullable|boolean',
            'is_task_setup_enabled' => 'nullable|boolean',
            'is_attendance_setup_enabled' => 'nullable|boolean',
            'is_reports_setup_enabled' => 'nullable|boolean',
            'is_document_setup_enabled' => 'nullable|boolean',
            'is_petty_cash_setup_enabled' => 'nullable|boolean',
            'is_contact_management_setup_enabled' => 'nullable|boolean',
            'is_asset_management_setup_enabled' => 'nullable|boolean',
            'is_email_marketing_setup_enabled' => 'nullable|boolean',
        ]);

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
                'is_contact_management' => $request->boolean('is_contact_management', $tenant->is_contact_management),
                'is_asset_management_enable' => $request->boolean('is_asset_management_enable', $tenant->is_asset_management_enable),
                'is_email_marketing_enable' => $request->boolean('is_email_marketing_enable', $tenant->is_email_marketing_enable),
                'is_sales_setup_enabled' => $request->boolean('is_sales_setup_enabled', $tenant->is_sales_setup_enabled),
                'is_work_setup_enabled' => $request->boolean('is_work_setup_enabled', $tenant->is_work_setup_enabled),
                'is_user_setup_enabled' => $request->boolean('is_user_setup_enabled', $tenant->is_user_setup_enabled),
                'is_tally_calling_enabled' => $request->boolean('is_tally_calling_enabled', $tenant->is_tally_calling_enabled),
                'is_leadgen_enabled' => $request->boolean('is_leadgen_enabled', $tenant->is_leadgen_enabled ?? true),
                'is_projects_enabled' => $request->boolean('is_projects_enabled', $tenant->is_projects_enabled),
                'is_tracking_enabled' => $request->boolean('is_tracking_enabled', $tenant->is_tracking_enabled),
                'is_workflow_enabled' => $request->boolean('is_workflow_enabled', $tenant->is_workflow_enabled),
                'is_social_media_calendar_enabled' => $request->boolean('is_social_media_calendar_enabled', $tenant->is_social_media_calendar_enabled),
                'is_master_enabled' => $request->boolean('is_master_enabled', $tenant->is_master_enabled),
                'is_task_reminders_enabled' => $request->boolean('is_task_reminders_enabled', $tenant->is_task_reminders_enabled),
                'is_reports_enabled' => $request->boolean('is_reports_enabled', $tenant->is_reports_enabled),
                'is_core_setup_enabled' => $request->boolean('is_core_setup_enabled', $tenant->is_core_setup_enabled),
                'is_tally_calling_setup_enabled' => $request->boolean('is_tally_calling_setup_enabled', $tenant->is_tally_calling_setup_enabled),
                'is_leadgen_setup_enabled' => $request->boolean('is_leadgen_setup_enabled', $tenant->is_leadgen_setup_enabled ?? true),
                'is_projects_setup_enabled' => $request->boolean('is_projects_setup_enabled', $tenant->is_projects_setup_enabled),
                'is_subscription_setup_enabled' => $request->boolean('is_subscription_setup_enabled', $tenant->is_subscription_setup_enabled),
                'is_tracking_setup_enabled' => $request->boolean('is_tracking_setup_enabled', $tenant->is_tracking_setup_enabled),
                'is_workflow_setup_enabled' => $request->boolean('is_workflow_setup_enabled', $tenant->is_workflow_setup_enabled),
                'is_calendar_setup_enabled' => $request->boolean('is_calendar_setup_enabled', $tenant->is_calendar_setup_enabled),
                'is_master_setup_enabled' => $request->boolean('is_master_setup_enabled', $tenant->is_master_setup_enabled),
                'is_task_setup_enabled' => $request->boolean('is_task_setup_enabled', $tenant->is_task_setup_enabled),
                'is_attendance_setup_enabled' => $request->boolean('is_attendance_setup_enabled', $tenant->is_attendance_setup_enabled),
                'is_reports_setup_enabled' => $request->boolean('is_reports_setup_enabled', $tenant->is_reports_setup_enabled),
                'is_document_setup_enabled' => $request->boolean('is_document_setup_enabled', $tenant->is_document_setup_enabled),
                'is_petty_cash_setup_enabled' => $request->boolean('is_petty_cash_setup_enabled', $tenant->is_petty_cash_setup_enabled),
                'is_contact_management_setup_enabled' => $request->boolean('is_contact_management_setup_enabled', $tenant->is_contact_management_setup_enabled),
                'is_asset_management_setup_enabled' => $request->boolean('is_asset_management_setup_enabled', $tenant->is_asset_management_setup_enabled),
                'is_email_marketing_setup_enabled' => $request->boolean('is_email_marketing_setup_enabled', $tenant->is_email_marketing_setup_enabled),
            ]);

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
