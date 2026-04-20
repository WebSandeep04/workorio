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
                    'core.setup' => 'Core: Setup (States, Cities, Countries)',
                    'master.employees' => 'Master: Employee Management',
                    'master.setup' => 'Master: Setup (Branches, Shifts, Depts)'
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
                    'sales.setup' => 'Sales: Setup Management'
                ]
            ],
            'tele_calling' => [
                'enabled' => (bool) ($tenant->is_tally_calling_enabled ?? false),
                'setup_enabled' => (bool) ($tenant->is_tally_calling_setup_enabled ?? false),
                'permissions' => [
                    'sales.calling.all' => 'Tele-Calling: View All',
                    'sales.calling.list' => 'Tele-Calling: List View',
                    'sales.calling.todayfollowups' => 'Tele-Calling: Today Followups',
                    'sales.calling.todaynew' => 'Tele-Calling: Today New',
                    'sales.calling.todaycompleted' => 'Tele-Calling: Today Completed',
                    'sales.calling.todaypending' => 'Tele-Calling: Today Pending',
                    'sales.calling.underprocess' => 'Tele-Calling: Under Process',
                    'sales.calling.allleadstable' => 'Tele-Calling: Leads Table'
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
                    'worklog.entry' => 'Worklog: Daily Entries',
                    'worklog.leave' => 'Worklog: Leave Requests',
                    'worklog.missing_summary' => 'Worklog: Missing Summary',
                    'worklog.setup' => 'Worklog: Setup Management'
                ]
            ],
            'tasks' => [
                'enabled' => (bool) ($tenant->is_task_reminders_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_task_setup_enabled ?? true),
                'permissions' => [
                    'task.view' => 'Tasks: View All',
                    'task.create' => 'Tasks: Create & Assign',
                    'task.edit' => 'Tasks: Edit Details',
                    'task.delete' => 'Tasks: Delete',
                    'task.toggle' => 'Tasks: Update Status',
                    'task.my_tasks' => 'Tasks: Assigned to Me',
                    'task.my_created' => 'Tasks: Created by Me',
                    'task.setup' => 'Tasks: Status Setup'
                ]
            ],
            'approvals' => [
                'enabled' => (bool) ($tenant->is_approval_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'worklog.approvals' => 'Approvals: Timesheets & Leaves',
                    'attendance.approval' => 'Approvals: Attendance Records',
                    'approvals.petty' => 'Approvals: Petty Cash Claims'
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
                    'attendance.setup' => 'Attendance: Setup Management'
                ]
            ],
            'reports' => [
                'enabled' => (bool) ($tenant->is_reports_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_reports_setup_enabled ?? false),
                'permissions' => [
                    'attendance.stats' => 'Reports: Attendance Stats',
                    'attendance.report' => 'Reports: Attendance History',
                    'reports.worklog' => 'Reports: Timesheet Summary',
                    'worklog.history' => 'Reports: Detailed History',
                    'reports.setup' => 'Reports: Setup Management'
                ]
            ],
            'user_rbac' => [
                'enabled' => (bool) ($tenant->is_user_setup_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'user.view' => 'Users: View & Manage',
                    'role.manage' => 'Users: Roles & Permissions'
                ]
            ],
            'other_modules' => [
                'enabled' => true,
                'setup_enabled' => true,
                'permissions' => [
                    'projects.view' => 'Others: Projects View',
                    'subscription.view' => 'Others: Subscriptions View',
                    'tracking.view' => 'Others: Live Tracking View',
                    'documents.manage' => 'Others: Document Management',
                    'calendar.view' => 'Others: Social Calendar View',
                    'petty_cash.view' => 'Others: Petty Cash Access',
                    'contact_management.access' => 'Others: Contacts Access',
                    'asset_management.access' => 'Others: Assets Access'
                ]
            ]
        ];
    }
}
