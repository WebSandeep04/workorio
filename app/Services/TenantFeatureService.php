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
            'core' => [
                'enabled' => true,
                'setup_enabled' => (bool) ($tenant->is_core_setup_enabled ?? true),
                'permissions' => [
                    'core.setup' => 'Core Setup Management'
                ]
            ],
            'master' => [
                'enabled' => true,
                'setup_enabled' => (bool) ($tenant->is_master_setup_enabled ?? true),
                'permissions' => [
                    'master.setup' => 'Master Setup Management'
                ]
            ],
            'sales' => [
                'enabled' => (bool) ($tenant->is_sales_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_sales_setup_enabled ?? true),
                'permissions' => [
                    'sales.alldata' => 'Sales: All Data',
                    'sales.analytics' => 'Sales: Analytics',
                    'sales.leads' => 'Sales: Leads',
                    'sales.indiamart' => 'Sales: IndiaMART Leads',
                    'sales.indiamart.junk' => 'Sales: IndiaMART Junk Leads',
                    'sales.myleads' => 'Sales: My Leads',
                    'sales.teamleads' => 'Sales: Team Leads',
                    'sales.assignedleads' => 'Sales: Assigned Leads',
                    'sales.followup' => 'Sales: Follow Up',
                    'sales.quotation' => 'Sales: Quotation',
                    'sales.payment_followup' => 'Sales: Payment Followup',
                    'sales.leadform' => 'Sales: Lead Form',
                    'sales.setup' => 'Sales Setup Management'
                ]
            ],
            'tele_calling' => [
                'enabled' => (bool) ($tenant->is_tally_calling_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_tally_calling_setup_enabled ?? false),
                'permissions' => [
                    'sales.calling.all' => 'Calling: All Calls',
                    'sales.calling' => 'Calling: Campaign',
                    'sales.calling.list' => 'Calling: List',
                    'sales.calling.lock' => 'Calling: Lock Leads',
                    'sales.calling.my' => 'Calling: My Calls',
                    'sales.calling.team' => 'Calling: Team Calls',
                    'sales.calling.assigned' => 'Calling: Assigned Calls',
                    'sales.calling.todays' => "Calling: Today's Calls",
                    'sales.calling.junk' => 'Calling: Junk Calls',
                ]
            ],
            'lead_generation' => [
                'enabled' => (bool) ($tenant->is_leadgen_enabled ?? false),
                'setup_enabled' => (bool) ($tenant->is_leadgen_setup_enabled ?? false),
                'permissions' => [
                    'leadgen.my' => 'Lead Gen: My Gen Leads'
                ]
            ],
            'worklog' => [
                'enabled' => (bool) ($tenant->is_worklog_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_work_setup_enabled ?? true),
                'permissions' => [
                    'worklog.entry' => 'Worklog: Entry',
                    'worklog.history' => 'Worklog: History',
                    'worklog.leave' => 'Worklog: Leave',
                    'worklog.approvals' => 'Worklog: Approvals',
                    'worklog.missing_summary' => 'Worklog: Missing Entries Summary',
                    'task.view' => 'Task: View All Tasks',
                    'task.create' => 'Task: Create Tasks',
                    'task.edit' => 'Task: Edit Tasks',
                    'task.delete' => 'Task: Delete Tasks',
                    'task.toggle' => 'Task: Toggle Done/Pending Status',
                    'task.my_tasks' => 'Task: View My Assigned Tasks',
                    'task.my_created' => 'Task: View My Created Tasks',
                    'task.status' => 'Task: Task Status Management',
                    'worklog.setup' => 'Worklog Setup Management'
                ]
            ],
            'attendance' => [
                'enabled' => (bool) ($tenant->is_attendance_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_attendance_setup_enabled ?? false),
                'permissions' => [
                    'attendance.entry' => 'Attendance: Entry',
                    'attendance.history' => 'Attendance: History',
                    'attendance.setup' => 'Attendance Setup Management'
                ]
            ],
            'tracking' => [
                'enabled' => (bool) ($tenant->is_tracking_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_tracking_setup_enabled ?? false),
                'permissions' => [
                    'tracking.view' => 'Tracking: View Tracking',
                    'tracking.setup' => 'Tracking Setup Management'
                ]
            ],
            'subscription' => [
                'enabled' => (bool) ($tenant->is_subscription_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_subscription_setup_enabled ?? false),
                'permissions' => [
                    'subscription.view' => 'Subscription: View Subscriptions',
                    'subscription.create' => 'Subscription: Create Subscriptions',
                    'subscription.edit' => 'Subscription: Edit Subscriptions',
                    'subscription.delete' => 'Subscription: Delete Subscriptions',
                    'subscription.setup' => 'Subscription Setup Management'
                ]
            ],
            'projects' => [
                'enabled' => (bool) ($tenant->is_projects_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_projects_setup_enabled ?? false),
                'permissions' => [
                    'projects.view' => 'Projects: View Projects',
                    'projects.create' => 'Projects: Create Projects',
                    'projects.edit' => 'Projects: Edit Projects',
                    'projects.delete' => 'Projects: Delete Projects',
                    'projects.setup' => 'Projects Setup Management'
                ]
            ],
            'documents' => [
                'enabled' => (bool) ($tenant->is_document_management_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_document_setup_enabled ?? false),
                'permissions' => [
                    'documents.manage' => 'Manage Documents',
                    'documents.my_documents' => 'My Documents',
                    'documents.setup' => 'Documents Setup Management'
                ]
            ],
            'user_management' => [
                'enabled' => (bool) ($tenant->is_user_setup_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'user.view' => 'View Users',
                    'user.create' => 'Create Users',
                    'user.edit' => 'Edit Users',
                    'user.delete' => 'Delete Users',
                    'role.manage' => 'Manage Roles'
                ]
            ],
            'reports' => [
                'enabled' => (bool) ($tenant->is_reports_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_reports_setup_enabled ?? false),
                'permissions' => [
                    'attendance.stats' => 'Attendance Stats',
                    'attendance.report' => 'Attendance Report',
                    'worklog.history' => 'Worklog Report',
                    'reports.setup' => 'Reports Setup Management'
                ]
            ],
            'calendar' => [
                'enabled' => (bool) ($tenant->is_social_media_calendar_enabled ?? true),
                'setup_enabled' => (bool) ($tenant->is_calendar_setup_enabled ?? false),
                'permissions' => [
                    'calendar.view' => 'Calendar: View Calendar',
                    'calendar.client_event_links' => 'Calendar: Client-Event Links',
                    'calendar.events' => 'Calendar: Manage Calendar Events',
                    'calendar.status' => 'Calendar: Manage Calendar Status',
                    'calendar.status_checklist' => 'Calendar: Status-Checklist Management',
                    'calendar.common_events' => 'Calendar: Manage Common Events',
                    'calendar.social_handles' => 'Calendar: Manage Social Handles',
                    'calendar.clients' => 'Calendar: Manage Calendar Clients',
                    'calendar.client_social' => 'Calendar: Client Social Handles Management',
                    'calendar.setup' => 'Calendar Setup Management'
                ]
            ],
            'petty_cash' => [
                'enabled' => (bool) ($tenant->is_petty_cash_enable ?? true),
                'setup_enabled' => (bool) ($tenant->is_petty_cash_setup_enabled ?? false),
                'permissions' => [
                    'petty_cash.view' => 'Petty Cash: View Cash',
                    'petty_cash.setup' => 'Petty Cash Setup Management'
                ]
            ],
            'approvals' => [
                'enabled' => (bool) ($tenant->is_approval_enabled ?? true),
                'setup_enabled' => false,
                'permissions' => [
                    'approvals.petty' => 'Approvals: Petty Approval'
                ]
            ],
            'contact_management' => [
                'enabled' => (bool) ($tenant->is_contact_management ?? true),
                'setup_enabled' => (bool) ($tenant->is_contact_management_setup_enabled ?? false),
                'permissions' => [
                    'contact_management.access' => 'Contact Management: Access',
                    'contact_management.setup' => 'Contact Management Setup Management'
                ]
            ],
            'asset_management' => [
                'enabled' => (bool) ($tenant->is_asset_management_enable ?? true),
                'setup_enabled' => (bool) ($tenant->is_asset_management_setup_enabled ?? false),
                'permissions' => [
                    'asset_management.access' => 'Asset Management: Access',
                    'asset_management.setup' => 'Asset Management Setup Management'
                ]
            ],
            'email_marketing' => [
                'enabled' => (bool) ($tenant->is_email_marketing_enable ?? false),
                'setup_enabled' => (bool) ($tenant->is_email_marketing_setup_enabled ?? false),
                'permissions' => [
                    'email_marketing.view' => 'Email Marketing: Access',
                    'email_marketing.setup' => 'Email Marketing Setup Management'
                ]
            ]
        ];
    }
}
