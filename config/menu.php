<?php

return [
    // Admin full sections
    'admin_sections' => [
        // Admin gets full sales access when sales is enabled
        [
            'key' => 'admin_sales_operational',
            'title' => 'Sales & CRM',
            'icon' => 'bi bi-cart',
            'feature_flag' => 'is_sales_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'alldata', 'title' => 'All Data', 'icon' => 'bi bi-collection', 'permission' => 'sales.alldata'],
                // ['route' => 'sales-analytics', 'title' => 'Sales Analytics', 'icon' => 'bi bi-bar-chart', 'permission' => 'sales.analytics'],
                // ['route' => 'lead', 'title' => 'Leads', 'icon' => 'bi bi-person-lines-fill', 'permission' => 'sales.leads'],
                ['route' => 'myleads', 'title' => 'My Leads', 'icon' => 'bi bi-person', 'permission' => 'sales.myleads'],
                ['route' => 'teamleads', 'title' => 'Team Leads', 'icon' => 'bi bi-people', 'condition' => 'has_subordinates', 'permission' => 'sales.teamleads'],
                ['route' => 'assignedleads', 'title' => 'Assigned Leads', 'icon' => 'bi bi-person-check', 'condition' => 'is_manager', 'permission' => 'sales.assignedleads'],
                ['route' => 'followup', 'title' => 'Follow Up', 'icon' => 'bi bi-bell', 'permission' => 'sales.followup'],

                ['route' => 'quotation', 'title' => 'Quotation', 'icon' => 'bi bi-file-text', 'permission' => 'sales.quotation'],
                ['route' => 'payment-followup', 'title' => 'Payment Followup', 'icon' => 'bi bi-cash-coin', 'permission' => 'sales.payment_followup'],

                ['route' => 'formbuilder.index', 'title' => 'Lead Form', 'icon' => 'bi bi-ui-checks-grid', 'permission' => 'sales.leadform'],
                // IndiaMART
                ['route' => 'indiamart.index', 'title' => 'External Leads', 'icon' => 'bi bi-bag', 'permission' => 'sales.indiamart'],
                ['route' => 'indiamart.junk.index', 'title' => 'External Junk Leads', 'icon' => 'bi bi-trash', 'permission' => 'sales.indiamart.junk'],
            ],
        ],
        [
            'key' => 'admin_tele_calling',
            'title' => 'Tele Calling',
            'icon' => 'bi bi-telephone-outbound',
            'feature_flag' => 'is_sales_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'calling', 'title' => 'Calling Board', 'icon' => 'bi bi-telephone', 'permission' => 'sales.calling'],
                ['route' => 'calling.my', 'title' => 'My Calls', 'icon' => 'bi bi-person', 'permission' => 'sales.calling.my'],
                ['route' => 'calling.team', 'title' => 'Team Calls', 'icon' => 'bi bi-people', 'condition' => 'has_subordinates', 'permission' => 'sales.calling.team'],
                ['route' => 'calling.assigned', 'title' => 'Assigned Calls', 'icon' => 'bi bi-person-check', 'condition' => 'is_manager', 'permission' => 'sales.calling.assigned'],
                ['route' => 'calling.todays', 'title' => 'Today\'s Calls', 'icon' => 'bi bi-calendar-date', 'permission' => 'sales.calling.todays'],
                ['route' => 'calling.junk', 'title' => 'Junk Calls', 'icon' => 'bi bi-trash', 'permission' => 'sales.calling.junk'],
            ],
        ],
        [
            'key' => 'admin_projects',
            'title' => 'Projects',
            'route' => 'projects.index',
            'icon' => 'bi bi-kanban',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'permission' => 'projects.view',
        ],
        [
            'key' => 'admin_subs_renewal',
            'title' => 'Subs & Renewal',
            'route' => 'subscriptions.index',
            'icon' => 'bi bi-arrow-repeat',
            'feature_flag' => 'is_sales_enabled',
            'roles' => ['admin'],
            'permission' => 'subscription.view',
        ],
        [
            'key' => 'admin_tracking',
            'title' => 'Tracking',
            'route' => 'tracking.index',
            'icon' => 'bi bi-geo-alt',
            'feature_flag' => 'is_attendance_enabled', // Assuming tracking relates to attendance
            'roles' => ['admin'],
            'permission' => 'tracking.view',
        ],
        // Admin gets full worklog access when worklog is enabled
        [
            'key' => 'admin_worklog_operational',
            'title' => 'Timesheet',
            'icon' => 'bi bi-clock',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'worklog', 'title' => 'Timesheet', 'icon' => 'bi bi-clipboard-check', 'permission' => 'worklog.entry'],
                ['route' => 'worklog-history', 'title' => 'Timesheet History', 'icon' => 'bi bi-clock-history', 'permission' => 'worklog.history'],
                ['route' => 'worklog-missing-summary', 'title' => 'Missing Entries Summary', 'icon' => 'bi bi-exclamation-triangle', 'permission' => 'worklog.missing_summary'],
            ],
        ],
        // Workflow - separate section placed below Worklog
        [
            'key' => 'workflow_critical_path',
            'title' => 'Workflow',
            'icon' => 'bi bi-diagram-3',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'critical-path.index', 'title' => 'Critical Path', 'icon' => 'bi bi-diagram-2', 'permission' => 'workflow.critical_path'],
                ['route' => 'workflow-templates.index', 'title' => 'Workflow Templates', 'icon' => 'bi bi-journal-text', 'permission' => 'workflow.templates'],
                ['route' => 'workflow-dependencies.index', 'title' => 'Dependencies', 'icon' => 'bi bi-diagram-3-fill', 'permission' => 'workflow.dependencies'],
            ],
        ],
        // Calendar - separate section
        [
            'key' => 'calendar_section',
            'title' => 'Social Media Calendar',
            'icon' => 'bi bi-calendar3',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'calendar.index', 'title' => 'Calendar', 'icon' => 'bi bi-calendar3', 'permission' => 'calendar.view'],
                ['route' => 'calendar-client-event.links', 'title' => 'Manage Calendar', 'icon' => 'bi bi-diagram-2', 'permission' => 'calendar.client_event_links'],
            ],
        ],
        // Master - separate section 
        [
            'key' => 'master_section',
            'title' => 'Master',
            'icon' => 'bi bi-person-badge',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'employees.index', 'title' => 'Employees', 'icon' => 'bi bi-people', 'permission' => 'master.employees'],

            ],
        ],
        // Tasks - separate section placed below Worklog
        [
            'key' => 'admin_tasks',
            'title' => 'Tasks & Reminders',
            'icon' => 'bi bi-list-task',
            'feature_flag' => 'is_worklog_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'all-tasks.index', 'title' => 'All Tasks', 'icon' => 'bi bi-card-list', 'permission' => 'task.view'],
                ['route' => 'task.index', 'title' => 'Task', 'tooltip' => 'Task assign by me', 'icon' => 'bi bi-list-task', 'permission' => 'task.my_created'],
                ['route' => 'my-tasks.index', 'title' => 'My Tasks', 'tooltip' => 'Task assign to me', 'icon' => 'bi bi-person-check', 'permission' => 'task.my_tasks'],
            ],
        ],
        // Admin gets full attendance access when attendance is enabled
        [
            'key' => 'admin_attendance_operational',
            'title' => 'Attendance',
            'icon' => 'bi bi-person-check',
            'feature_flag' => 'is_attendance_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'attendance', 'title' => 'Mark Attendance', 'icon' => 'bi bi-person-check', 'permission' => 'attendance.entry'],
                ['route' => 'attendance.history', 'title' => 'Attendance History', 'icon' => 'bi bi-journal-text', 'permission' => 'attendance.history'],
                ['route' => 'leave.index', 'title' => 'Leave', 'icon' => 'bi bi-calendar-minus', 'permission' => 'worklog.leave'],
            ],
        ],
        // Reports section
        [
            'key' => 'admin_reports',
            'title' => 'Reports',
            'icon' => 'bi bi-file-earmark-bar-graph',
            'feature_flag' => 'is_attendance_enabled',
            'roles' => ['admin'],
            'items' => [
                // ['route' => 'attendance.stats-view', 'title' => 'Attendance Stats', 'icon' => 'bi bi-bar-chart', 'permission' => 'attendance.stats'],
                ['route' => 'attendance.report', 'title' => 'Attendance Report', 'icon' => 'bi bi-file-earmark-text', 'permission' => 'attendance.report'],
                ['route' => 'reports.worklog', 'title' => 'Timesheet Report', 'icon' => 'bi bi-journals'],
                // ['route' => 'reports.user-worklog', 'title' => 'User-wise Report', 'icon' => 'bi bi-person-lines-fill'],
            ],
        ],
        // Document Management section
        [
            'key' => 'admin_document_management',
            'title' => 'Document',
            'icon' => 'bi bi-folder2-open',
            'feature_flag' => 'is_document_management_enabled',     
            'roles' => ['admin'],
            'items' => [
                ['route' => 'document.index', 'title' => 'Manage Documents', 'icon' => 'bi bi-folder', 'permission' => 'documents.manage'],
                ['route' => 'document.user-access', 'title' => 'My Documents', 'icon' => 'bi bi-person-check', 'permission' => 'documents.my_documents'],
            ],
        ],
        // Petty Cash section
        [
            'key' => 'petty_cash_section',
            'title' => 'Petty Cash',
            'route' => 'petty-cash.index',
            'icon' => 'bi bi-cash-stack',
            'feature_flag' => 'is_petty_cash_enable',
            'roles' => ['admin'],
            'permission' => 'petty_cash.view',
        ],
        // Approvals section
        [
            'key' => 'approvals_section',
            'title' => 'Approvals',
            'icon' => 'bi bi-check2-circle',
            'feature_flag' => 'is_approval_enabled',
            'roles' => ['admin'],
            'items' => [
                ['route' => 'approvals.petty', 'title' => 'Petty Approval', 'icon' => 'bi bi-cash', 'permission' => 'approvals.petty'],
                ['route' => 'worklog-approvals', 'title' => 'Timesheet Approvals', 'icon' => 'bi bi-check2-square', 'permission' => 'worklog.approvals'],
                ['route' => 'attendance.approval', 'title' => 'Attendance Approval', 'icon' => 'bi bi-person-check', 'permission' => 'attendance.approval'],
                ['route' => 'attendance.unlock', 'title' => 'Unlock Attendance', 'icon' => 'bi bi-unlock', 'permission' => 'attendance.approval'],
            ],
        ],
        // Contact Management section
        [
            'key' => 'contact_management',
            'title' => 'Contact Management',
            'route' => 'contactmanagement.index',
            'icon' => 'bi bi-person-lines-fill',
            'feature_flag' => 'is_contact_management',
            'roles' => ['admin'],
            'permission' => 'contact_management.access',
        ],
        // Asset Management section
        [
            'key' => 'asset_management',
            'title' => 'Asset Management',
            'route' => 'asset-management.index',
            'icon' => 'bi bi-box-seam',
            'feature_flag' => 'is_asset_management_enable',
            'roles' => ['admin'],
            'permission' => 'asset_management.access',
        ],
        // Email Marketing section
        [
            'key' => 'email_marketing',
            'title' => 'Email Marketing',
            'route' => 'emailmarketing.index',
            'icon' => 'bi bi-envelope',
            'feature_flag' => 'is_email_marketing_enable',
            'roles' => ['admin'],
            'permission' => 'email_marketing.view',
        ],
                // Software Setup - Consolidated Setup Section
                [
                    'key' => 'software_setup',
                    'title' => 'Software Setup',
                    'icon' => 'bi bi-gear-fill',
                    'feature_flag' => 'is_setup_enabled',
                    'roles' => ['admin'],
                    'items' => [
                        // Core Setup (always show if setup is enabled)
                        ['route' => 'state', 'title' => 'State', 'icon' => 'bi bi-globe'],
                        ['route' => 'city', 'title' => 'City', 'icon' => 'bi bi-geo-alt'],
                        ['route' => 'countries.index', 'title' => 'Countries', 'icon' => 'bi bi-flag', 'permission' => 'master.countries'],
                        
                        // User Management (show if user setup is enabled)
                        ['route' => 'user', 'title' => 'User Management', 'icon' => 'bi bi-people', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'role-master', 'title' => 'Role Master', 'icon' => 'bi bi-shield-lock', 'feature_flag' => 'is_user_setup_enabled'],
                        
                        // Master Setup (Moved from Master Section)
                        ['route' => 'branches.index', 'title' => 'Branches', 'icon' => 'bi bi-diagram-3', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'shifts.index', 'title' => 'Shift', 'icon' => 'bi bi-clock-history', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'departments.index', 'title' => 'Departments', 'icon' => 'bi bi-diagram-2', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'designations.index', 'title' => 'Designations', 'icon' => 'bi bi-badge-ad', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'employment-types.index', 'title' => 'Employment Types', 'icon' => 'bi bi-briefcase', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'late-reasons.index', 'title' => 'Late Reasons', 'icon' => 'bi bi-clock-history', 'feature_flag' => 'is_user_setup_enabled'],
                        ['route' => 'places.index', 'title' => 'Places', 'icon' => 'bi bi-map', 'feature_flag' => 'is_user_setup_enabled'],
                        
                        // Sales Setup (show if sales setup is enabled)
                        ['route' => 'status', 'title' => 'Sales Status', 'icon' => 'bi bi-check2-circle', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'source', 'title' => 'Lead Source', 'icon' => 'bi bi-diagram-3', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'product', 'title' => 'Product', 'icon' => 'bi bi-box2', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'payment-terms', 'title' => 'Payment Terms', 'icon' => 'bi bi-credit-card', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'business', 'title' => 'Business Type', 'icon' => 'bi bi-briefcase', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'calling-type.index', 'title' => 'Calling Types', 'icon' => 'bi bi-list-ul', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'quotation.setup', 'title' => 'Quotation Setup', 'icon' => 'bi bi-file-earmark-text', 'feature_flag' => 'is_sales_setup_enabled'],
                        ['route' => 'expenses.index', 'title' => 'Expenses', 'icon' => 'bi bi-cash-coin', 'feature_flag' => 'is_petty_cash_enable'],
                        ['route' => 'petty-opening-balance.index', 'title' => 'Opening Balance', 'icon' => 'bi bi-wallet2', 'feature_flag' => 'is_petty_cash_enable'],
                        
                        // Work & Project Setup (show if work setup is enabled)
                        ['route' => 'customer', 'title' => 'Customer', 'icon' => 'bi bi-person-badge', 'feature_flag' => 'is_work_setup_enabled'],
                        ['route' => 'service', 'title' => 'Project Services', 'icon' => 'bi bi-kanban', 'feature_flag' => 'is_work_setup_enabled'],
                        ['route' => 'module', 'title' => 'Module', 'icon' => 'bi bi-puzzle', 'feature_flag' => 'is_work_setup_enabled'],
                        ['route' => 'customer-project', 'title' => 'Open Project', 'icon' => 'bi bi-collection', 'feature_flag' => 'is_work_setup_enabled'],
                        ['route' => 'entry-type.index', 'title' => 'Entry Types', 'icon' => 'bi bi-list-check', 'feature_flag' => 'is_work_setup_enabled'],
                        ['route' => 'holiday', 'title' => 'Holidays', 'icon' => 'bi bi-calendar2-event', 'feature_flag' => 'is_work_setup_enabled'],
                        
                        // Task & Subscription Setup (show if respective setup is enabled)
                        ['route' => 'task-status.index', 'title' => 'Task Status', 'icon' => 'bi bi-tag', 'permission' => 'task.status', 'feature_flag' => 'is_worklog_enabled'],
                        ['route' => 'subscription-status.index', 'title' => 'Subscription Status', 'icon' => 'bi bi-tag', 'feature_flag' => 'is_subscription_enabled'],
                        // Calendar Setup
                        ['route' => 'calendar-events.index', 'title' => 'Calendar Events', 'icon' => 'bi bi-calendar-plus', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.events'],
                        ['route' => 'calendar-missed-reasons.index', 'title' => 'Missed Reason', 'icon' => 'bi bi-calendar-x', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.events'],
                        ['route' => 'calendar-status.index', 'title' => 'Calendar Status', 'icon' => 'bi bi-tag', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.status'],
                        ['route' => 'calendar-status-checklist.index', 'title' => 'Status-Checklist', 'icon' => 'bi bi-link-45deg', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.status_checklist'],
                        ['route' => 'common-events.index', 'title' => 'Common Events', 'icon' => 'bi bi-collection', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.common_events'],
                        ['route' => 'calendar-social.index', 'title' => 'Calendar Social Handles', 'icon' => 'bi bi-share', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.social_handles'],
                        ['route' => 'calendar-clients.index', 'title' => 'Calendar Clients', 'icon' => 'bi bi-people', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.clients'],
                        ['route' => 'calendar-client-social.index', 'title' => 'Client Social Handles', 'icon' => 'bi bi-link-45deg', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'calendar.client_social'],
                        ['route' => 'checklist.index', 'title' => 'Checklist', 'icon' => 'bi bi-list-check', 'feature_flag' => 'is_worklog_enabled', 'permission' => 'checklist.index'],
                        // Asset Management Setup (show if asset management is enabled)
                        ['route' => 'asset-type.index', 'title' => 'Asset Types', 'icon' => 'bi bi-box', 'feature_flag' => 'is_asset_management_enable'],
                        ['route' => 'asset-category.index', 'title' => 'Asset Categories', 'icon' => 'bi bi-tags', 'feature_flag' => 'is_asset_management_enable'],
                        ['route' => 'asset-status.index', 'title' => 'Asset Status', 'icon' => 'bi bi-check2-circle', 'feature_flag' => 'is_asset_management_enable'],
                        ['route' => 'supplier.index', 'title' => 'Suppliers', 'icon' => 'bi bi-truck', 'feature_flag' => 'is_asset_management_enable'],
                        ['route' => 'assets.index', 'title' => 'Open Assets', 'icon' => 'bi bi-box-seam', 'feature_flag' => 'is_asset_management_enable'],
                    ],
                ],

    ],
];