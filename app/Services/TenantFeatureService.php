<?php

namespace App\Services;

class TenantFeatureService
{
    /**
     * Get the standardized Tenant Features and Permissions Matrix.
     * Evaluates dynamically against the provided Tenant model.
     */
    public static function getFeatures($tenant)
    {
        return [
            'core_master' => [
                'enabled' => true,
                'setup_enabled' => (bool) ($tenant->is_core_setup_enabled ?? true),
                'permissions' => [
                    'master.employees' => 'Master: Employee Management',
                    'setup.state' => 'Setup: States',
                    'setup.city' => 'Setup: Cities',
                    'setup.countries' => 'Setup: Countries',
                    'setup.branches' => 'Setup: Branches',
                    'setup.shifts' => 'Setup: Shifts',
                    'setup.departments' => 'Setup: Departments',
                    'setup.designations' => 'Setup: Designations',
                    'setup.employment_types' => 'Setup: Employment Types',
                    'setup.leave_types' => 'Setup: Leave Types',
                    'setup.late_reasons' => 'Setup: Late Reasons',
                    'setup.places' => 'Setup: Places',
                ]
            ],
            'sales_crm' => [
                'enabled' => (bool) ($tenant->is_sales_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_sales_setup_enabled ?? true),
                'permissions' => [
                    'sales.alldata' => 'Sales: View All Records',
                    'sales.myleads' => 'Sales: My Leads',
                    'sales.assignedleads' => 'Sales: Assigned Leads',
                    'sales.teamleads' => 'Sales: Team Leads',
                    'sales.followup' => 'Sales: Followup Tracker',
                    'sales.quotation' => 'Sales: Quotation Management',
                    'sales.payment_followup' => 'Sales: Payment Reminders',
                    'sales.leadform' => 'Sales: Lead Form Builder',
                    'sales.indiamart' => 'Sales: IndiaMART Leads',
                    'sales.indiamart.junk' => 'Sales: IndiaMART Junk',
                    'setup.sales_status' => 'Setup: Sales Status',
                    'setup.lead_source' => 'Setup: Lead Source',
                    'setup.products' => 'Setup: Products',
                    'setup.payment_terms' => 'Setup: Payment Terms',
                    'setup.business_types' => 'Setup: Business Types',
                    'setup.quotation' => 'Setup: Quotation Settings',
                ]
            ],
            'tele_calling' => [
                'enabled' => (bool) ($tenant->is_tally_calling_enabled ?? false),
                'setup_enabled' => (bool) ($tenant->is_tally_calling_setup_enabled ?? false),
                'permissions' => [
                    'sales.calling.all' => 'Tele-Calling: View All',
                    'sales.calling.list' => 'Tele-Calling: List View',
                    'sales.calling.my' => 'Tele-Calling: My Calls',
                    'sales.calling.team' => 'Tele-Calling: Team Calls',
                    'sales.calling.assigned' => 'Tele-Calling: Assigned Calls',
                    'sales.calling.converted' => 'Tele-Calling: Converted',
                    'sales.calling.todays' => 'Tele-Calling: Today\'s Calls',
                    'sales.calling.junk' => 'Tele-Calling: Junk Calls',
                    'sales.calling.lock' => 'Tele-Calling: Lock Calling',
                    'setup.calling_types' => 'Setup: Calling Types',
                    'setup.whatsapp_templates' => 'Setup: Whatsapp Templates',
                ]
            ],
            'lead_generation' => [
                'enabled' => (bool) ($tenant->is_leadgen_enabled ?? false),
                'setup_enabled' => (bool) ($tenant->is_leadgen_setup_enabled ?? false),
                'permissions' => [
                    'leadgen.my' => 'Lead Gen: My Created Leads'
                ]
            ],
            'worklog' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_work_setup_enabled ?? true),
                'permissions' => [
                    'worklog.entry' => 'Timesheet: Daily Entries',
                    'worklog.history' => 'Timesheet: Personal History',
                    'worklog.missing_summary' => 'Timesheet: Missing Summary',
                    'setup.customers' => 'Setup: Customers',
                    'setup.worklog_entry_types' => 'Setup: Entry Types',
                ]
            ],
            'tasks' => [
                'enabled' => (bool) ($tenant->is_task_reminders_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_task_setup_enabled ?? true),
                'permissions' => [
                    'task.view' => 'Tasks: View All',
                    'task.my_tasks' => 'Tasks: Assigned to Me',
                    'task.my_created' => 'Tasks: Created by Me',
                    'setup.task_status' => 'Setup: Task Status',
                ]
            ],
            'approvals' => [
                'enabled' => (bool) ($tenant->is_approval_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'approvals.worklog' => 'Approvals: Timesheets',
                    'approvals.attendance' => 'Approvals: Attendance',
                    'approvals.leave' => 'Approvals: Leaves',
                    'approvals.petty' => 'Approvals: Petty Cash',
                    'approvals.unlock_attendance' => 'Approvals: Unlock Attendance',
                ]
            ],
            'workflow' => [
                'enabled' => (bool) ($tenant->is_workflow_enabled ?? false),
                'setup_enabled' => (bool) ($tenant->is_workflow_setup_enabled ?? false),
                'permissions' => [
                    'workflow.critical_path' => 'Workflow: Critical Path',
                    'workflow.templates' => 'Workflow: Template Management',
                    'workflow.dependencies' => 'Workflow: Task Dependencies'
                ]
            ],
            'attendance' => [
                'enabled' => (bool) ($tenant->is_attendance_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_attendance_setup_enabled ?? false),
                'permissions' => [
                    'attendance.entry' => 'Attendance: Daily Check-in',
                    'attendance.history' => 'Attendance: Personal History',
                    'attendance.leave' => 'Attendance: Apply Leave',
                    'setup.holidays' => 'Setup: Holidays',
                ]
            ],
            'reports' => [
                'enabled' => (bool) ($tenant->is_reports_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_reports_setup_enabled ?? false),
                'permissions' => [
                    'attendance.report' => 'Reports: Attendance Report',
                    'reports.worklog' => 'Reports: Timesheet Report',
                ]
            ],
            'user_rbac' => [
                'enabled' => (bool) ($tenant->is_user_setup_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'setup.users' => 'Setup: User Management',
                    'setup.roles' => 'Setup: Role Master',
                ]
            ],
            'calendar' => [
                'enabled' => (bool) ($tenant->is_calendar_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_calendar_setup_enabled ?? true),
                'permissions' => [
                    'calendar.view' => 'Calendar: View',
                    'calendar.client_event_links' => 'Calendar: Manage Links',
                    'setup.calendar_events' => 'Setup: Calendar Events',
                    'setup.calendar_missed_reasons' => 'Setup: Missed Reasons',
                    'setup.calendar_status' => 'Setup: Status',
                    'setup.calendar_status_checklist' => 'Setup: Status Checklist',
                    'setup.calendar_common_events' => 'Setup: Common Events',
                    'setup.calendar_social_handles' => 'Setup: Social Handles',
                    'setup.calendar_clients' => 'Setup: Clients',
                    'setup.calendar_client_social' => 'Setup: Client Social',
                    'setup.checklist' => 'Setup: Checklists',
                ]
            ],
            'assets' => [
                'enabled' => (bool) ($tenant->is_asset_management_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_asset_management_setup_enabled ?? true),
                'permissions' => [
                    'asset_management.access' => 'Assets: Access',
                    'setup.asset_types' => 'Setup: Asset Types',
                    'setup.asset_categories' => 'Setup: Asset Categories',
                    'setup.asset_status' => 'Setup: Asset Status',
                    'setup.asset_suppliers' => 'Setup: Asset Suppliers',
                    'setup.open_assets' => 'Setup: Manage Assets',
                ]
            ],
            'petty_cash' => [
                'enabled' => (bool) ($tenant->is_petty_cash_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_petty_cash_setup_enabled ?? true),
                'permissions' => [
                    'petty_cash.view' => 'Petty Cash: Access',
                    'setup.expenses' => 'Setup: Expense Types',
                    'setup.petty_opening_balance' => 'Setup: Opening Balances',
                ]
            ],
            'other_modules' => [
                'enabled' => true,
                'setup_enabled' => true,
                'permissions' => [
                    'projects.view' => 'Projects: View',
                    'setup.project_services' => 'Setup: Project Services',
                    'setup.project_modules' => 'Setup: Project Modules',
                    'setup.open_projects' => 'Setup: Open Projects',
                    'subscription.view' => 'Subscriptions: View',
                    'setup.subscription_status' => 'Setup: Subscription Status',
                    'tracking.view' => 'Tracking: Live View',
                    'documents.manage' => 'Documents: Manage',
                    'documents.my_documents' => 'Documents: My Files',
                    'contact_management.access' => 'Contacts: Access',
                    'email_marketing.view' => 'Email Marketing: View',
                ]
            ]
        ];
    }
}
