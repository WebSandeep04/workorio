@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
    $userDisplayName = auth()->check()
        ? auth()->user()->name
        : (session('user_name') ?? 'there');
    $firstName = explode(' ', trim($userDisplayName))[0] ?: 'there';

    // Resolve tenant for feature flags
    $tenant = null;
    if (session()->has('tenant_id')) {
        $tenant = \App\Models\Tenant::find(session('tenant_id'));
    }

    $currentUserId = auth()->check() ? auth()->id() : session('user_id');
    $currentUser = $currentUserId ? \App\Models\User::find($currentUserId) : null;
    $hasSubordinates = ($currentUser && $currentUser->subordinates()->exists()) || ($currentUser && $currentUser->role_id == 1);

    // Resolve tenant for feature flags - handle missing flags gracefully
    $isSales = isset($tenant->is_sales_enabled) ? (bool)$tenant->is_sales_enabled : true;
    $isCalling = isset($tenant->is_tally_calling_enabled) ? (bool)$tenant->is_tally_calling_enabled : true;
    $isAttendance = isset($tenant->is_attendance_enabled) ? (bool)$tenant->is_attendance_enabled : true;
    $isTasks = isset($tenant->is_task_reminders_enabled) ? (bool)$tenant->is_task_reminders_enabled : true;
    $isSubs = isset($tenant->is_subscription_enabled) ? (bool)$tenant->is_subscription_enabled : true;
    $isPettyCash = isset($tenant->is_petty_cash_enable) ? (bool)$tenant->is_petty_cash_enable : true;
    $isCalendar = isset($tenant->is_social_media_calendar_enabled) ? (bool)$tenant->is_social_media_calendar_enabled : true;
    $isApproval = isset($tenant->is_approval_enabled) ? (bool)$tenant->is_approval_enabled : true;
    $isWorklog = isset($tenant->is_worklog_enabled) ? (bool)$tenant->is_worklog_enabled : true;
    $canQuickWorklog = $isWorklog && $currentUser && !empty($currentUser->is_worklog);
@endphp

<div class="dashboard-wrapper">
    <!-- Top Dashboard Header Row -->
    <div class="dash-top-bar">
        <div class="wbar">
            <h2>Welcome back, {{ $firstName }}! 👋</h2>
            <p>Here is your business overview for today.</p>
        </div>

        <div class="dash-top-controls">
            @if($hasSubordinates)
            <div class="rtabs">
                <button class="rtab act" onclick="sw('founder',this)">My Dashboard</button>
                <button class="rtab" onclick="sw('sales',this)">Teams Dashboard</button>
            </div>
            @endif
            @if($isAttendance)
            <div class="my-att-container" id="my-att-container" style="display:none;">
                <div id="my-att-badge" class="my-att-pill" onclick="toggleMyAttDropdown(event)" title="Click for Attendance Options (Punch Out, Break, Details)"></div>
                <button type="button" class="my-att-quick-btn" id="my-att-quick-btn" style="display:none;"></button>
                <div class="my-att-dropdown" id="my-att-dropdown" style="display:none;"></div>
            </div>
            @endif
            @if($canQuickWorklog)
            <button type="button" id="dash-ts-pill" class="dash-ts-pill" onclick="openQuickTimesheetDrawer()" style="display:none;" title="Open Quick Timesheet">
                <span class="dash-ts-dot"></span>
                <span class="dash-ts-text">Timesheet</span>
            </button>
            @endif
            <div class="dash-date-pill">
                <i class="bi bi-calendar3"></i>
                <strong>{{ date('l, d M Y') }}</strong>
                <span class="dash-date-sep">•</span>
                <span class="dash-live-clock" id="clk-display">Workorio</span>
            </div>
        </div>
    </div>

    <!-- ===== FOUNDER VIEW ===== -->
    <div class="d-view act" id="vf">
        @if($isSales || $isCalling)
            @if($isSales && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('sales.')))
            <div class="dash-sec-hdr">
                <div>
                    <div class="dash-sec-title">Sales Intelligence</div>
                    <div class="dash-sec-sub">Real-time performance metrics for your sales pipeline</div>
                </div>
            </div>
            <div class="sgrid">
                <div class="sc" onclick="location.href='{{ route('todayfollowupstable') }}'">
                    <div class="sc-top">
                        <span class="sc-label">Today's Followups</span>
                        <span class="sc-icon c-amber">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="todayfollowups">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-amber">Scheduled</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>

                <div class="sc" onclick="location.href='{{ route('underprocesstable') }}'">
                    <div class="sc-top">
                        <span class="sc-label">Under Process</span>
                        <span class="sc-icon c-emerald">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="underprocess">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-emerald">In Pipeline</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>

                <div class="sc" onclick="location.href='{{ route('todaycompletedtable') }}'">
                    <div class="sc-top">
                        <span class="sc-label">Today Completed</span>
                        <span class="sc-icon c-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="todaycompleted">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-blue">Done Today</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>

                <div class="sc" onclick="location.href='{{ route('todaypendingtable') }}'">
                    <div class="sc-top">
                        <span class="sc-label">Today Pending</span>
                        <span class="sc-icon c-violet">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="todaypending">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-violet">Action Due</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>

                <div class="sc" onclick="location.href='{{ route('todaynewtable') }}'">
                    <div class="sc-top">
                        <span class="sc-label">New Followups</span>
                        <span class="sc-icon c-rose">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="todaynew">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-rose">Fresh</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>

                <div class="sc" onclick="location.href='{{ route('myleads') }}'">
                    <div class="sc-top">
                        <span class="sc-label">All Leads</span>
                        <span class="sc-icon c-indigo">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        </span>
                    </div>
                    <div class="sc-val"><span id="allleads">0</span></div>
                    <div class="sc-foot">
                        <span class="sc-pill p-indigo">Total</span>
                        <span class="sc-arrow">→</span>
                    </div>
                </div>
            </div>
            @endif
        @endif

        @php
            $showCalling = $isCalling && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('sales.calling'));
            $showAttendance = $isAttendance && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('attendance.'));
            $showLeadSource = $isSales && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('sales.'));
            $showTasks = $isTasks && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('task.'));
            $showDuePayments = $isSales && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('sales.payment_followup'));
            $showDueSubs = $isSubs && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('subscription.view'));
            $showCalendar = $isCalendar && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('calendar.view'));

            $canViewApprovals = $isApproval && $hasSubordinates && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('approvals.'));
            $canViewPettyCash = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('petty_cash.view'));
            $showPettyCash = $isPettyCash && $canViewPettyCash;
            $canViewLeaves = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('attendance.leave'));
            $showLeaves = ($isPettyCash || $isAttendance) && $canViewLeaves;
            $showHolidays = $isAttendance && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('attendance.'));
            $showCelebrations = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('attendance.'));

            $earnedLeaveCounter = \App\Models\LeaveAccrualCounter::where('user_id', $currentUserId)->first();
            $earnedLeaveDays = $earnedLeaveCounter ? floatval($earnedLeaveCounter->valid_days_count) : 0;

            $canAllTasks = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('task.view'));
            $canMyTasks = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('task.my_tasks'));
            $canCreatedTasks = $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermission('task.my_created'));

            $taskToMeUrl = ($canMyTasks && Route::has('my-tasks.index'))
                ? route('my-tasks.index')
                : (($canAllTasks && Route::has('all-tasks.index')) ? route('all-tasks.index') : (Route::has('task.index') ? route('task.index') : '#'));
            $taskByMeUrl = ($canCreatedTasks && Route::has('task.index'))
                ? route('task.index')
                : (($canAllTasks && Route::has('all-tasks.index')) ? route('all-tasks.index') : $taskToMeUrl);
            $taskViewAllUrl = ($canAllTasks && Route::has('all-tasks.index')) ? route('all-tasks.index') : $taskToMeUrl;

            $mainWidgets = [];
            if ($showCalling)     $mainWidgets[] = 'calling';
            if ($showAttendance)  $mainWidgets[] = 'attendance';
            if ($showLeadSource)  $mainWidgets[] = 'lead_source';
            if ($showTasks)       $mainWidgets[] = 'task';
            if ($showDuePayments) $mainWidgets[] = 'due_payments';
            if ($showDueSubs)     $mainWidgets[] = 'due_subs';
            if ($showCalendar)    $mainWidgets[] = 'calendar';

            $compactWidgets = [];
            if ($canViewApprovals) $compactWidgets[] = 'approvals';
            if ($showPettyCash)    $compactWidgets[] = 'petty_cash';
            if ($showLeaves)       $compactWidgets[] = 'leaves';
            if ($showHolidays)     $compactWidgets[] = 'holidays';
            if ($showCelebrations) $compactWidgets[] = 'celebrations';

            $reservedThreeCol = null;
            if ($showDuePayments && $showDueSubs && $showCalendar) {
                $reservedThreeCol = ['class' => 'dash-row-3', 'widgets' => ['due_payments', 'due_subs', 'calendar'], 'style' => ''];
                $mainWidgets = array_values(array_diff($mainWidgets, ['due_payments', 'due_subs', 'calendar']));
            }

            $dashboardRows = [];
            $countMain = count($mainWidgets);
            for ($i = 0; $i + 1 < $countMain; $i += 2) {
                $pair = [$mainWidgets[$i], $mainWidgets[$i + 1]];
                $rowClass = 'dash-row-2';
                if ($pair === ['calling', 'attendance'] || $pair === ['attendance', 'calendar'] || $pair === ['calling', 'calendar']) {
                    $rowClass = 'dash-row-mid-top';
                } elseif ($pair === ['lead_source', 'task']) {
                    $rowClass = 'dash-row-mid-bot';
                }
                $dashboardRows[] = ['class' => $rowClass, 'widgets' => $pair, 'style' => ''];
            }

            $bottomWidgets = $compactWidgets;
            $promotedCalToBottom = false;

            if ($countMain % 2 === 1) {
                $leftover = $mainWidgets[$countMain - 1];
                $compactFriendly = ['lead_source', 'task', 'due_payments', 'due_subs', 'calendar'];
                if ($leftover === 'calendar' && count($compactWidgets) >= 1 && count($compactWidgets) <= 4) {
                    array_unshift($bottomWidgets, 'calendar');
                    $promotedCalToBottom = true;
                } elseif (!$reservedThreeCol && !empty($dashboardRows)) {
                    $lastIdx = count($dashboardRows) - 1;
                    $prevPair = $dashboardRows[$lastIdx]['widgets'];
                    if (count($prevPair) === 2
                        && in_array($prevPair[0], $compactFriendly, true)
                        && in_array($prevPair[1], $compactFriendly, true)
                        && in_array($leftover, $compactFriendly, true)
                    ) {
                        $dashboardRows[$lastIdx] = [
                            'class' => 'dash-row-3',
                            'widgets' => [$prevPair[0], $prevPair[1], $leftover],
                            'style' => '',
                        ];
                    } else {
                        $dashboardRows[] = ['class' => 'dash-row-single', 'widgets' => [$leftover], 'style' => ''];
                    }
                } else {
                    $dashboardRows[] = ['class' => 'dash-row-single', 'widgets' => [$leftover], 'style' => ''];
                }
            }

            if ($reservedThreeCol) {
                $dashboardRows[] = $reservedThreeCol;
            }

            if (!empty($bottomWidgets)) {
                if ($promotedCalToBottom) {
                    $compactCount = count($bottomWidgets) - 1;
                    $dashboardRows[] = [
                        'class' => 'dash-row-bottom-cal',
                        'widgets' => $bottomWidgets,
                        'style' => '--bottom-compact-count: ' . $compactCount . ';',
                    ];
                } else {
                    $dashboardRows[] = [
                        'class' => 'dash-row-5',
                        'widgets' => $bottomWidgets,
                        'style' => '',
                    ];
                }
            }
        @endphp

        @foreach($dashboardRows as $row)
        <div class="{{ $row['class'] }}"@if(!empty($row['style'])) style="{{ $row['style'] }}"@endif>
            @foreach($row['widgets'] as $widget)
                @if($widget === 'calling')
                <div class="card">
                    <div class="chead">
                        <div>
                            <span class="ctitle">Tele-Calling Status</span>
                            <div class="tc-head-sub">Daily calling activity and follow-up tracking</div>
                        </div>
                        <div class="tc-legend">
                            <span class="tc-leg-item"><span class="tc-leg-dot" style="background:#7C3AED;"></span>Calling Activity</span>
                        </div>
                    </div>
                    <div class="tc-chart-wrap">
                        <canvas id="callingChart"></canvas>
                    </div>
                    <div class="tc-metrics-strip">
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.todayfollowupstable') }}'">
                            <div class="tc-chip-val" id="c_todayfollowups">0</div>
                            <div class="tc-chip-lbl">Followups</div>
                        </div>
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.underprocesstable') }}'">
                            <div class="tc-chip-val" id="c_underprocess">0</div>
                            <div class="tc-chip-lbl">In Process</div>
                        </div>
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.todaycompletedtable') }}'">
                            <div class="tc-chip-val" id="c_todaycompleted">0</div>
                            <div class="tc-chip-lbl">Completed</div>
                        </div>
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.todaypendingtable') }}'">
                            <div class="tc-chip-val" id="c_todaypending">0</div>
                            <div class="tc-chip-lbl">Pending</div>
                        </div>
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.todaynewtable') }}'">
                            <div class="tc-chip-val" id="c_todaynew">0</div>
                            <div class="tc-chip-lbl">New</div>
                        </div>
                        <div class="tc-metric-chip" onclick="location.href='{{ route('calling.allleadstable') }}'">
                            <div class="tc-chip-val" id="c_allleads">0</div>
                            <div class="tc-chip-lbl">All Leads</div>
                        </div>
                    </div>
                </div>
                @elseif($widget === 'attendance')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Team Attendance Today</span>
                    </div>
                    <div class="att-visual-wrap">
                        <div id="att-present-block" class="att-present-group">
                            <div class="att-donut-box">
                                <canvas id="attDonut" width="124" height="124"></canvas>
                                <div class="att-donut-center">
                                    <div class="att-pct-num" id="att-pct">0%</div>
                                    <div class="att-pct-lbl">Present (<span id="att-present">0</span>)</div>
                                </div>
                            </div>
                            <div class="att-breakdown-box">
                                <div class="att-bd-row">
                                    <span class="att-bd-left"><span class="att-chip-dot dot-office"></span>In Office</span>
                                    <span class="att-bd-val" id="att-office">0</span>
                                </div>
                                <div class="att-bd-row">
                                    <span class="att-bd-left"><span class="att-chip-dot dot-remote"></span>Remote</span>
                                    <span class="att-bd-val" id="att-remote">0</span>
                                </div>
                                <div class="att-bd-row">
                                    <span class="att-bd-left"><span class="att-chip-dot dot-field"></span>In Field</span>
                                    <span class="att-bd-val" id="att-field">0</span>
                                </div>
                                <div class="att-bd-row">
                                    <span class="att-bd-left"><span class="att-chip-dot dot-wfh"></span>WFH</span>
                                    <span class="att-bd-val" id="att-wfh">0</span>
                                </div>
                            </div>
                        </div>

                        <div id="att-absent-block" class="att-absent-box">
                            <svg class="att-absent-icon-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <div class="att-absent-num" id="att-absent">0</div>
                            <div class="att-absent-lbl">Absent</div>
                        </div>
                    </div>
                </div>
                @elseif($widget === 'lead_source')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Lead Source Breakdown</span>
                        <span class="card-dots-btn">···</span>
                    </div>
                    <div class="dw">
                        <div class="dc">
                            <canvas id="dc1" width="136" height="136"></canvas>
                            <div class="dctr">
                                <div class="n" id="total-leads-donut">0</div>
                                <div class="l">Leads</div>
                            </div>
                        </div>
                        <ul class="leg" id="lead-source-leg">
                            <!-- Dynamic legend -->
                        </ul>
                    </div>
                </div>
                @elseif($widget === 'task')
                <div class="card task-card">
                    <div class="chead">
                        <span class="ctitle">Task</span>
                        <span class="va" onclick="goToActiveTaskPage()">View All →</span>
                    </div>
                    <div class="subtabs">
                        <button class="subtab" onclick="swTaskTab('byMe',this)">Assigned by You</button>
                        <button class="subtab act" onclick="swTaskTab('toMe',this)">Assigned to You</button>
                    </div>
                    <div id="tk-byMe" class="tk-cnt dash-list">
                        <div class="task-empty-cta">
                            <div class="task-empty-icon"><i class="bi bi-clipboard-check"></i></div>
                            <div class="task-empty-title">Loading tasks...</div>
                            <div class="task-empty-sub">Fetching your assigned tasks</div>
                        </div>
                    </div>
                    <div id="tk-toMe" class="tk-cnt dash-list act">
                        <div class="task-empty-cta">
                            <div class="task-empty-icon"><i class="bi bi-clipboard-check"></i></div>
                            <div class="task-empty-title">Loading tasks...</div>
                            <div class="task-empty-sub">Fetching tasks assigned to you</div>
                        </div>
                    </div>
                </div>
                @elseif($widget === 'due_payments')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Due Payments</span>
                        <span id="overdue-badge" class="overdue-pill" style="display:none;">0 Overdue</span>
                    </div>
                    <div id="due-payments-list" class="dash-list">
                        <div class="dash-empty"><i class="bi bi-credit-card"></i><span>No pending payments</span></div>
                    </div>
                </div>
                @elseif($widget === 'due_subs')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Due Subscriptions</span>
                        <span class="va" onclick="location.href='{{ route('subscriptions.index') }}'">View All →</span>
                    </div>
                    <div id="due-subscriptions-list" class="dash-list">
                        <div class="dash-empty"><i class="bi bi-arrow-repeat"></i><span>No active subscriptions</span></div>
                    </div>
                </div>
                @elseif($widget === 'calendar')
                <div class="card">
                    <div class="cal-hdr">
                        <button class="cal-nav" onclick="prevM()">‹</button>
                        <span class="cal-month-lbl" id="cal-ml">April 2025</span>
                        <button class="cal-nav" onclick="nextM()">›</button>
                    </div>
                    <div class="cal-dnames">
                        <div class="cal-dn">Sun</div><div class="cal-dn">Mon</div><div class="cal-dn">Tue</div><div class="cal-dn">Wed</div><div class="cal-dn">Thu</div><div class="cal-dn">Fri</div><div class="cal-dn">Sat</div>
                    </div>
                    <div class="cal-grid" id="cal-grid"></div>
                </div>
                @elseif($widget === 'approvals')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Pending Approvals</span>
                        <span class="va" onclick="viewAllApprovals()">View All →</span>
                    </div>
                    <div class="ap-summary-list">
                        @if($isWorklog)
                        <button type="button" class="ap-summary-row" data-ap-type="ts" onclick="openApprovalModal('ts')">
                            <span class="ap-row-icon ap-ic-ts"><i class="bi bi-clock"></i></span>
                            <span class="ap-row-label">Timesheet</span>
                            <span class="ap-count-pill" id="ap-ts-badge">0</span>
                        </button>
                        @endif
                        @if($isAttendance)
                        <button type="button" class="ap-summary-row" data-ap-type="at" onclick="openApprovalModal('at')">
                            <span class="ap-row-icon ap-ic-at"><i class="bi bi-calendar-check"></i></span>
                            <span class="ap-row-label">Attendance</span>
                            <span class="ap-count-pill" id="ap-at-badge">0</span>
                        </button>
                        @endif
                        @if($isTasks)
                        <button type="button" class="ap-summary-row" data-ap-type="tk" onclick="openApprovalModal('tk')">
                            <span class="ap-row-icon ap-ic-tk"><i class="bi bi-check2-square"></i></span>
                            <span class="ap-row-label">Task</span>
                            <span class="ap-count-pill" id="ap-tk-badge">0</span>
                        </button>
                        @endif
                        @if($isAttendance)
                        <button type="button" class="ap-summary-row" data-ap-type="lv" onclick="openApprovalModal('lv')">
                            <span class="ap-row-icon ap-ic-lv"><i class="bi bi-person"></i></span>
                            <span class="ap-row-label">Leave</span>
                            <span class="ap-count-pill" id="ap-lv-badge">0</span>
                        </button>
                        @endif
                        @if($isPettyCash)
                        <button type="button" class="ap-summary-row" data-ap-type="pc" onclick="openApprovalModal('pc')">
                            <span class="ap-row-icon ap-ic-pc"><i class="bi bi-wallet2"></i></span>
                            <span class="ap-row-label">Petty Cash</span>
                            <span class="ap-count-pill" id="ap-pc-badge">0</span>
                        </button>
                        @endif
                    </div>
                </div>
                @elseif($widget === 'petty_cash')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Petty Cash</span>
                        <input type="hidden" id="pc-period" value="month">
                        <div class="dropdown pc-dropdown">
                            <button type="button" class="pc-dd-btn" data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="pc-period-label">This Month</span>
                                <i class="bi bi-chevron-down pc-dd-chev"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end pc-dd-menu">
                                <li><button type="button" class="pc-dd-item act" onclick="selectPCPeriod('month', 'This Month', this)">This Month</button></li>
                                <li><button type="button" class="pc-dd-item" onclick="selectPCPeriod('year', 'This Year', this)">This Year</button></li>
                                <li><button type="button" class="pc-dd-item" onclick="selectPCPeriod('fin', 'Financial Year', this)">Financial Year</button></li>
                            </ul>
                        </div>
                    </div>
                    <div class="pc-stack">
                        <div class="pc-tile pc-open-tile">
                            <span class="pc-tile-icon"><i class="bi bi-wallet2"></i></span>
                            <div>
                                <div class="pc-tile-val" id="pc-open">₹0</div>
                                <div class="pc-tile-lbl">Opening Balance</div>
                            </div>
                        </div>
                        <div class="pc-tile pc-used-tile">
                            <span class="pc-tile-icon"><i class="bi bi-credit-card"></i></span>
                            <div>
                                <div class="pc-tile-val" id="pc-used">₹0</div>
                                <div class="pc-tile-lbl">Balance Used</div>
                            </div>
                        </div>
                        <div class="pc-tile pc-rem-tile">
                            <span class="pc-tile-icon"><i class="bi bi-layers"></i></span>
                            <div>
                                <div class="pc-tile-val" id="pc-rem">₹0</div>
                                <div class="pc-tile-lbl">Remaining</div>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($widget === 'leaves')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Upcoming Leaves</span>
                    </div>
                    <div class="dash-center-body">
                        <div id="upcoming-leaves-list" class="leave-list-wrap">
                            <div class="dash-empty" style="padding: 16px 8px;">
                                <div class="dash-empty-circle"><i class="bi bi-calendar2-event"></i></div>
                                <span>No upcoming leaves</span>
                            </div>
                        </div>
                    </div>
                    <div class="leave-accrual-pill">
                        New earned leave will be earned in days: <strong>{{ $earnedLeaveDays }} / 45</strong>
                    </div>
                </div>
                @elseif($widget === 'holidays')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Upcoming Holidays</span>
                    </div>
                    <div id="holidays-list" class="dash-center-body">
                        <div class="dash-empty" style="padding: 16px 8px;">
                            <div class="dash-empty-circle"><i class="bi bi-calendar-event"></i></div>
                            <span>No upcoming Holidays</span>
                        </div>
                    </div>
                </div>
                @elseif($widget === 'celebrations')
                <div class="card">
                    <div class="chead">
                        <span class="ctitle">Celebrations</span>
                    </div>
                    <div id="celebrations-list" class="dash-center-body">
                        <div class="dash-empty" style="padding: 16px 8px;">
                            <div class="dash-empty-circle"><i class="bi bi-stars"></i></div>
                            <span>No upcoming celebrations</span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="modal-ov" id="modal-cal" onclick="closeModal(event)">
    <div class="modal-cnt" onclick="event.stopPropagation()">
        <div class="m-close" onclick="closeModal(event)">✕</div>
        <div id="cal-detail-modal"></div>
    </div>
</div>

@if($canViewApprovals)
<div class="modal-ov" id="modal-approvals" onclick="closeApprovalModal(event)">
    <div class="modal-cnt modal-ap" onclick="event.stopPropagation()">
        <div class="modal-ap-head">
            <span class="ctitle">Pending Approvals</span>
            <div class="modal-ap-actions">
                <button type="button" class="btn-modal-viewall" onclick="viewAllApprovals()">
                    <span>View All</span>
                    <i class="bi bi-arrow-right-short"></i>
                </button>
                <button type="button" class="m-close-inline" onclick="closeApprovalModal(event)" aria-label="Close">✕</button>
            </div>
        </div>
        <div class="subtabs">
            @if($isWorklog)
            <button class="subtab act" data-ap-tab="ts" onclick="swApprovalTab('ts',this)">Timesheet<span class="notif-badge ap-modal-badge" data-ap="ts">0</span></button>
            @endif
            @if($isAttendance)
            <button class="subtab {{ !$isWorklog ? 'act' : '' }}" data-ap-tab="at" onclick="swApprovalTab('at',this)">Attendance<span class="notif-badge ap-modal-badge" data-ap="at">0</span></button>
            @endif
            @if($isTasks)
            <button class="subtab {{ !$isWorklog && !$isAttendance ? 'act' : '' }}" data-ap-tab="tk" onclick="swApprovalTab('tk',this)">Task<span class="notif-badge ap-modal-badge" data-ap="tk">0</span></button>
            @endif
            @if($isAttendance)
            <button class="subtab" data-ap-tab="lv" onclick="swApprovalTab('lv',this)">Leave<span class="notif-badge ap-modal-badge" data-ap="lv">0</span></button>
            @endif
            @if($isPettyCash)
            <button class="subtab {{ !$isWorklog && !$isAttendance && !$isTasks ? 'act' : '' }}" data-ap-tab="pc" onclick="swApprovalTab('pc',this)">Petty Cash<span class="notif-badge ap-modal-badge" data-ap="pc">0</span></button>
            @endif
        </div>

        @if($isWorklog)
        <div id="ap-ts" class="ap-cnt dash-list act">
            <div class="dash-empty"><i class="bi bi-clock-history"></i><span>No pending timesheets</span></div>
        </div>
        @endif

        @if($isAttendance)
        <div id="ap-at" class="ap-cnt dash-list {{ !$isWorklog ? 'act' : '' }}">
            <div class="dash-empty"><i class="bi bi-person-check"></i><span>No pending attendance</span></div>
        </div>
        @endif

        @if($isTasks)
        <div id="ap-tk" class="ap-cnt dash-list {{ !$isWorklog && !$isAttendance ? 'act' : '' }}">
            <div class="dash-empty"><i class="bi bi-list-check"></i><span>No pending tasks</span></div>
        </div>
        @endif

        @if($isAttendance)
        <div id="ap-lv" class="ap-cnt dash-list">
            <div class="dash-empty"><i class="bi bi-calendar2-minus"></i><span>No pending leaves</span></div>
        </div>
        @endif

        @if($isPettyCash)
        <div id="ap-pc" class="ap-cnt dash-list {{ !$isWorklog && !$isAttendance && !$isTasks ? 'act' : '' }}">
            <div class="dash-empty"><i class="bi bi-wallet2"></i><span>No pending petty cash</span></div>
        </div>
        @endif
    </div>
</div>
@endif

@if($showTasks)
<div class="modal-ov" id="modal-task-quick" onclick="closeTaskQuickModal()">
    <div class="modal-cnt modal-tkq" onclick="event.stopPropagation()">
        <div class="modal-ap-head">
            <div style="min-width:0;flex:1;">
                <span class="ctitle" id="tkq-title">Update Task</span>
                <div class="tkq-sub" id="tkq-meta"></div>
            </div>
            <div class="modal-ap-actions">
                <a href="{{ $taskToMeUrl }}" id="tkq-open-link" class="btn-modal-viewall" style="text-decoration:none;">
                    <span>Open Page</span>
                    <i class="bi bi-arrow-right-short"></i>
                </a>
                <button type="button" class="m-close-inline" onclick="closeTaskQuickModal()" aria-label="Close">✕</button>
            </div>
        </div>

        <div class="tkq-desc-box" id="tkq-desc-wrap" style="display:none;">
            <div class="tkq-desc" id="tkq-desc"></div>
        </div>

        <div class="tkq-field">
            <div class="tkq-label-row">
                <span class="tkq-label">Update Status</span>
            </div>
            <div class="tkq-status-grid" id="tkq-status-list">
                <span class="tkq-loading">Loading statuses...</span>
            </div>
        </div>

        <div class="tkq-field">
            <div class="tkq-label-row">
                <label class="tkq-label" for="tkq-remark">Add Remark</label>
                <span class="tkq-char" id="tkq-char-count">0 / 1000</span>
            </div>
            <textarea id="tkq-remark" class="tkq-textarea" rows="3" maxlength="1000" placeholder="Add progress update or remark..."></textarea>
        </div>

        <div class="tkq-foot">
            <span class="tkq-msg" id="tkq-msg"></span>
            <div class="tkq-foot-btns">
                <button type="button" class="tkq-btn-cancel" onclick="closeTaskQuickModal()">Cancel</button>
                <button type="button" class="tkq-btn-save" id="tkq-save-btn" onclick="submitTaskQuickUpdate()">Save Update</button>
            </div>
        </div>
    </div>
</div>
@endif

<div id="att-hover-popover" class="att-hover-popover" role="tooltip" aria-hidden="true"></div>
@if($canViewApprovals)
<div id="ap-hover-popover" class="ap-hover-popover" role="tooltip" aria-hidden="true"></div>
@endif
@if($showCalendar)
<div id="cal-hover-popover" class="cal-hover-popover" role="tooltip" aria-hidden="true"></div>
@endif
<div id="chart-hover-tooltip" class="chart-hover-tooltip" role="tooltip" aria-hidden="true"></div>
<div id="event-hover-popover" class="event-hover-popover" role="tooltip" aria-hidden="true"></div>
<div id="leave-hover-popover" class="event-hover-popover leave-hover-popover" role="tooltip" aria-hidden="true"></div>
<div id="dash-att-toast" class="dash-att-toast" role="alert" aria-live="polite"></div>

<!-- Quick Late Punch-in Modal -->
<div class="modal fade" id="quickLateReasonModal" tabindex="-1" aria-labelledby="quickLateReasonModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 20px 40px rgba(15,23,42,0.18);">
            <div class="modal-header" style="background: linear-gradient(135deg, #434AFA 0%, #3138D6 100%); color: #fff; border-radius: 15px 15px 0 0; padding: 14px 20px;">
                <h6 class="modal-title d-flex align-items-center gap-2 fw-bold" id="quickLateReasonModalLabel" style="font-size: 15px;">
                    <i class="bi bi-clock-history"></i> Late Punch-in Reason
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="font-size: 12px;"></button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <p style="font-size: 12.5px; color: #64748B; margin-bottom: 12px;">You are punching in past official cutoff time. Please select or state your reason to record attendance:</p>
                <div class="mb-3">
                    <label class="form-label" style="font-size: 12px; font-weight: 700; color: #1E293B;">Select Reason</label>
                    <select class="form-select form-select-sm" id="quickLatePreset" onchange="if(this.value && this.value!=='Other'){ $('#quickLateCustom').val(this.value); } else { $('#quickLateCustom').val(''); }" style="border-radius: 8px;">
                        <option value="">-- Choose a standard reason --</option>
                        <option value="Heavy traffic / Transport delay">Heavy traffic / Transport delay</option>
                        <option value="Client meeting on the way">Client meeting on the way</option>
                        <option value="Personal emergency">Personal emergency</option>
                        <option value="Health / Doctor appointment">Health / Doctor appointment</option>
                        <option value="Bad weather condition">Bad weather condition</option>
                        <option value="Other">Other reason...</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label" style="font-size: 12px; font-weight: 700; color: #1E293B;">Reason Detail <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="quickLateCustom" rows="2" placeholder="Describe the reason..." style="border-radius: 8px; font-size: 12.5px;"></textarea>
                </div>
                <div id="quickLateError" class="text-danger small mt-1" style="display:none;">Please enter a reason.</div>
            </div>
            <div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #F1F5F9; background: #F8FAFC; border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal" style="font-weight: 600; border-radius: 8px;">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary" id="btnSubmitQuickLate" onclick="submitQuickLateReason()" style="background: #434AFA; border-color: #434AFA; font-weight: 700; border-radius: 8px; padding: 6px 16px;">
                    <i class="bi bi-check-circle me-1"></i> Save & Punch In
                </button>
            </div>
        </div>
    </div>
</div>

@if($canQuickWorklog)
<!-- Post Punch-Out Guided Timesheet Prompt -->
<div id="dash-ts-prompt" class="dash-ts-prompt" role="dialog" aria-label="Submit Today's Timesheet" style="display:none;">
    <div class="dtp-head">
        <div class="dtp-title-wrap">
            <span class="dtp-icon"><i class="bi bi-check2-circle"></i></span>
            <div>
                <div class="dtp-title" id="dtp-title">Punched Out!</div>
                <div class="dtp-sub" id="dtp-sub">Submit today's timesheet now to avoid tomorrow's morning rush.</div>
            </div>
        </div>
        <button type="button" class="dtp-close" onclick="dismissPostPunchPrompt()" aria-label="Close">&times;</button>
    </div>
    <div class="dtp-task-note" id="dtp-task-note" style="display:none;"></div>
    <div class="dtp-actions">
        <button type="button" class="dtp-btn-later" onclick="dismissPostPunchPrompt()">Later</button>
        <button type="button" class="dtp-btn-fill" onclick="dismissPostPunchPrompt(); openQuickTimesheetDrawer();">
            <i class="bi bi-journal-check"></i> Fill & Submit Timesheet
        </button>
    </div>
</div>

<!-- Quick Timesheet Slide-Over Drawer -->
<div id="quick-ts-backdrop" class="qts-backdrop" onclick="closeQuickTimesheetDrawer()"></div>
<aside id="quick-ts-drawer" class="qts-drawer" role="dialog" aria-modal="true" aria-labelledby="qts-drawer-title" aria-hidden="true">
    <div class="qts-header">
        <div class="qts-header-left">
            <div class="qts-header-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <div class="qts-title-row">
                    <h5 id="qts-drawer-title">Quick Timesheet</h5>
                    <span id="qts-header-badge" class="qts-header-badge">Today</span>
                </div>
                <div id="qts-header-sub" class="qts-header-sub">Log and submit your daily work</div>
            </div>
        </div>
        <div class="qts-header-right">
            <a href="{{ route('worklog') }}" class="qts-fullpage-link" title="Open Full Timesheet Page" target="_self">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
            <button type="button" class="qts-close-btn" onclick="closeQuickTimesheetDrawer()" aria-label="Close drawer">&times;</button>
        </div>
    </div>

    <div class="qts-body">
        <!-- Missing Date / Submitted Banner -->
        <div id="qts-alert-banner" style="display:none;"></div>

        <!-- Date & Entry Type -->
        <div class="qts-section">
            <div class="qts-grid-2">
                <div class="qts-field">
                    <label for="qts-date">Work Date <span>*</span></label>
                    <input type="date" id="qts-date" class="form-control" onchange="qtsOnDateChange()">
                </div>
                <div class="qts-field">
                    <label for="qts-entry-type">Entry Type <span>*</span></label>
                    <select id="qts-entry-type" class="form-select" onchange="qtsRenderProgress()"></select>
                </div>
            </div>
        </div>

        <!-- Today's Assigned Tasks Helper (Collapsible, Non-blocking) -->
        <div id="qts-tasks-box" class="qts-tasks-box" style="display:none;">
            <button type="button" class="qts-tasks-toggle" onclick="qtsToggleTasks()">
                <span class="qts-tasks-title">
                    <i class="bi bi-list-check"></i>
                    Today's Assigned Tasks
                    <span id="qts-tasks-count" class="qts-tasks-count">0</span>
                </span>
                <i id="qts-tasks-chev" class="bi bi-chevron-down"></i>
            </button>
            <div id="qts-tasks-list" class="qts-tasks-list" style="display:none;"></div>
        </div>

        <!-- Compact 1-Line Recent Projects -->
        <div id="qts-recent-wrap" class="qts-recent-wrap" style="display:none;">
            <span class="qts-recent-lbl">Recent</span>
            <div id="qts-recent-chips" class="qts-recent-chips"></div>
        </div>

        <!-- Clean Work Entry Form -->
        <form id="qts-form" class="qts-section" onsubmit="qtsAddEntry(event)">
            <div class="qts-sec-head">
                <span class="qts-sec-title">Add Work Entry</span>
            </div>
            <div class="qts-grid-2">
                <div class="qts-field">
                    <label for="qts-customer">Customer <span>*</span></label>
                    <select id="qts-customer" class="form-select" required>
                        <option value="">Select Customer</option>
                    </select>
                </div>
                <div class="qts-field">
                    <label for="qts-project">Project <span>*</span></label>
                    <select id="qts-project" class="form-select" required>
                        <option value="">Select Project</option>
                    </select>
                </div>
            </div>
            <div class="qts-grid-2">
                <div class="qts-field">
                    <label for="qts-service">Service <span>*</span></label>
                    <select id="qts-service" class="form-select" required>
                        <option value="">Select Service</option>
                    </select>
                </div>
                <div class="qts-field">
                    <label for="qts-module">Module <span>*</span></label>
                    <select id="qts-module" class="form-select" required>
                        <option value="">Select Module</option>
                    </select>
                </div>
            </div>
            <div class="qts-field">
                <div class="qts-time-head">
                    <label>Time Spent <span>*</span></label>
                    <div class="qts-time-presets">
                        <button type="button" onclick="qtsSetPresetTime(0,30)">30m</button>
                        <button type="button" onclick="qtsSetPresetTime(1,0)">1h</button>
                        <button type="button" onclick="qtsSetPresetTime(2,0)">2h</button>
                        <button type="button" onclick="qtsSetPresetTime(4,0)">4h</button>
                    </div>
                </div>
                <div class="qts-grid-2">
                    <div class="qts-unit-field">
                        <input type="number" id="qts-hours" class="form-control" min="0" max="24" value="0" placeholder="0" oninput="qtsRenderProgress()" required>
                        <span class="qts-unit-lbl">hrs</span>
                    </div>
                    <div class="qts-unit-field">
                        <input type="number" id="qts-minutes" class="form-control" min="0" max="59" value="0" placeholder="0" oninput="qtsRenderProgress()" required>
                        <span class="qts-unit-lbl">min</span>
                    </div>
                </div>
            </div>
            <div class="qts-field">
                <label for="qts-description">Work Description <span>*</span></label>
                <textarea id="qts-description" class="form-control qts-textarea" rows="2" maxlength="1000" placeholder="Briefly describe what you worked on..." required></textarea>
            </div>
            <div class="qts-form-actions">
                <button type="submit" id="qts-add-btn" class="qts-add-btn">
                    <i class="bi bi-plus-lg"></i> Add to Draft
                </button>
            </div>
        </form>

        <!-- Draft Entries List -->
        <div class="qts-section qts-section-last">
            <div class="qts-draft-head">
                <span class="qts-sec-title">Draft Entries <span id="qts-draft-count" class="qts-draft-badge">0</span></span>
                <button type="button" id="qts-clear-btn" class="qts-clear-btn" onclick="qtsClearDraft()" style="display:none;">Clear All</button>
            </div>
            <div id="qts-date-mismatch" class="qts-mismatch-banner" style="display:none;"></div>
            <div id="qts-draft-list" class="qts-draft-list">
                <div class="qts-draft-empty">No entries added yet. Add your work above to build today's timesheet.</div>
            </div>
        </div>
    </div>

    <div class="qts-footer">
        <div id="qts-progress-wrap" class="qts-progress-wrap">
            <div class="qts-progress-top">
                <span id="qts-progress-lbl">Logged <strong>0h 0m</strong> / 8h 0m</span>
                <button type="button" id="qts-fill-rem-btn" class="qts-fill-rem-link" onclick="qtsFillRemaining()" style="display:none;">Fill Remaining</button>
            </div>
            <div class="qts-progress-track">
                <div id="qts-progress-fill" class="qts-progress-fill" style="width:0%;"></div>
            </div>
        </div>
        <div id="qts-footer-hint" class="qts-footer-hint"></div>
        <button type="button" id="qts-submit-btn" class="qts-submit-btn" onclick="qtsSubmit()" disabled>
            <i class="bi bi-check2-circle"></i> Submit Timesheet
        </button>
    </div>
</aside>
@endif

<div id="work-flash-box" class="work-flash-box" role="region" aria-label="Today's Work Alerts" style="display:none;">
    <div class="wf-head">
        <div class="wf-head-left">
            <span class="wf-live-dot"></span>
            <span class="wf-kicker" id="wf-kicker">TODAY'S WORK</span>
            <span class="wf-paused-pill" id="wf-paused-pill">Paused</span>
        </div>
        <div class="wf-head-right">
            <div class="wf-nav-group" id="wf-nav-group">
                <button type="button" class="wf-icon-btn" id="wf-prev-btn" onclick="stepWorkFlash(-1)" title="Previous alert" aria-label="Previous alert"><i class="bi bi-chevron-left"></i></button>
                <span class="wf-counter" id="wf-counter">1 / 1</span>
                <button type="button" class="wf-icon-btn" id="wf-next-btn" onclick="stepWorkFlash(1)" title="Next alert" aria-label="Next alert"><i class="bi bi-chevron-right"></i></button>
            </div>
            <div class="wf-snooze-wrap">
                <button type="button" class="wf-hide-btn" id="wf-hide-btn" onclick="toggleWorkFlashSnooze(event)" title="Hide alerts">
                    <i class="bi bi-eye-slash"></i>
                    <span>Hide</span>
                    <i class="bi bi-chevron-down wf-hide-chev"></i>
                </button>
                <div class="wf-snooze-menu" id="wf-snooze-menu" role="menu">
                    <div class="wf-snooze-title">Hide alerts for</div>
                    <button type="button" class="wf-snooze-opt" onclick="snoozeWorkFlash('1h')"><i class="bi bi-clock"></i><span>1 Hour</span></button>
                    <button type="button" class="wf-snooze-opt" onclick="snoozeWorkFlash('3h')"><i class="bi bi-clock"></i><span>3 Hours</span></button>
                    <button type="button" class="wf-snooze-opt" onclick="snoozeWorkFlash('6h')"><i class="bi bi-clock-history"></i><span>6 Hours</span></button>
                    <button type="button" class="wf-snooze-opt wf-snooze-day" onclick="snoozeWorkFlash('day')"><i class="bi bi-moon-stars"></i><span>Whole Day</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="wf-body" id="wf-body"></div>
    <div class="wf-foot">
        <span class="wf-foot-note" id="wf-foot-note">Updates live • Hover to pause</span>
        <button type="button" class="wf-action-btn" id="wf-action-btn"></button>
    </div>
    <div class="wf-progress-track">
        <div class="wf-progress-fill" id="wf-progress-fill"></div>
    </div>
</div>
<button type="button" id="work-flash-restore" class="work-flash-restore" onclick="unhideWorkFlash()" style="display:none;" title="Show Today's Work Alerts">
    <i class="bi bi-bell"></i>
    <span>Alerts (<span id="wf-restore-count">0</span>)</span>
</button>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ file_exists(public_path('css/dashboard.css')) ? filemtime(public_path('css/dashboard.css')) : '1' }}">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
var charts={};
var calY={{ date('Y') }},calM={{ date('n') - 1 }};
var selDs=null;

window.tenantFeatures = {
    isSales: {{ $isSales ? 'true' : 'false' }},
    isCalling: {{ $isCalling ? 'true' : 'false' }},
    isAttendance: {{ $isAttendance ? 'true' : 'false' }},
    isTasks: {{ $isTasks ? 'true' : 'false' }},
    isSubs: {{ $isSubs ? 'true' : 'false' }},
    isPettyCash: {{ $isPettyCash ? 'true' : 'false' }},
    isCalendar: {{ $isCalendar ? 'true' : 'false' }},
    isApproval: {{ ($isApproval && $hasSubordinates) ? 'true' : 'false' }},
    isWorklog: {{ $isWorklog ? 'true' : 'false' }}
};
window.canQuickWorklog = {{ $canQuickWorklog ? 'true' : 'false' }};
window.qtsRoutes = {
    widget: '{{ route("worklog.dashboard-widget") }}',
    customers: '{{ route("worklog.customers") }}',
    projectsByCustomer: '{{ url("/worklog/projects-only/customer") }}',
    servicesByCustomer: '{{ url("/worklog/services/customer") }}',
    modulesByService: '{{ url("/worklog/modules") }}',
    addToSession: '{{ route("worklog.add-to-session") }}',
    removeFromSession: '{{ route("worklog.remove-from-session") }}',
    clearSession: '{{ route("worklog.clear-session") }}',
    submit: '{{ route("worklog.submit") }}',
    saveRemark: '{{ route("task.remark.save") }}'
};
window.showMyAttNotMarked = {{ ($isAttendance && $currentUser && $currentUser->role_id != 1 && (($currentUser->is_attendance ?? 1) != 0)) ? 'true' : 'false' }};
window.currentDashUserId = '{{ $currentUserId ?? "guest" }}';
window.userDashPerms = {
    isAdmin: {{ ($currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3)) ? 'true' : 'false' }},
    sales: {{ ($isSales && $currentUser && ($currentUser->role_id == 1 || $currentUser->role_id == 3 || $currentUser->hasPermissionPrefix('sales.'))) ? 'true' : 'false' }},
    calling: {{ $showCalling ? 'true' : 'false' }},
    tasks: {{ $showTasks ? 'true' : 'false' }},
    calendar: {{ $showCalendar ? 'true' : 'false' }},
    approvals: {{ $canViewApprovals ? 'true' : 'false' }},
    duePayments: {{ $showDuePayments ? 'true' : 'false' }},
    dueSubs: {{ $showDueSubs ? 'true' : 'false' }},
    attendance: {{ ($isAttendance && $currentUser && $currentUser->role_id != 1 && (($currentUser->is_attendance ?? 1) != 0)) ? 'true' : 'false' }}
};
window.workFlashRoutes = {
    tasks: '{{ $taskToMeUrl }}',
    tasksByMe: '{{ $taskByMeUrl }}',
    salesFollowups: '{{ route("todayfollowupstable") }}',
    salesPending: '{{ route("todaypendingtable") }}',
    callingFollowups: '{{ route("calling.todayfollowupstable") }}',
    callingPending: '{{ route("calling.todaypendingtable") }}',
    subscriptions: '{{ route("subscriptions.index") }}',
    attendance: '{{ Route::has("attendance") ? route("attendance") : "#" }}'
};
window.dashboardTaskUrls = {
    toMe: '{{ $taskToMeUrl }}',
    byMe: '{{ $taskByMeUrl }}',
    all: '{{ $taskViewAllUrl }}'
};
window.dashboardTaskStore = {};
window.dashboardTaskStatuses = null;
window.activeQuickTask = null;
window.workFlashData = {
    salesFollowups: 0,
    salesPending: 0,
    callingFollowups: 0,
    callingPending: 0,
    tasksToMe: { count: 0, topTitle: '' },
    tasksByMe: { count: 0, topTitle: '' },
    todayFlyers: { ds: '', items: [] },
    approvals: { total: 0, summary: '', firstType: 'ts' },
    duePayments: { count: 0, totalDue: 0 },
    dueSubs: { count: 0, topName: '' },
    attendancePending: false
};

var smmData={};

var MONTHS=['January','February','March','April','May','June','July','August','September','October','November','December'];

function renderCal(y,m){
  var mlEl = document.getElementById('cal-ml');
  if(!mlEl) return;
  mlEl.textContent=MONTHS[m]+' '+y;
  var firstDay=new Date(y,m,1).getDay();
  var days=new Date(y,m+1,0).getDate();
  var todayRef=new Date();
  todayRef.setHours(0,0,0,0);
  var html='';
  for(var i=0;i<firstDay;i++) html+='<div class="cal-day empty"></div>';
  for(var d=1;d<=days;d++){
    var ds=y+'-'+String(m+1).padStart(2,'0')+'-'+String(d).padStart(2,'0');
    var cd=new Date(y,m,d);
    var isToday=(y===todayRef.getFullYear()&&m===todayRef.getMonth()&&d===todayRef.getDate());
    var isPast=cd<todayRef;
    var data=smmData[ds];
    var dots='';
    if(data){
      var dc=[];
      if(isToday){
        if((data.done||[]).length) dc.push('#10B981');
        if((data.rem||[]).length) dc.push('#F59E0B');
        if((data.canc||[]).length) dc.push('#94A3B8');
      } else if(isPast){
        if((data.done||[]).length) dc.push('#10B981');
        if((data.missed||[]).length) dc.push('#EF4444');
        if((data.canc||[]).length) dc.push('#94A3B8');
      } else {
        if((data.sch||[]).length) dc.push('#434AFA');
        if((data.resch||[]).length) dc.push('#8B5CF6');
        if((data.canc||[]).length) dc.push('#94A3B8');
      }
      var dotInner=isToday?dc.map(()=>'<div class="cal-dot" style="background:rgba(255,255,255,0.85)"></div>').join(''):dc.map(c=>'<div class="cal-dot" style="background:'+c+'"></div>').join('');
      dots='<div class="cal-dots">'+dotInner+'</div>';
    }
    var cls='cal-day'+(isToday?' today':'')+(ds===selDs?' sel-day':'');
    html+='<div class="'+cls+'" id="cd-'+ds+'" data-cal-date="'+ds+'" onclick="pickDate(\''+ds+'\')">'+(isToday?'<b>'+d+'</b>':d)+dots+'</div>';
  }
  document.getElementById('cal-grid').innerHTML=html;
}

function prevM(){if(typeof hideCalPopover==='function')hideCalPopover(true);calM--;if(calM<0){calM=11;calY--;}loadCalendarData(calY,calM);selDs=null;}
function nextM(){if(typeof hideCalPopover==='function')hideCalPopover(true);calM++;if(calM>11){calM=0;calY++;}loadCalendarData(calY,calM);selDs=null;}

function platHtml(p){
  var n=p==='ig'?'Instagram':p==='fb'?'Facebook':'LinkedIn';
  return '<span class="cplat '+escHtml(p)+'">'+n+'</span>';
}
function entryRow(e,extra){
  var note=e.r?'<span class="cnote">'+escHtml(e.r)+'</span>':'';
  var td=extra?'style="text-decoration:line-through;opacity:0.65"':'';
  return '<div class="cal-entry">'+platHtml(e.p)+'<span class="cclient" '+td+'>'+escHtml(e.c||'')+'</span>'+note+'</div>';
}
function calStatusSection(items,title,colorStyle,isStrike){
  if(!items||!items.length) return '';
  var st=colorStyle?' style="'+colorStyle+'"':'';
  return '<div class="cal-sec"><div class="cal-sec-title"'+st+'>'+title+'</div>'+items.map(function(e){return entryRow(e,isStrike);}).join('')+'</div>';
}

function buildCalDateHtml(ds,isCompact){
  var data=smmData[ds];
  var d=new Date(ds+'T00:00:00');
  var dLabel=d.toLocaleDateString('en-IN',{weekday:'short',day:'numeric',month:'long',year:'numeric'});
  var headHtml=isCompact
    ?'<div class="cal-pop-date">📅 '+escHtml(dLabel)+'</div>'
    :'<div class="cal-detail-date" style="font-weight:700; margin-bottom:10px;">📅 '+escHtml(dLabel)+'</div>';

  if(!data){
    var emptyHtml='<div class="cal-empty-msg">No posts scheduled on this date.</div>';
    return isCompact?(headHtml+'<div class="cal-pop-body">'+emptyHtml+'</div>'):(headHtml+emptyHtml);
  }

  var pills='';
  var body='';
  if(data.t==='today'){
    if((data.done||[]).length) pills+='<span class="cpill done">✓ '+(data.done.length)+' Posted</span>';
    if((data.rem||[]).length) pills+='<span class="cpill rem">⏳ '+(data.rem.length)+' Remaining</span>';
    if((data.canc||[]).length) pills+='<span class="cpill canc">✕ '+(data.canc.length)+' Cancelled</span>';
    body+=calStatusSection(data.done,'Posted','color:#047857',false);
    body+=calStatusSection(data.rem,'Remaining','color:#B45309',false);
    body+=calStatusSection(data.canc,'Cancelled','',true);
  } else if(data.t==='past'){
    if((data.done||[]).length) pills+='<span class="cpill done">✓ '+(data.done.length)+' Posted</span>';
    if((data.missed||[]).length) pills+='<span class="cpill miss">✗ '+(data.missed.length)+' Missed</span>';
    if((data.canc||[]).length) pills+='<span class="cpill canc">↩ '+(data.canc.length)+' Cancelled</span>';
    body+=calStatusSection(data.done,'Posted','color:#047857',false);
    body+=calStatusSection(data.missed,'Missed','color:#B91C1C',false);
    body+=calStatusSection(data.canc,'Cancelled / Rescheduled','',true);
  } else {
    if((data.sch||[]).length) pills+='<span class="cpill sch">📅 '+(data.sch.length)+' Scheduled</span>';
    if((data.resch||[]).length) pills+='<span class="cpill resch">↩ '+(data.resch.length)+' Rescheduled</span>';
    if((data.canc||[]).length) pills+='<span class="cpill canc">✕ '+(data.canc.length)+' Cancelled</span>';
    body+=calStatusSection(data.sch,'Scheduled','color:#1D4ED8',false);
    body+=calStatusSection(data.resch,'Rescheduled','color:#6D28D9',false);
    body+=calStatusSection(data.canc,'Cancelled','',true);
  }

  var contentHtml='<div class="cal-pills">'+pills+'</div>'+body;
  if(isCompact){
    return headHtml+'<div class="cal-pop-body">'+contentHtml+'</div><div class="cal-pop-hint">Click date for full view</div>';
  }
  return headHtml+contentHtml;
}

function pickDate(ds){
  if(typeof hideCalPopover==='function') hideCalPopover(true);
  if(selDs){var prev=document.getElementById('cd-'+selDs);if(prev){prev.classList.remove('sel-day');}}
  selDs=ds;
  var el=document.getElementById('cd-'+ds);
  if(el) el.classList.add('sel-day');
  var det=document.getElementById('cal-detail-modal');
  det.innerHTML=buildCalDateHtml(ds,false);
  document.getElementById('modal-cal').style.display='flex';
}

function closeModal(){document.getElementById('modal-cal').style.display='none';}

function openApprovalModal(tab) {
    if (typeof hideApPopover === 'function') hideApPopover(true);
    const modal = document.getElementById('modal-approvals');
    if (!modal) return;
    const btn = modal.querySelector('.subtab[data-ap-tab="' + tab + '"]');
    if (btn) swApprovalTab(tab, btn);
    modal.style.display = 'flex';
}

function closeApprovalModal() {
    const modal = document.getElementById('modal-approvals');
    if (modal) modal.style.display = 'none';
}

function swTaskTab(tab,btn){
  btn.parentElement.querySelectorAll('.subtab').forEach(t=>t.classList.remove('act'));btn.classList.add('act');
  btn.parentElement.parentElement.querySelectorAll('.tk-cnt').forEach(c=>c.classList.remove('act'));
  document.getElementById('tk-'+tab).classList.add('act');
}

function swApprovalTab(tab,btn){
  btn.parentElement.querySelectorAll('.subtab').forEach(t=>t.classList.remove('act'));btn.classList.add('act');
  btn.parentElement.parentElement.querySelectorAll('.ap-cnt').forEach(c=>c.classList.remove('act'));
  document.getElementById('ap-'+tab).classList.add('act');
}

function getApprovalRoute(type) {
    var routes = {
        ts: '{{ route("worklog-approvals") }}',
        at: '{{ route("attendance.approval") }}',
        tk: '{{ route("all-tasks.index") }}',
        lv: '{{ route("leave.approvals") }}',
        pc: '{{ route("approvals.petty") }}'
    };
    return routes[type] || '#';
}

function viewAllApprovals() {
    const activeCnt = document.querySelector('.ap-cnt.act');
    if (!activeCnt) return;
    const type = activeCnt.id ? activeCnt.id.replace('ap-', '') : '';
    const targetRoute = getApprovalRoute(type);

    if (targetRoute && targetRoute !== '#') {
        location.href = targetRoute;
    }
}

function selectPCPeriod(val, label, el) {
    var inputEl = document.getElementById('pc-period');
    var labelEl = document.getElementById('pc-period-label');
    if (inputEl) inputEl.value = val;
    if (labelEl) labelEl.textContent = label;
    if (el && el.closest('.pc-dd-menu')) {
        el.closest('.pc-dd-menu').querySelectorAll('.pc-dd-item').forEach(function (item) {
            item.classList.remove('act');
        });
        el.classList.add('act');
    }
    updatePC();
}

function updatePC(){
    var periodEl = document.getElementById('pc-period');
    if (!periodEl) return;
    var period = periodEl.value;
    $.ajax({
        url: '/petty-cash/summary?period=' + period,
        method: 'GET',
        success: function(res) {
            $('#pc-open').text('₹' + res.total_opening_balance.toLocaleString());
            $('#pc-used').text('₹' + res.total_expense.toLocaleString());
            $('#pc-rem').text('₹' + res.remaining_balance.toLocaleString());
        }
    });
}

function hideChartTooltip() {
    var tip = document.getElementById('chart-hover-tooltip');
    if (!tip) return;
    tip.classList.remove('show');
    tip.setAttribute('aria-hidden', 'true');
}

function renderDonutExternalTooltip(context) {
    var chart = context && context.chart;
    var tooltip = context && context.tooltip;
    var tip = document.getElementById('chart-hover-tooltip');
    if (!tip || !chart || !tooltip || tooltip.opacity === 0 || !tooltip.dataPoints || !tooltip.dataPoints.length) {
        hideChartTooltip();
        return;
    }

    var dp = tooltip.dataPoints[0];
    var labelsArr = (chart.data && Array.isArray(chart.data.labels)) ? chart.data.labels : [];
    var label = dp.label || labelsArr[dp.dataIndex] || 'Source';
    var rawVal = Number(dp.raw ?? dp.parsed ?? 0) || 0;
    var dataArr = (dp.dataset && Array.isArray(dp.dataset.data)) ? dp.dataset.data : [];
    var total = dataArr.reduce(function (sum, v) { return sum + (Number(v) || 0); }, 0);
    var pct = total > 0 ? Math.round((rawVal / total) * 100) : 0;
    var bgColors = dp.dataset && dp.dataset.backgroundColor;
    var dotColor = Array.isArray(bgColors) ? (bgColors[dp.dataIndex] || '#434AFA') : (bgColors || '#434AFA');

    tip.innerHTML =
        '<div class="cht-row">' +
            '<span class="cht-dot" style="background:' + escHtml(dotColor) + '"></span>' +
            '<span class="cht-label">' + escHtml(label) + '</span>' +
        '</div>' +
        '<div class="cht-meta">' +
            '<span><strong>' + rawVal + '</strong> Leads</span>' +
            '<span class="cht-pct">' + pct + '%</span>' +
        '</div>';

    tip.classList.add('show');
    tip.setAttribute('aria-hidden', 'false');

    var canvasRect = chart.canvas.getBoundingClientRect();
    var caretX = canvasRect.left + (Number(tooltip.caretX) || (canvasRect.width / 2));
    var caretY = canvasRect.top + (Number(tooltip.caretY) || (canvasRect.height / 2));
    var popW = tip.offsetWidth || 168;
    var popH = tip.offsetHeight || 56;
    var left = Math.max(12, Math.min(caretX - (popW / 2), window.innerWidth - popW - 12));
    var top = (caretY - popH - 12 >= 12)
        ? (caretY - popH - 12)
        : Math.min(caretY + 14, window.innerHeight - popH - 12);

    tip.style.left = Math.round(left) + 'px';
    tip.style.top = Math.round(top) + 'px';
}

function drawDonut(id,data,colors,labels){
    var ctx=document.getElementById(id);
    if(!ctx)return;
    hideChartTooltip();
    if(charts[id]) charts[id].destroy();
    charts[id]=new Chart(ctx,{
        type:'doughnut',
        data:{
            labels: labels || [],
            datasets:[{
                data:data,
                backgroundColor:colors,
                borderWidth:3,
                borderColor:'#ffffff',
                borderRadius:5
            }]
        },
        options:{
            cutout:'75%',
            plugins:{
                legend:{display:false},
                tooltip:{
                    enabled:false,
                    external:renderDonutExternalTooltip
                }
            },
            animation:{duration:650}
        }
    });
}

window.isTeamView = false;

function sw(role,btn){
  document.querySelectorAll('.rtab').forEach(t=>t.classList.remove('act'));btn.classList.add('act');
  window.isTeamView = (role === 'sales');

  const titleEl = document.querySelector('.wbar h2');
  if (titleEl) {
      titleEl.innerHTML = window.isTeamView ? 'Welcome back, Team Viewer! 👋' : 'Welcome back, {{ $firstName }}! 👋';
  }

  const subtitleEl = document.querySelector('.wbar p');
  if (subtitleEl) {
      subtitleEl.innerHTML = window.isTeamView ? "Here's what's happening with your team today." : "Here's what's happening with your projects today.";
  }

  loadDashboardMetrics();
}

function loadLeadSourceDonut(canvasId, totalId) {
    var qp = window.isTeamView ? '?team=1' : '';
    $.ajax({
        url: '/lead-source/data' + qp,
        method: 'GET',
        success: function(response) {
            const el = document.getElementById(totalId);
            if(!response || !response.length) {
                if (el) el.textContent = '0';
                const legEl = document.getElementById(canvasId === 'dc1' ? 'lead-source-leg' : 'lead-source-leg-sales');
                if(legEl) legEl.innerHTML = '<li class="dash-empty" style="padding:12px;"><span>No data available</span></li>';
                if (charts[canvasId]) {
                    charts[canvasId].destroy();
                    delete charts[canvasId];
                }
                return;
            }
            const labels = response.map(r => r.label);
            const values = response.map(r => r.value);
            const total = values.reduce((a, b) => a + b, 0);
            if (el) el.textContent = total;

            const colors = ['#434AFA','#10B981','#8B5CF6','#F97316','#0EA5E9','#EC4899','#6366f1','#EF4444'];
            drawDonut(canvasId, values, values.map((_, i) => colors[i % colors.length]), labels);

            let legHtml = '';
            response.forEach((r, i) => {
                const percent = total > 0 ? Math.round((r.value / total) * 100) : 0;
                legHtml += `<li class="li"><span class="ld" style="background:${colors[i % colors.length]}"></span><span>${r.label}</span><span class="lp">${percent}%</span></li>`;
            });
            const legEl = document.getElementById(canvasId === 'dc1' ? 'lead-source-leg' : 'lead-source-leg-sales');
            if(legEl) legEl.innerHTML = legHtml;
        }
    });
}

function tick(){var n=new Date(),t=n.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true}),ce=document.getElementById('clk-display');if(ce)ce.textContent=t;}

window.callingChartVals = { followups: 0, underprocess: 0, completed: 0, pending: 0, newf: 0 };

function drawCallingChart() {
    var ctx = document.getElementById('callingChart');
    if (!ctx) return;
    var c2d = ctx.getContext('2d');
    var grad = c2d.createLinearGradient(0, 0, 0, 155);
    grad.addColorStop(0, 'rgba(124, 58, 237, 0.24)');
    grad.addColorStop(1, 'rgba(124, 58, 237, 0.01)');

    var v = window.callingChartVals;
    var series = [v.followups, v.underprocess, v.completed, v.pending, v.newf];

    if (charts['callingChart']) charts['callingChart'].destroy();
    charts['callingChart'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Followups', 'In Process', 'Completed', 'Pending', 'New'],
            datasets: [{
                label: 'Calling Activity',
                data: series,
                borderColor: '#7C3AED',
                backgroundColor: grad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.42,
                pointRadius: 3.5,
                pointHoverRadius: 5.5,
                pointBackgroundColor: '#7C3AED',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { color: '#71717A', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: 10,
                    grid: { color: '#F1F5F9', tickLength: 0 },
                    border: { display: false },
                    ticks: { color: '#94A3B8', font: { size: 10.5 }, precision: 0 }
                }
            },
            animation: { duration: 450 }
        }
    });
}

function drawAttendanceDonut(present, absent) {
    var total = (Number(present) || 0) + (Number(absent) || 0);
    var pct = total > 0 ? Math.round(((Number(present) || 0) / total) * 100) : 0;
    var pctEl = document.getElementById('att-pct');
    if (pctEl) pctEl.textContent = pct + '%';

    var ctx = document.getElementById('attDonut');
    if (!ctx) return;
    var vals = total > 0 ? [Number(present) || 0, Number(absent) || 0] : [0, 1];
    var colors = total > 0 ? ['#10B981', '#D1FAE5'] : ['#A7F3D0', '#ECFDF5'];

    if (charts['attDonut']) charts['attDonut'].destroy();
    charts['attDonut'] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: vals,
                backgroundColor: colors,
                borderWidth: 0,
                borderRadius: 6
            }]
        },
        options: {
            cutout: '76%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            animation: { duration: 500 }
        }
    });
}

function fetchCallingMetric(url, elementId, key, chartKey) {
    var qp = window.isTeamView ? (url.indexOf('?') !== -1 ? '&team=1' : '?team=1') : '';
    $.ajax({
        url: url + qp,
        method: 'GET',
        success: function (response) {
            const value = Number(response && response[key]) || 0;
            const el = document.getElementById(elementId);
            if (el) el.textContent = value;
            window.callingChartVals[chartKey] = value;
            drawCallingChart();
            if (elementId === 'c_todayfollowups') window.workFlashData.callingFollowups = value;
            if (elementId === 'c_todaypending') window.workFlashData.callingPending = value;
            if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
        },
        error: function () {
            const el = document.getElementById(elementId);
            if (el) el.textContent = '0';
            window.callingChartVals[chartKey] = 0;
            drawCallingChart();
            if (elementId === 'c_todayfollowups') window.workFlashData.callingFollowups = 0;
            if (elementId === 'c_todaypending') window.workFlashData.callingPending = 0;
            if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
        }
    });
}

function fetchMetric(url, elementId, key) {
    var qp = window.isTeamView ? (url.indexOf('?') !== -1 ? '&team=1' : '?team=1') : '';
    $.ajax({
        url: url + qp,
        method: 'GET',
        success: function (response) {
            const value = Number(response && response[key]) || 0;
            const el = document.getElementById(elementId);
            if (el) el.textContent = value;
            if (elementId === 'todayfollowups') window.workFlashData.salesFollowups = value;
            if (elementId === 'todaypending') window.workFlashData.salesPending = value;
            if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
        },
        error: function () {
            const el = document.getElementById(elementId);
            if (el) el.textContent = '0';
            if (elementId === 'todayfollowups') window.workFlashData.salesFollowups = 0;
            if (elementId === 'todaypending') window.workFlashData.salesPending = 0;
            if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
        }
    });
}

function loadDashboardMetrics() {
    const f = window.tenantFeatures;
    const qp = window.isTeamView ? '?team=1' : '';
    const qpAmp = window.isTeamView ? '&team=1' : '';

    // Sales Intelligence
    if (f.isSales) {
        fetchMetric('/todayfollowups', 'todayfollowups', 'totalLeads');
        fetchMetric('/underprocess', 'underprocess', 'underprocess');
        fetchMetric('/todaycompleted', 'todaycompleted', 'todaycompleted');
        fetchMetric('/todaypending', 'todaypending', 'todaypending');
        fetchMetric('/todaynew', 'todaynew', 'todaynew');
        fetchMetric('/allleads', 'allleads', 'allleads');
    }

    // Tele-Calling Status
    if (f.isCalling) {
        fetchCallingMetric('/calling/todayfollowups', 'c_todayfollowups', 'totalLeads', 'followups');
        fetchCallingMetric('/calling/underprocess', 'c_underprocess', 'underprocess', 'underprocess');
        fetchCallingMetric('/calling/todaycompleted', 'c_todaycompleted', 'todaycompleted', 'completed');
        fetchCallingMetric('/calling/todaypending', 'c_todaypending', 'todaypending', 'pending');
        fetchCallingMetric('/calling/todaynew', 'c_todaynew', 'todaynew', 'newf');
        fetchMetric('/calling/allleads', 'c_allleads', 'allleads');
    }

    // Team Attendance
    if (f.isAttendance) {
        $.ajax({
            url: '/attendance/summary' + qp,
            method: 'GET',
            success: function(res) {
                $('#att-present').text(res.present);
                $('#att-absent').text(res.absent);
                $('#att-office').text(res.office);
                $('#att-remote').text(res.remote);
                $('#att-field').text(res.field);
                $('#att-wfh').text(res.wfh);

                drawAttendanceDonut(res.present, res.absent);

                window.attNamesData.present = Array.isArray(res.present_names) ? res.present_names : [];
                window.attNamesData.absent = Array.isArray(res.absent_names) ? res.absent_names : [];
                $('#att-present-block, #att-absent-block').removeAttr('title');
                renderMyAttendanceBadge(res.my_attendance || null);
                window.workFlashData.attendancePending = !(res && res.my_attendance && res.my_attendance.marked);
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            },
            error: function() {
                var badgeEl = document.getElementById('my-att-badge');
                if (badgeEl && !badgeEl.dataset.rendered && window.showMyAttNotMarked) {
                    renderMyAttendanceBadge({ marked: false });
                }
                if (window.userDashPerms && window.userDashPerms.attendance) {
                    window.workFlashData.attendancePending = true;
                    if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
                }
            }
        });

        // Holidays
        $.ajax({
            url: '/holidays/summary' + qp,
            method: 'GET',
            success: function(res) {
                renderHolidays(res.list || []);
            }
        });
    }

    // Petty Cash
    if (f.isPettyCash) {
        updatePC();
    }

    // Upcoming Leaves
    if (f.isPettyCash || f.isAttendance) {
        $.ajax({
            url: '/upcoming-leaves/summary' + qp,
            method: 'GET',
            success: function(res) {
                renderUpcomingLeaves(res.list || [], res.balances || [], res.earned_leave_days);
            }
        });
    }

    // Pending Approvals
    if (f.isApproval) {
        $.ajax({
            url: '/pending-approvals/summary' + qp,
            method: 'GET',
            success: function(res) {
                if (f.isAttendance) {
                    $('#ap-at-badge').text(res.attendance);
                    $('.ap-modal-badge[data-ap="at"]').text(res.attendance);
                }
                if (f.isAttendance) {
                    $('#ap-lv-badge').text(res.leave);
                    $('.ap-modal-badge[data-ap="lv"]').text(res.leave);
                }
                if (f.isPettyCash) {
                    $('#ap-pc-badge').text(res.petty_cash);
                    $('.ap-modal-badge[data-ap="pc"]').text(res.petty_cash);
                }
                if (f.isWorklog) {
                    $('#ap-ts-badge').text(res.timesheet || 0);
                    $('.ap-modal-badge[data-ap="ts"]').text(res.timesheet || 0);
                }
                if (f.isTasks) {
                    $('#ap-tk-badge').text(res.task || 0);
                    $('.ap-modal-badge[data-ap="tk"]').text(res.task || 0);
                }

                var lists = res.lists || {};
                window.approvalPreviewData = {
                    ts: { count: Number(res.timesheet) || 0, list: Array.isArray(lists.timesheet) ? lists.timesheet : [] },
                    at: { count: Number(res.attendance) || 0, list: Array.isArray(lists.attendance) ? lists.attendance : [] },
                    tk: { count: Number(res.task) || 0, list: Array.isArray(lists.task) ? lists.task : [] },
                    lv: { count: Number(res.leave) || 0, list: Array.isArray(lists.leave) ? lists.leave : [] },
                    pc: { count: Number(res.petty_cash) || 0, list: Array.isArray(lists.petty_cash) ? lists.petty_cash : [] }
                };

                var apTotal = 0;
                var apParts = [];
                var firstApType = 'ts';
                var foundFirstAp = false;
                [
                    ['ts', f.isWorklog, Number(res.timesheet) || 0, 'Timesheet'],
                    ['at', f.isAttendance, Number(res.attendance) || 0, 'Attendance'],
                    ['tk', f.isTasks, Number(res.task) || 0, 'Task'],
                    ['lv', f.isAttendance, Number(res.leave) || 0, 'Leave'],
                    ['pc', f.isPettyCash, Number(res.petty_cash) || 0, 'Petty Cash']
                ].forEach(function (row) {
                    if (row[1]) {
                        apTotal += row[2];
                        if (row[2] > 0) {
                            if (!foundFirstAp) {
                                firstApType = row[0];
                                foundFirstAp = true;
                            }
                            apParts.push(row[2].toLocaleString('en-IN') + ' ' + row[3]);
                        }
                    }
                });
                window.workFlashData.approvals = {
                    total: apTotal,
                    summary: apParts.slice(0, 3).join(' • '),
                    firstType: firstApType
                };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();

                if (res.lists) {
                    if (f.isAttendance) renderApprovals('at', res.lists.attendance);
                    if (f.isAttendance) renderApprovals('lv', res.lists.leave);
                    if (f.isPettyCash) renderApprovals('pc', res.lists.petty_cash);
                    if (f.isWorklog) renderApprovals('ts', res.lists.timesheet);
                    if (f.isTasks) renderApprovals('tk', res.lists.task);
                }

                var activeApPop = document.getElementById('ap-hover-popover');
                if (activeApPop && activeApPop.classList.contains('show') && window.activeApPopoverType && window.activeApPopoverAnchor) {
                    showApPopover(window.activeApPopoverType, window.activeApPopoverAnchor);
                }
            }
        });
    }

    // Due Payments
    if (f.isSales) {
        $.ajax({
            url: '/due-payments/summary' + qp,
            method: 'GET',
            success: function(res) {
                var payList = res.list || [];
                renderPayments(payList, res.total_due || 0, res.count || 0);
                window.workFlashData.duePayments = {
                    count: Number(res.count || payList.length) || 0,
                    totalDue: Number(res.total_due) || 0
                };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            }
        });
    }

    // Due Subscriptions
    if (f.isSubs) {
        $.ajax({
            url: '/due-subscriptions/summary' + qp,
            method: 'GET',
            success: function(res) {
                var subList = res.list || [];
                renderSubscriptions(subList);
                window.workFlashData.dueSubs = {
                    count: subList.length,
                    topName: (subList[0] && subList[0].customer_name) ? subList[0].customer_name : ''
                };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            }
        });
    }

    // Sales View Specific
    if (f.isSales) {
        $.ajax({
            url: '/todayfollowups' + qp,
            method: 'GET',
            success: function(res) {
                $('#s_todayfollowups').text(res.totalLeads || 0);
                renderFollowups(res.leads || []);
                if (res.pipeline) {
                    res.pipeline.forEach(p => {
                        const id = 'pipe-' + p.status_name.toLowerCase().replace(/\s+/g, '');
                        const el = document.getElementById(id);
                        if (el) el.textContent = p.total;
                    });
                }
            }
        });
        fetchMetric('/todaypending', 's_missed', 'todaypending');
        fetchMetric('/todaycompleted', 's_conversions', 'todaycompleted');

        loadLeadSourceDonut('dc1', 'total-leads-donut');
    }

    // Celebrations
    $.ajax({
        url: '/celebrations/summary' + qp,
        method: 'GET',
        success: function(res) {
            renderCelebrations(res.list || []);
        }
    });

    // Calendar
    if (f.isCalendar) {
        loadCalendarData(calY, calM);
    }

    // Tasks
    if (f.isTasks) {
        $.ajax({
            url: '/user-tasks?type=byMe' + qpAmp,
            method: 'GET',
            success: function(res) {
                var byList = res.data || [];
                renderTasks('tk-byMe', byList);
                window.workFlashData.tasksByMe = {
                    count: byList.length,
                    topTitle: (byList[0] && byList[0].task_name) ? byList[0].task_name : ''
                };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            }
        });
        $.ajax({
            url: '/user-tasks?type=toMe' + qpAmp,
            method: 'GET',
            success: function(res) {
                var toList = res.data || [];
                renderTasks('tk-toMe', toList);
                window.workFlashData.tasksToMe = {
                    count: toList.length,
                    topTitle: (toList[0] && toList[0].task_name) ? toList[0].task_name : ''
                };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            }
        });
    }
}

function loadCalendarData(y, m) {
    const from = `${y}-${String(m + 1).padStart(2, '0')}-01`;
    const to = `${y}-${String(m + 1).padStart(2, '0')}-${new Date(y, m + 1, 0).getDate()}`;
    $.ajax({
        url: `/calendar/grid?from=${from}&to=${to}`,
        method: 'GET',
        success: function(res) {
            smmData = {};
            const todayStr = new Date().toISOString().split('T')[0];
            (Array.isArray(res) ? res : []).forEach(item => {
                const ds = item.event_date;
                if (!smmData[ds]) {
                    const isPast = new Date(ds) < new Date(todayStr);
                    const isToday = ds === todayStr;
                    smmData[ds] = { t: isToday ? 'today' : (isPast ? 'past' : 'future'), sch: [], done: [], missed: [], canc: [], rem: [] };
                }
                if (smmData[ds].t === 'future') smmData[ds].sch.push({ c: item.title, p: 'ig' });
                else if (smmData[ds].t === 'today') smmData[ds].rem.push({ c: item.title, p: 'ig' });
                else smmData[ds].missed.push({ c: item.title, p: 'ig' });
            });
            var nowObj = new Date();
            var nowDs = nowObj.getFullYear() + '-' + String(nowObj.getMonth() + 1).padStart(2, '0') + '-' + String(nowObj.getDate()).padStart(2, '0');
            if (y === nowObj.getFullYear() && m === nowObj.getMonth()) {
                var todayFlyerItems = (Array.isArray(res) ? res : []).filter(function (it) {
                    return it && it.event_date === nowDs;
                });
                window.workFlashData.todayFlyers = { ds: nowDs, items: todayFlyerItems };
                if (typeof scheduleWorkFlashRefresh === 'function') scheduleWorkFlashRefresh();
            }
            renderCal(y, m);
        }
    });
}

function renderUpcomingLeaves(list, balances, earnedDays) {
    const cont = document.getElementById('upcoming-leaves-list');
    if (!cont) return;
    const hasList = Array.isArray(list) && list.length > 0;

    // Filter out LWP (Leave Without Pay / Loss of Pay)
    const validBalances = (Array.isArray(balances) ? balances : []).filter(function (b) {
        var n = (b.name || '').toLowerCase();
        var c = (b.code || '').toLowerCase();
        return !n.includes('lwp') && !n.includes('without pay') && !n.includes('loss of pay') && c !== 'lwp';
    });
    window.dashLeaveBalances = validBalances;
    if (earnedDays !== undefined) {
        window.dashEarnedLeaveDays = earnedDays;
    }

    const hasBalances = validBalances.length > 0;

    if (!hasList && !hasBalances) {
        cont.innerHTML = '<div class="dash-empty" style="padding:16px 8px;"><div class="dash-empty-circle"><i class="bi bi-calendar2-event"></i></div><span>No upcoming leaves</span></div>';
        return;
    }

    let html = '';

    if (hasBalances) {
        html += '<div class="leave-balances-box">';
        html += '<div class="leave-bal-title">Available Leaves</div>';
        html += '<div class="leave-bal-grid">';
        validBalances.forEach(function (b, idx) {
            const valLabel = b.is_unlimited
                ? '∞'
                : (b.quota_type === 'monthly' ? `${b.balance}/mo` : `${b.balance}`);
            html += `<div class="leave-bal-chip" data-leave-idx="${idx}">
                <span class="leave-bal-name">${escHtml(b.name)}</span>
                <span class="leave-bal-val">${escHtml(valLabel)}</span>
            </div>`;
        });
        html += '</div></div>';
    }

    if (hasList) {
        html += '<div class="leave-upcoming-sec">';
        if (hasBalances) {
            html += '<div class="leave-bal-title">Upcoming</div>';
        }
        list.slice(0, 6).forEach(item => {
            const start = new Date(item.start_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
            const end = new Date(item.end_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
            html += `<div class="leave-row">
                <div style="min-width:0;">
                    <div class="tnm">${escHtml(item.user_name)}</div>
                    <div class="tdue">${escHtml(item.leave_type_name)} • ${start} - ${end}</div>
                </div>
                <span class="sc-pill p-slate">${item.total_days} d</span>
            </div>`;
        });
        html += '</div>';
    } else {
        html += '<div class="leave-empty-mini"><i class="bi bi-calendar2-check"></i><span>No upcoming leaves scheduled</span></div>';
    }

    cont.innerHTML = html;
}

function renderPayments(list, totalDue = 0, totalCount = 0) {
    const cont = document.getElementById('due-payments-list');
    if (!cont) return;
    if (!list.length) {
        cont.innerHTML = '<div class="dash-empty"><i class="bi bi-credit-card"></i><span>No pending payments</span></div>';
        $('#overdue-badge').hide();
        return;
    }
    if (!totalDue) {
        totalDue = list.reduce((sum, p) => sum + parseFloat(p.remaining || 0), 0);
    }
    const badgeCount = totalCount || list.length;
    $('#overdue-badge').text(badgeCount + ' Pending (₹' + Number(totalDue).toLocaleString('en-IN') + ')').show();
    let html = '';
    list.forEach(p => {
        const due = new Date(p.due_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const isSub = p.source_type === 'subscription';
        const badgeClass = isSub ? 'sub' : 'inv';
        const badgeText = isSub ? 'Subscription' : 'Invoice';

        html += `<div class="pdi">
            <div class="pdi-top">
                <div class="pdi-left">
                    <div class="dash-row-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="pdi-nm">${p.customer_name || 'Customer'} <span class="pdi-badge ${badgeClass}">${badgeText}</span></div>
                        <div class="pdi-due">Due ${due}</div>
                    </div>
                </div>
                <div class="pdi-amt">₹${Number(p.remaining).toLocaleString('en-IN')}</div>
            </div>
        </div>`;
    });
    cont.innerHTML = html;
}

function renderSubscriptions(list) {
    const cont = document.getElementById('due-subscriptions-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div class="dash-empty"><i class="bi bi-arrow-repeat"></i><span>No active subscriptions</span></div>';
        return;
    }
    let html = '';
    list.forEach(s => {
        const due = new Date(s.due_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        html += `<div class="pdi">
            <div class="pdi-top">
                <div class="pdi-left">
                    <div class="dash-row-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div>
                        <div class="pdi-nm">${s.customer_name || 'Customer'}</div>
                        <div class="pdi-due">${s.product_name || s.subscription_name || 'Product'} • Due ${due}</div>
                    </div>
                </div>
                <div class="pdi-amt">₹${Number(s.amount).toLocaleString('en-IN')}</div>
            </div>
        </div>`;
    });
    cont.innerHTML = html;
}

function renderCelebrations(list) {
    const cont = document.getElementById('celebrations-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div class="dash-empty" style="padding:16px 8px;"><div class="dash-empty-circle"><i class="bi bi-stars"></i></div><span>No upcoming celebrations</span></div>';
        return;
    }

    const resolveMeta = (c) => {
        let icon = '🎉', color = '#8B5CF6', pillBg = '#F5F3FF', labelColor = '#6D28D9';
        if (c.type === 'birthday') {
            icon = '🎂'; color = '#EC4899'; pillBg = '#FCE7F3'; labelColor = '#BE185D';
        } else if (c.type === 'work_anniversary') {
            icon = '💼'; color = '#434AFA'; pillBg = '#EEF2FF'; labelColor = '#434AFA';
        } else if (c.type === 'anniversary') {
            icon = '💍'; color = '#F43F5E'; pillBg = '#FFE4E6'; labelColor = '#BE123C';
        }
        return { icon, color, pillBg, labelColor };
    };

    const visibleList = list.slice(0, 6);
    window.dashCelebrationsList = visibleList;
    let html = '<div class="dash-event-stack" style="width:100%;text-align:left;max-height:230px;overflow-y:auto;">';
    visibleList.forEach((c, idx) => {
        const d = new Date(c.date);
        const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const initial = c.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        const m = resolveMeta(c);
        const daysNum = typeof c.days_until === 'number' ? c.days_until : null;
        const whenText = daysNum === 0 ? 'Today' : (daysNum === 1 ? 'Tomorrow' : (daysNum !== null ? `In ${daysNum}d` : 'Upcoming'));

        html += `<div class="dash-event-card" data-ev-kind="celebration" data-ev-idx="${idx}">
                    <div class="dash-event-left">
                        <div class="dash-event-avatar" style="background: ${m.color};">${initial}</div>
                        <div>
                            <div class="dash-event-name">${escHtml(c.name)}</div>
                            <div class="dash-event-sub" style="color: ${m.labelColor};">${m.icon} ${escHtml(c.label)}</div>
                        </div>
                    </div>
                    <div class="dash-event-right">
                        <div class="dash-event-when">${whenText}</div>
                        <div class="dash-event-date">${dateStr}</div>
                    </div>
                </div>`;
    });
    html += '</div>';
    cont.innerHTML = html;
}

function renderHolidays(list) {
    const cont = document.getElementById('holidays-list');
    if (!cont) return;
    if (!list || !list.length) {
        window.dashHolidaysList = [];
        cont.innerHTML = '<div class="dash-empty" style="padding:16px 8px;"><div class="dash-empty-circle"><i class="bi bi-calendar-event"></i></div><span>No upcoming Holidays</span></div>';
        return;
    }
    const visibleList = list.slice(0, 6);
    window.dashHolidaysList = visibleList;
    let html = '<div class="dash-event-stack" style="width:100%;text-align:left;max-height:230px;overflow-y:auto;">';
    visibleList.forEach((h, idx) => {
        const d = new Date(h.holiday_date);
        const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const initial = h.name.split(' ').filter(n => n).map(n => n[0]).join('').substring(0,2).toUpperCase();
        const color = h.is_rh ? '#3B82F6' : '#10B981';
        const labelColor = h.is_rh ? '#1D4ED8' : '#047857';
        const icon = h.is_rh ? '🔵' : '🌴';
        const typeLabel = h.is_rh ? 'Restricted' : 'Public Holiday';
        const weekday = d.toLocaleDateString('en-IN', { weekday: 'short' });

        html += `<div class="dash-event-card" data-ev-kind="holiday" data-ev-idx="${idx}">
                    <div class="dash-event-left">
                        <div class="dash-event-avatar" style="background: ${color};">${initial}</div>
                        <div>
                            <div class="dash-event-name">${escHtml(h.name)}</div>
                            <div class="dash-event-sub" style="color: ${labelColor};">${icon} ${typeLabel}</div>
                        </div>
                    </div>
                    <div class="dash-event-right">
                        <div class="dash-event-when">${weekday}</div>
                        <div class="dash-event-date">${dateStr}</div>
                    </div>
                </div>`;
    });
    html += '</div>';
    cont.innerHTML = html;
}

function renderFollowups(leads) {
    const cont = document.getElementById('followups-list');
    if (!cont) return;
    if (!leads || !leads.length) {
        cont.innerHTML = '<div class="dash-empty"><i class="bi bi-telephone"></i><span>No follow-ups for today</span></div>';
        return;
    }
    let html = '';
    leads.slice(0, 5).forEach(l => {
        html += `<div class="ti">
            <div class="tdot o"></div>
            <div style="flex:1">
                <div class="tnm">${l.customer_name || 'Unnamed Lead'}</div>
                <div class="tdue">${l.source_name || 'Manual'} • ${l.status_name || 'New'}</div>
            </div>
            <span class="tm">···</span>
        </div>`;
    });
    cont.innerHTML = html;
}

function formatApprovalItem(type, item) {
    let title = '';
    let sub = '';
    if (type === 'at') {
        title = `${item.user_name || 'Employee'} - ${new Date(item.date).toLocaleDateString()}`;
        sub = 'Attendance regularization';
    } else if (type === 'lv') {
        title = `${item.user_name || 'Employee'} - ${item.leave_type_name || 'Leave'}`;
        const start = new Date(item.start_date).toLocaleDateString();
        const end = new Date(item.end_date).toLocaleDateString();
        sub = `${start} - ${end} • ${item.total_days} days`;
    } else if (type === 'pc') {
        title = `${item.expense_name || 'Expense'} - ${item.department_name || 'Dept'}`;
        sub = `₹${Number(item.price || 0).toLocaleString('en-IN')} • ${item.remark || 'No remark'}`;
    } else if (type === 'ts') {
        title = `${item.user_name || 'Employee'} - ${new Date(item.work_date).toLocaleDateString()}`;
        sub = `Logged: ${item.hours}h ${item.minutes}m`;
    } else if (type === 'tk') {
        title = `${item.task_name || 'Task'}`;
        const due = item.due_date ? new Date(item.due_date).toLocaleDateString() : 'No due date';
        sub = `Assigned to: ${item.user_name || 'N/A'} • Due: ${due}`;
    }
    const safeId = String(item.id ?? '').replace(/[^a-zA-Z0-9_-]/g, '');
    return { title: escHtml(title), sub: escHtml(sub), safeId: safeId };
}

function renderApprovals(type, list) {
    const cont = document.getElementById('ap-' + type);
    if (!cont) return;
    if (!list || !list.length) {
        const labels = { ts: 'timesheets', at: 'attendance', tk: 'tasks', lv: 'leaves', pc: 'petty cash' };
        cont.innerHTML = `<div class="dash-empty"><i class="bi bi-check-circle"></i><span>No pending ${labels[type] || 'items'}</span></div>`;
        return;
    }
    let html = '';
    list.forEach(item => {
        const info = formatApprovalItem(type, item);
        let btnHtml = '';
        if (type === 'tk') {
            btnHtml = `<span class="btn-approve-pill" onclick="approveTask('${info.safeId}', this)">Approve</span>`;
        } else if (type !== 'ts') {
            btnHtml = `<span class="btn-approve-pill" onclick="approveItem('${type}', '${info.safeId}', this)">Approve</span>`;
        }
        html += `<div class="ti"><div style="flex:1;min-width:0;"><div class="tnm">${info.title}</div><div class="tdue">${info.sub}</div></div>${btnHtml}</div>`;
    });
    cont.innerHTML = html;
}

function approveItem(type, id, btn) {
    if (!confirm('Are you sure you want to approve this item?')) return;

    let originalText = btn.innerHTML;
    btn.innerHTML = 'Approving...';
    btn.style.pointerEvents = 'none';
    btn.style.opacity = '0.7';

    let url = '';
    let payload = {
        _token: '{{ csrf_token() }}'
    };

    if (type === 'lv') {
        url = '/leave/approvals/' + id + '/approve';
    } else if (type === 'at') {
        url = '/attendance/approve/' + id;
    } else if (type === 'pc') {
        url = '/petty-cash/approve-bulk';
        payload.ids = [id];
    } else {
        return;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: payload,
        success: function(res) {
            loadDashboardMetrics();
        },
        error: function(err) {
            alert(err.responseJSON?.message || 'Failed to approve item');
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
            btn.style.opacity = '1';
        }
    });
}

function approveTask(id, btn) {
    if (!confirm('Are you sure you want to mark this task as done?')) return;

    let originalText = btn.innerHTML;
    btn.innerHTML = 'Approving...';
    btn.style.pointerEvents = 'none';
    btn.style.opacity = '0.7';

    $.ajax({
        url: '/task/' + id + '/toggle-done',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            loadDashboardMetrics();
        },
        error: function(err) {
            alert('Failed to mark task as done');
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
            btn.style.opacity = '1';
        }
    });
}

function renderTasks(containerId, tasks) {
    const cont = document.getElementById(containerId);
    if (!cont) return;
    const isByMe = (containerId === 'tk-byMe');
    const taskUrl = isByMe ? window.dashboardTaskUrls.byMe : window.dashboardTaskUrls.toMe;
    if (!tasks.length) {
        cont.innerHTML = `<div class="task-empty-cta">
            <div class="task-empty-icon"><i class="bi bi-clipboard-check"></i></div>
            <div class="task-empty-title">No tasks found</div>
            <div class="task-empty-sub">Create and assign tasks to keep your work on track.</div>
            <a href="${escHtml(taskUrl)}" class="btn-create-task">
                <i class="bi bi-plus-lg"></i>
                <span>Create Task</span>
            </a>
        </div>`;
        return;
    }
    let html = '';
    tasks.forEach(t => {
        const safeId = String(t.id ?? '').replace(/[^a-zA-Z0-9_-]/g, '');
        if (safeId) {
            window.dashboardTaskStore[safeId] = { task: t, listType: isByMe ? 'byMe' : 'toMe' };
        }
        const dot = t.priority === 'High' ? 'r' : (t.priority === 'Medium' ? 'o' : 'g');
        const due = t.due_date ? new Date(t.due_date).toLocaleDateString() : 'No due date';
        const label = isByMe ? 'Assigned to: ' + (t.assigned_to_name || 'N/A') : 'Assigned by: ' + (t.assigned_by_name || 'N/A');
        const statusChip = t.status_name
            ? `<span class="tk-status-chip"><span class="tk-st-dot" style="background:${escHtml(t.status_color || '#64748B')}"></span>${escHtml(t.status_name)}</span>`
            : '';
        html += `<div class="ti tk-row-link" data-task-url="${escHtml(taskUrl)}" onclick="openTaskPage(this.getAttribute('data-task-url'))">
            <div class="tdot ${dot}"></div>
            <div style="flex:1;min-width:0;">
                <div class="tk-title-line">
                    <div class="tnm">${escHtml(t.task_name || 'Untitled Task')}</div>
                    ${statusChip}
                </div>
                <div class="tdue">${escHtml(label)} • Due ${escHtml(due)}</div>
            </div>
            <div class="tk-row-actions">
                <button type="button" class="tk-act-btn tk-act-add" onclick="event.stopPropagation();openTaskQuickModal('${safeId}')" title="Update Status & Add Remark" aria-label="Update Status and Add Remark"><i class="bi bi-plus-lg"></i></button>
            </div>
        </div>`;
    });
    cont.innerHTML = html;
}

function openTaskPage(url) {
    if (url && url !== '#') {
        location.href = url;
    }
}

function goToActiveTaskPage() {
    var byMeActive = document.getElementById('tk-byMe') && document.getElementById('tk-byMe').classList.contains('act');
    var url = byMeActive ? window.dashboardTaskUrls.byMe : window.dashboardTaskUrls.toMe;
    if (!url || url === '#') url = window.dashboardTaskUrls.all;
    openTaskPage(url);
}

function loadDashboardTaskStatuses(cb) {
    if (Array.isArray(window.dashboardTaskStatuses) && window.dashboardTaskStatuses.length) {
        if (typeof cb === 'function') cb(window.dashboardTaskStatuses);
        return;
    }
    $.ajax({
        url: '/task/statuses',
        method: 'GET',
        success: function (res) {
            window.dashboardTaskStatuses = Array.isArray(res) ? res : [];
            if (typeof cb === 'function') cb(window.dashboardTaskStatuses);
        },
        error: function () {
            if (typeof cb === 'function') cb([]);
        }
    });
}

function renderQuickTaskStatusPills(statuses) {
    var wrap = document.getElementById('tkq-status-list');
    if (!wrap) return;
    if (!statuses || !statuses.length) {
        wrap.innerHTML = '<span class="tkq-loading">No statuses configured</span>';
        return;
    }
    var selectedId = window.activeQuickTask ? window.activeQuickTask.selectedStatusId : null;
    wrap.innerHTML = statuses.map(function (s) {
        var sid = Number(s.id);
        var isAct = selectedId !== null && Number(selectedId) === sid;
        return '<label class="tkq-status-pill' + (isAct ? ' act' : '') + '">' +
            '<input type="checkbox" class="tkq-status-chk" value="' + sid + '"' + (isAct ? ' checked' : '') + ' onchange="selectQuickTaskStatus(' + sid + ', this)">' +
            '<span class="tkq-chk-box" aria-hidden="true"><i class="bi bi-check-lg"></i></span>' +
            '<span class="tkq-st-dot" style="background:' + escHtml(s.color || '#64748B') + '"></span>' +
            '<span>' + escHtml(s.name || 'Status') + '</span>' +
        '</label>';
    }).join('');
}

function selectQuickTaskStatus(statusId, inputEl) {
    if (!window.activeQuickTask) return;
    window.activeQuickTask.selectedStatusId = Number(statusId);
    var wrap = document.getElementById('tkq-status-list');
    if (wrap) {
        wrap.querySelectorAll('.tkq-status-chk').forEach(function (chk) { chk.checked = false; });
        wrap.querySelectorAll('.tkq-status-pill').forEach(function (lbl) { lbl.classList.remove('act'); });
    }
    if (inputEl) {
        inputEl.checked = true;
        var labelEl = inputEl.closest('.tkq-status-pill');
        if (labelEl) labelEl.classList.add('act');
    }
}

function openTaskQuickModal(taskId) {
    var entry = window.dashboardTaskStore && window.dashboardTaskStore[String(taskId)];
    if (!entry || !entry.task) return;

    var t = entry.task;
    var isByMe = entry.listType === 'byMe';
    var pageUrl = isByMe ? window.dashboardTaskUrls.byMe : window.dashboardTaskUrls.toMe;
    var due = t.due_date ? new Date(t.due_date).toLocaleDateString() : 'No due date';
    var who = isByMe ? ('Assigned to: ' + (t.assigned_to_name || 'N/A')) : ('Assigned by: ' + (t.assigned_by_name || 'N/A'));
    var origStatusId = t.task_status_id ? Number(t.task_status_id) : null;

    window.activeQuickTask = {
        id: t.id,
        originalStatusId: origStatusId,
        selectedStatusId: origStatusId
    };

    var titleEl = document.getElementById('tkq-title');
    if (titleEl) titleEl.textContent = t.task_name || 'Update Task';

    var metaEl = document.getElementById('tkq-meta');
    if (metaEl) metaEl.textContent = who + ' • Due ' + due;

    var openLink = document.getElementById('tkq-open-link');
    if (openLink) openLink.href = pageUrl || '#';

    var descWrap = document.getElementById('tkq-desc-wrap');
    var descEl = document.getElementById('tkq-desc');
    var descText = t.task ? String(t.task).trim() : '';
    if (descWrap && descEl) {
        if (descText) {
            descEl.textContent = descText;
            descWrap.style.display = 'block';
        } else {
            descWrap.style.display = 'none';
        }
    }

    var remarkEl = document.getElementById('tkq-remark');
    if (remarkEl) remarkEl.value = '';
    var charEl = document.getElementById('tkq-char-count');
    if (charEl) charEl.textContent = '0 / 1000';

    var msgEl = document.getElementById('tkq-msg');
    if (msgEl) {
        msgEl.className = 'tkq-msg';
        msgEl.textContent = '';
    }

    var saveBtn = document.getElementById('tkq-save-btn');
    if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Update';
    }

    var statusWrap = document.getElementById('tkq-status-list');
    if (statusWrap) statusWrap.innerHTML = '<span class="tkq-loading">Loading statuses...</span>';

    var modal = document.getElementById('modal-task-quick');
    if (modal) modal.style.display = 'flex';

    loadDashboardTaskStatuses(function (statuses) {
        renderQuickTaskStatusPills(statuses);
    });
}

function closeTaskQuickModal() {
    var modal = document.getElementById('modal-task-quick');
    if (modal) modal.style.display = 'none';
    window.activeQuickTask = null;
}

function submitTaskQuickUpdate() {
    var active = window.activeQuickTask;
    if (!active || !active.id) return;

    var remarkEl = document.getElementById('tkq-remark');
    var remark = remarkEl ? remarkEl.value.trim() : '';
    var statusChanged = active.selectedStatusId !== null && Number(active.selectedStatusId) !== Number(active.originalStatusId);

    var msgEl = document.getElementById('tkq-msg');
    var saveBtn = document.getElementById('tkq-save-btn');

    if (!statusChanged && !remark) {
        if (msgEl) {
            msgEl.className = 'tkq-msg err';
            msgEl.textContent = 'Select a new status or enter a remark to save.';
        }
        return;
    }

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }
    if (msgEl) {
        msgEl.className = 'tkq-msg';
        msgEl.textContent = '';
    }

    var reqs = [];
    if (statusChanged) {
        reqs.push($.ajax({
            url: '/task/' + encodeURIComponent(active.id) + '/update-status-id',
            method: 'POST',
            data: {
                task_status_id: active.selectedStatusId,
                _token: '{{ csrf_token() }}'
            }
        }));
    }
    if (remark) {
        reqs.push($.ajax({
            url: '/task/remark/save',
            method: 'POST',
            data: {
                task_id: active.id,
                remark: remark,
                _token: '{{ csrf_token() }}'
            }
        }));
    }

    $.when.apply($, reqs).done(function () {
        if (msgEl) {
            msgEl.className = 'tkq-msg ok';
            msgEl.textContent = 'Task updated successfully!';
        }
        if (saveBtn) saveBtn.textContent = 'Saved ✓';
        loadDashboardMetrics();
        setTimeout(function () {
            closeTaskQuickModal();
        }, 650);
    }).fail(function (xhr) {
        var errMsg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save update';
        if (msgEl) {
            msgEl.className = 'tkq-msg err';
            msgEl.textContent = errMsg;
        }
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Update';
        }
    });
}

window.attNamesData = { present: [], absent: [] };
var attPopTimer = null;

window.approvalPreviewData = {
    ts: { count: 0, list: [] },
    at: { count: 0, list: [] },
    tk: { count: 0, list: [] },
    lv: { count: 0, list: [] },
    pc: { count: 0, list: [] }
};
var apPopTimer = null;
window.activeApPopoverType = null;
window.activeApPopoverAnchor = null;

function escHtml(s) {
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderMyAttendanceBadge(att) {
    window.latestMyAttendance = att || null;
    var container = document.getElementById('my-att-container');
    var badge = document.getElementById('my-att-badge');
    var quickBtn = document.getElementById('my-att-quick-btn');
    var dropdown = document.getElementById('my-att-dropdown');
    if (!badge || !container) return;
    badge.dataset.rendered = '1';

    var attUrl = "{{ Route::has('attendance') ? route('attendance') : '/attendance' }}";
    clearInterval(window.myAttTickerTimer);

    var canWebPunch = att ? (att.can_web_punch !== false) : true;
    var canFieldPunch = att ? (att.can_field_punch === true) : false;

    if (att && att.marked) {
        var lateMin = Number(att.late_minutes) || 0;
        var workTimeStr = att.work_hours_formatted || (att.work_minutes ? (Math.floor(att.work_minutes / 60) + 'h ' + (att.work_minutes % 60) + 'm') : '0m');

        if (att.state === 'on_break') {
            badge.className = 'my-att-pill is-break';
            badge.innerHTML =
                '<span class="my-att-dot"></span>' +
                '<span class="my-att-lbl">ON BREAK</span>' +
                '<strong class="my-att-time">' + escHtml(att.in_time || 'Marked') + '</strong>' +
                '<span class="my-att-work-tag" title="Working Hours Recorded"><i class="bi bi-cup-hot"></i> <span id="my-att-work-clock">' + escHtml(workTimeStr) + '</span></span>' +
                '<span class="my-att-mode is-amber">Break</span>' +
                (!canWebPunch ? '<span class="my-att-mobile-tag" title="Mobile Tracking Active"><i class="bi bi-phone"></i></span>' : '') +
                '<i class="bi bi-chevron-down my-att-arrow"></i>';

            if (quickBtn) {
                if (canWebPunch) {
                    quickBtn.className = 'my-att-quick-btn btn-quick-break-end';
                    quickBtn.innerHTML = '<i class="bi bi-play-fill"></i> End Break';
                    quickBtn.title = 'End break and resume working';
                    quickBtn.onclick = function() { quickPunchAction('break_end'); };
                    quickBtn.style.display = 'inline-flex';
                } else {
                    quickBtn.style.display = 'none';
                }
            }

            if (dropdown) {
                dropdown.innerHTML =
                    '<div class="my-att-dp-head">' +
                        '<div class="my-att-dp-title"><span class="my-att-dot" style="background:#F59E0B;"></span> Currently On Break</div>' +
                        '<div class="my-att-dp-sub"><span>Started ' + escHtml(att.in_time || '--') + '</span><span class="my-att-dp-work"><i class="bi bi-clock-history me-1"></i>' + escHtml(workTimeStr) + '</span></div>' +
                    '</div>' +
                    (!canWebPunch ?
                        '<div class="my-att-mobile-banner"><i class="bi bi-phone"></i><span>Live tracking is enabled on your profile. Please resume work or punch out from the <strong>Workorio Mobile App</strong>.</span></div>' : '') +
                    '<div class="my-att-dp-menu">' +
                        (canWebPunch ?
                            '<button type="button" class="my-att-dp-btn btn-break-end" onclick="quickPunchAction(\'break_end\')">' +
                                '<i class="bi bi-play-circle-fill"></i>' +
                                '<div><strong>End Break / Resume</strong><span>Continue working session</span></div>' +
                            '</button>' +
                            '<button type="button" class="my-att-dp-btn btn-out" onclick="quickPunchAction(\'out\', \'' + (att.mode || 'office').toLowerCase() + '\')">' +
                                '<i class="bi bi-box-arrow-right"></i>' +
                                '<div><strong>Punch Out</strong><span>End office cycle for today</span></div>' +
                            '</button>' : '') +
                        '<a href="' + attUrl + '" class="my-att-dp-btn btn-link">' +
                            '<i class="bi bi-calendar2-check"></i>' +
                            '<div><strong>Attendance Page</strong><span>Detailed timeline & stats</span></div>' +
                            '<i class="bi bi-arrow-right ms-auto" style="font-size:12px;"></i>' +
                        '</a>' +
                    '</div>';
            }
        } else if (att.state === 'punched_out') {
            badge.className = 'my-att-pill is-out';
            badge.innerHTML =
                '<i class="bi bi-check-circle-fill" style="color:#434AFA;font-size:13px;"></i>' +
                '<span class="my-att-lbl">Punched Out</span>' +
                '<strong class="my-att-time">' + escHtml(att.in_time || '--') + (att.out_time ? (' - ' + escHtml(att.out_time)) : '') + '</strong>' +
                '<span class="my-att-work-tag" title="Total Working Hours Today"><i class="bi bi-hourglass-split"></i> <span>' + escHtml(workTimeStr) + '</span></span>' +
                (!canWebPunch ? '<span class="my-att-mobile-tag" title="Mobile Tracking Active"><i class="bi bi-phone"></i></span>' : '') +
                '<i class="bi bi-chevron-down my-att-arrow"></i>';

            if (quickBtn) {
                if (canWebPunch) {
                    quickBtn.className = 'my-att-quick-btn btn-quick-in';
                    quickBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Punch In';
                    quickBtn.title = 'Punch in again';
                    quickBtn.onclick = function() { quickPunchAction('in', 'office'); };
                    quickBtn.style.display = 'inline-flex';
                } else {
                    quickBtn.style.display = 'none';
                }
            }

            if (dropdown) {
                dropdown.innerHTML =
                    '<div class="my-att-dp-head">' +
                        '<div class="my-att-dp-title"><i class="bi bi-check-circle-fill text-primary"></i> Shift Completed</div>' +
                        '<div class="my-att-dp-sub"><span>In ' + escHtml(att.in_time || '--') + ' • Out ' + escHtml(att.out_time || '--') + '</span><span class="my-att-dp-work">Total ' + escHtml(workTimeStr) + '</span></div>' +
                    '</div>' +
                    (!canWebPunch ?
                        '<div class="my-att-mobile-banner"><i class="bi bi-phone"></i><span>Live tracking is enabled on your profile. Please punch in from the <strong>Workorio Mobile App</strong>.</span></div>' : '') +
                    '<div class="my-att-dp-menu">' +
                        (canWebPunch ?
                            '<button type="button" class="my-att-dp-btn btn-in" onclick="quickPunchAction(\'in\', \'office\')">' +
                                '<i class="bi bi-building"></i>' +
                                '<div><strong>Punch In (Office)</strong><span>Start a new office cycle</span></div>' +
                            '</button>' +
                            (canFieldPunch ?
                                '<button type="button" class="my-att-dp-btn btn-in" onclick="quickPunchAction(\'in\', \'field\')">' +
                                    '<i class="bi bi-geo-alt"></i>' +
                                    '<div><strong>Punch In (Field)</strong><span>Start field duty cycle</span></div>' +
                                '</button>' : '') : '') +
                        '<a href="' + attUrl + '" class="my-att-dp-btn btn-link">' +
                            '<i class="bi bi-calendar2-check"></i>' +
                            '<div><strong>Attendance Page</strong><span>Detailed timeline & stats</span></div>' +
                            '<i class="bi bi-arrow-right ms-auto" style="font-size:12px;"></i>' +
                        '</a>' +
                    '</div>';
            }
        } else {
            // Punched IN
            badge.className = 'my-att-pill is-in';
            badge.innerHTML =
                '<span class="my-att-dot"></span>' +
                '<span class="my-att-lbl">IN TIME</span>' +
                '<strong class="my-att-time">' + escHtml(att.in_time || 'Marked') + '</strong>' +
                '<span class="my-att-work-tag" title="Total Working Hours Today"><i class="bi bi-clock-history"></i> <span id="my-att-work-clock">' + escHtml(workTimeStr) + '</span></span>' +
                '<span class="my-att-mode">' + escHtml(att.mode || 'Office') + '</span>' +
                (lateMin > 0 ? '<span class="my-att-late">Late ' + lateMin + 'm</span>' : '') +
                (!canWebPunch ? '<span class="my-att-mobile-tag" title="Mobile Tracking Active"><i class="bi bi-phone"></i></span>' : '') +
                '<i class="bi bi-chevron-down my-att-arrow"></i>';

            if (quickBtn) {
                if (canWebPunch) {
                    quickBtn.className = 'my-att-quick-btn btn-quick-out';
                    quickBtn.innerHTML = '<i class="bi bi-box-arrow-right"></i> Punch Out';
                    quickBtn.title = 'Punch out for today';
                    quickBtn.onclick = function() { quickPunchAction('out', (att.mode || 'office').toLowerCase()); };
                    quickBtn.style.display = 'inline-flex';
                } else {
                    quickBtn.style.display = 'none';
                }
            }

            if (dropdown) {
                dropdown.innerHTML =
                    '<div class="my-att-dp-head">' +
                        '<div class="my-att-dp-title"><span class="my-att-dot" style="background:#10B981;"></span> Active: Punched In (' + escHtml(att.mode || 'Office') + ')</div>' +
                        '<div class="my-att-dp-sub"><span>Since ' + escHtml(att.in_time || '--') + '</span><span class="my-att-dp-work"><i class="bi bi-clock-history me-1"></i><span id="my-att-dp-work-clock">' + escHtml(workTimeStr) + '</span></span></div>' +
                    '</div>' +
                    (!canWebPunch ?
                        '<div class="my-att-mobile-banner"><i class="bi bi-phone"></i><span>Live tracking is enabled on your profile. Please punch out or take break from the <strong>Workorio Mobile App</strong>.</span></div>' : '') +
                    '<div class="my-att-dp-menu">' +
                        (canWebPunch ?
                            '<button type="button" class="my-att-dp-btn btn-out" onclick="quickPunchAction(\'out\', \'' + (att.mode || 'office').toLowerCase() + '\')">' +
                                '<i class="bi bi-box-arrow-right"></i>' +
                                '<div><strong>Punch Out</strong><span>End office cycle for today</span></div>' +
                            '</button>' +
                            '<button type="button" class="my-att-dp-btn btn-break" onclick="quickPunchAction(\'break_start\')">' +
                                '<i class="bi bi-cup-hot"></i>' +
                                '<div><strong>Take a Break</strong><span>Pause work session</span></div>' +
                            '</button>' : '') +
                        '<a href="' + attUrl + '" class="my-att-dp-btn btn-link">' +
                            '<i class="bi bi-calendar2-check"></i>' +
                            '<div><strong>Attendance Page</strong><span>Detailed timeline & stats</span></div>' +
                            '<i class="bi bi-arrow-right ms-auto" style="font-size:12px;"></i>' +
                        '</a>' +
                    '</div>';
            }

            // Live minute ticker when punched in
            if (typeof att.work_minutes === 'number') {
                var curMins = att.work_minutes;
                window.myAttTickerTimer = setInterval(function () {
                    curMins++;
                    var wh = Math.floor(curMins / 60);
                    var wm = curMins % 60;
                    var formatted = (wh > 0 ? (wh + 'h ') : '') + wm + 'm';
                    var clockEl = document.getElementById('my-att-work-clock');
                    if (clockEl) clockEl.textContent = formatted;
                    var dpClockEl = document.getElementById('my-att-dp-work-clock');
                    if (dpClockEl) dpClockEl.textContent = formatted;
                }, 60000);
            }
        }
        container.style.display = 'inline-flex';
        return;
    }

    if (window.showMyAttNotMarked) {
        badge.className = 'my-att-pill is-pending';
        badge.innerHTML =
            '<span class="my-att-dot"></span>' +
            '<span class="my-att-lbl">Not Punched In</span>' +
            (!canWebPunch ? '<span class="my-att-mobile-tag"><i class="bi bi-phone"></i> Mobile App</span>' : '<span class="my-att-cta">Options ▾</span>');

        if (quickBtn) {
            if (canWebPunch) {
                quickBtn.className = 'my-att-quick-btn btn-quick-in';
                quickBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Punch In';
                quickBtn.title = 'Punch In Now';
                quickBtn.onclick = function() { quickPunchAction('in', 'office'); };
                quickBtn.style.display = 'inline-flex';
            } else {
                quickBtn.style.display = 'none';
            }
        }

        if (dropdown) {
            dropdown.innerHTML =
                '<div class="my-att-dp-head">' +
                    '<div class="my-att-dp-title"><span class="my-att-dot" style="background:#F97316;"></span> Attendance Not Marked</div>' +
                    '<div class="my-att-dp-sub"><span>Mark today\'s attendance</span></div>' +
                '</div>' +
                (!canWebPunch ?
                    '<div class="my-att-mobile-banner"><i class="bi bi-phone"></i><span>Live tracking is enabled on your profile. Attendance can only be punched from the <strong>Workorio Mobile App</strong>.</span></div>' : '') +
                '<div class="my-att-dp-menu">' +
                    (canWebPunch ?
                        '<button type="button" class="my-att-dp-btn btn-in" onclick="quickPunchAction(\'in\', \'office\')">' +
                            '<i class="bi bi-building"></i>' +
                            '<div><strong>Punch In (Office)</strong><span>Standard office attendance</span></div>' +
                        '</button>' +
                        (canFieldPunch ?
                            '<button type="button" class="my-att-dp-btn btn-in" onclick="quickPunchAction(\'in\', \'field\')">' +
                                '<i class="bi bi-geo-alt"></i>' +
                                '<div><strong>Punch In (Field)</strong><span>Outdoor or client duty</span></div>' +
                            '</button>' : '') : '') +
                    '<a href="' + attUrl + '" class="my-att-dp-btn btn-link">' +
                        '<i class="bi bi-calendar2-check"></i>' +
                        '<div><strong>Attendance Page</strong><span>View details & regularize</span></div>' +
                        '<i class="bi bi-arrow-right ms-auto" style="font-size:12px;"></i>' +
                    '</a>' +
                '</div>';
        }

        container.style.display = 'inline-flex';
        return;
    }

    container.style.display = 'none';
}

function toggleMyAttDropdown(event) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    var container = document.getElementById('my-att-container');
    var dp = document.getElementById('my-att-dropdown');
    if (!dp || !container) return;

    var isOpen = dp.style.display !== 'none';
    if (isOpen) {
        dp.style.display = 'none';
        container.classList.remove('is-open');
    } else {
        dp.style.display = 'block';
        container.classList.add('is-open');
    }
}

$(document).on('click', function (e) {
    var container = document.getElementById('my-att-container');
    if (container && !container.contains(e.target)) {
        var dp = document.getElementById('my-att-dropdown');
        if (dp) dp.style.display = 'none';
        container.classList.remove('is-open');
    }
});

function quickPunchAction(action, movementType, extraData) {
    extraData = extraData || {};
    var container = document.getElementById('my-att-container');
    var quickBtn = document.getElementById('my-att-quick-btn');
    var dropdown = document.getElementById('my-att-dropdown');

    if (dropdown) dropdown.style.display = 'none';
    if (container) container.classList.remove('is-open');

    var origBtnHtml = quickBtn ? quickBtn.innerHTML : '';
    if (quickBtn) {
        quickBtn.disabled = true;
        quickBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    }

    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

    function runAjax(lat, lng) {
        var url = '';
        var postData = {
            _token: csrfToken,
            latitude: lat,
            longitude: lng
        };

        if (action === 'in') {
            url = '/attendance/punch-in';
            postData.movement_type = movementType || 'office';
            if (extraData.late_reason) {
                postData.late_reason = extraData.late_reason;
            }
        } else if (action === 'out') {
            url = '/attendance/punch-out';
            postData.movement_type = movementType || 'office';
        } else if (action === 'break_start') {
            url = '/attendance/start-break';
        } else if (action === 'break_end') {
            url = '/attendance/end-break';
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: postData,
            success: function (resp) {
                if (quickBtn) {
                    quickBtn.disabled = false;
                    quickBtn.innerHTML = origBtnHtml;
                }
                if (resp && resp.success) {
                    showDashAttToast('success', resp.message || 'Attendance updated successfully!');
                    refreshMyAttendance();
                    if (window.canQuickWorklog && typeof loadQuickTimesheetWidget === 'function') {
                        loadQuickTimesheetWidget(action === 'out', resp.not_updated_tasks_count || 0);
                    }
                } else {
                    showDashAttToast('error', (resp && resp.message) ? resp.message : 'Action could not be completed.');
                }
            },
            error: function (xhr) {
                if (quickBtn) {
                    quickBtn.disabled = false;
                    quickBtn.innerHTML = origBtnHtml;
                }
                var errJson = xhr.responseJSON;
                if (xhr.status === 422 && errJson && errJson.require_late_reason) {
                    window.pendingLatePunchType = movementType || 'office';
                    var modalEl = document.getElementById('quickLateReasonModal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else {
                        var reason = prompt('Please provide a reason for late punch-in:');
                        if (reason) {
                            quickPunchAction('in', movementType, { late_reason: reason });
                        }
                    }
                    return;
                }
                var msg = 'An error occurred while processing attendance.';
                if (errJson && errJson.message) {
                    msg = errJson.message;
                } else if (errJson && errJson.errors) {
                    msg = Object.values(errJson.errors).flat().join(', ');
                }
                showDashAttToast('error', msg);
                if (action === 'in' && xhr.status === 403 && msg.toLowerCase().indexOf('worklog') !== -1 && window.canQuickWorklog && typeof openQuickTimesheetDrawer === 'function') {
                    setTimeout(function () { openQuickTimesheetDrawer(); }, 350);
                }
            }
        });
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (pos) {
                runAjax(pos.coords.latitude, pos.coords.longitude);
            },
            function (err) {
                runAjax(null, null);
            },
            { enableHighAccuracy: true, timeout: 4000, maximumAge: 0 }
        );
    } else {
        runAjax(null, null);
    }
}

function submitQuickLateReason() {
    var reason = $('#quickLateCustom').val().trim();
    if (!reason) {
        $('#quickLateError').show();
        return;
    }
    $('#quickLateError').hide();
    var modalEl = document.getElementById('quickLateReasonModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
    var type = window.pendingLatePunchType || 'office';
    quickPunchAction('in', type, { late_reason: reason });
}

function showDashAttToast(type, msg) {
    var toast = document.getElementById('dash-att-toast');
    if (!toast) return;
    var isSuccess = type === 'success';
    toast.className = 'dash-att-toast ' + (isSuccess ? 'is-success' : 'is-error') + ' show';
    toast.innerHTML =
        '<div class="toast-icon">' + (isSuccess ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-exclamation-triangle-fill"></i>') + '</div>' +
        '<div class="toast-body">' + escHtml(msg) + '</div>' +
        '<button type="button" class="toast-close" onclick="this.parentElement.classList.remove(\'show\')">×</button>';
    clearTimeout(window.dashAttToastTimer);
    window.dashAttToastTimer = setTimeout(function () {
        toast.classList.remove('show');
    }, 4500);
}

function refreshMyAttendance() {
    var qp = (window.currentView === 'sales') ? '?team=1' : '';
    $.ajax({
        url: '/attendance/summary' + qp,
        method: 'GET',
        cache: false,
        success: function (res) {
            $('#att-present').text(res.present);
            $('#att-absent').text(res.absent);
            $('#att-office').text(res.office);
            $('#att-remote').text(res.remote);
            $('#att-field').text(res.field);
            $('#att-wfh').text(res.wfh);
            if (typeof drawAttendanceDonut === 'function') drawAttendanceDonut(res.present, res.absent);
            window.attNamesData.present = Array.isArray(res.present_names) ? res.present_names : [];
            window.attNamesData.absent = Array.isArray(res.absent_names) ? res.absent_names : [];
            renderMyAttendanceBadge(res.my_attendance || null);
        }
    });
}

// ────────────── Dashboard Quick Timesheet Module ──────────────
window.qtsData = null;
window.qtsCustomersLoaded = false;
window.qtsDrawerOpen = false;
window.qtsTasksExpanded = false;

function qtsFmtMins(mins) {
    mins = Math.max(0, Number(mins) || 0);
    var h = Math.floor(mins / 60);
    var m = mins % 60;
    return h + 'h ' + m + 'm';
}

function qtsFmtShortDate(isoDate) {
    if (!isoDate) return '';
    var parts = String(isoDate).split('-');
    if (parts.length !== 3) return isoDate;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var mIdx = parseInt(parts[1], 10) - 1;
    return parseInt(parts[2], 10) + ' ' + (months[mIdx] || parts[1]);
}

function qtsGetCsrf() {
    return $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
}

function loadQuickTimesheetWidget(showPostPrompt, notUpdatedTasksCount) {
    if (!window.canQuickWorklog || !window.qtsRoutes) return;
    $.ajax({
        url: window.qtsRoutes.widget,
        method: 'GET',
        cache: false,
        success: function (res) {
            if (!res || !res.enabled) {
                $('#dash-ts-pill').hide();
                return;
            }
            window.qtsData = res;
            qtsRenderHeaderPill(res);

            if (showPostPrompt && !res.today_submitted) {
                qtsShowPostPunchPrompt(res, notUpdatedTasksCount || 0);
            }

            if (window.qtsDrawerOpen) {
                qtsRenderDrawer(null, true);
            }
        }
    });
}

function qtsRenderHeaderPill(res) {
    var pill = document.getElementById('dash-ts-pill');
    if (!pill) return;
    var missing = Array.isArray(res.missing_dates) ? res.missing_dates : [];
    var draftEntries = Array.isArray(res.session_entries) ? res.session_entries : [];
    var draftMins = draftEntries.reduce(function (sum, e) { return sum + (Number(e.total_minutes) || 0); }, 0);

    pill.className = 'dash-ts-pill';
    if (missing.length > 0) {
        pill.classList.add('is-pending');
        pill.innerHTML =
            '<span class="dash-ts-dot"></span>' +
            '<span class="dash-ts-text">Timesheet Pending (' + escHtml(qtsFmtShortDate(missing[0])) + ')</span>';
        pill.title = 'Previous timesheet pending — click to submit now';
    } else if (res.today_submitted) {
        pill.classList.add('is-done');
        pill.innerHTML =
            '<i class="bi bi-check-circle-fill" style="color:#10B981;font-size:13px;"></i>' +
            '<span class="dash-ts-text">Timesheet Done</span>' +
            '<span class="dash-ts-chip">' + escHtml(qtsFmtMins(res.today_submitted_minutes)) + '</span>';
        pill.title = 'Today\'s timesheet is submitted';
    } else if (res.today_punched_out) {
        pill.classList.add('is-action');
        pill.innerHTML =
            '<span class="dash-ts-dot"></span>' +
            '<span class="dash-ts-text">Submit Timesheet</span>' +
            (draftMins > 0 ? '<span class="dash-ts-chip">' + escHtml(qtsFmtMins(draftMins)) + '</span>' : '');
        pill.title = 'You have punched out — click to submit today\'s timesheet';
    } else if (draftEntries.length > 0) {
        pill.classList.add('is-draft');
        pill.innerHTML =
            '<i class="bi bi-journal-text" style="font-size:13px;"></i>' +
            '<span class="dash-ts-text">Draft: ' + escHtml(qtsFmtMins(draftMins)) + '</span>';
        pill.title = 'Click to add more entries or submit later';
    } else {
        pill.classList.add('is-idle');
        pill.innerHTML =
            '<i class="bi bi-journal-plus" style="font-size:13px;"></i>' +
            '<span class="dash-ts-text">Timesheet</span>';
        pill.title = 'Log your work entries in draft anytime today';
    }
    pill.style.display = 'inline-flex';
}

function qtsShowPostPunchPrompt(res, notUpdatedTasksCount) {
    var box = document.getElementById('dash-ts-prompt');
    if (!box) return;
    var workStr = qtsFmtMins(res.today_work_minutes || 0);
    $('#dtp-title').text('Punched Out! (' + workStr + ' worked)');
    $('#dtp-sub').text('Submit today\'s timesheet right now so you skip tomorrow\'s morning rush.');
    if (notUpdatedTasksCount > 0) {
        $('#dtp-task-note').html('<i class="bi bi-bell-fill"></i> Reminder: ' + notUpdatedTasksCount + ' pending task(s) had no update today — you can update them inside the drawer.').show();
    } else {
        $('#dtp-task-note').hide();
    }
    box.style.display = 'block';
}

function dismissPostPunchPrompt() {
    var box = document.getElementById('dash-ts-prompt');
    if (box) box.style.display = 'none';
}

function qtsSyncSelect(sel) {
    var el = typeof sel === 'string' ? document.querySelector(sel) : sel;
    if (!el) return;
    if (window.NexoraSelect) {
        if (el.dataset.nxEnhanced !== '1') {
            window.NexoraSelect.enhance(el);
        }
        window.NexoraSelect.sync(el);
    }
}

function openQuickTimesheetDrawer(forceDate) {
    if (!window.canQuickWorklog) return;
    dismissPostPunchPrompt();
    var backdrop = document.getElementById('quick-ts-backdrop');
    var drawer = document.getElementById('quick-ts-drawer');
    if (!drawer || !backdrop) return;

    window.qtsDrawerOpen = true;
    backdrop.classList.add('open');
    drawer.classList.add('open');
    drawer.setAttribute('aria-hidden', 'false');

    if (window.NexoraSelect) {
        window.NexoraSelect.enhanceAll(drawer);
    }
    if (window.NexoraDate) {
        window.NexoraDate.enhanceAll(drawer);
    }

    if (!window.qtsCustomersLoaded) {
        qtsLoadCustomers();
    }

    if (window.qtsData) {
        qtsRenderDrawer(forceDate, false);
        loadQuickTimesheetWidget(false, 0);
    } else {
        loadQuickTimesheetWidget(false, 0);
    }
}

function closeQuickTimesheetDrawer() {
    window.qtsDrawerOpen = false;
    document.querySelectorAll('.nx-auto-menu.show').forEach(function (m) { m.classList.remove('show'); });
    document.querySelectorAll('.nx-auto-select.open').forEach(function (w) { w.classList.remove('open'); });
    document.querySelectorAll('.nx-date-popover.show').forEach(function (p) { p.classList.remove('show'); });
    var backdrop = document.getElementById('quick-ts-backdrop');
    var drawer = document.getElementById('quick-ts-drawer');
    if (backdrop) backdrop.classList.remove('open');
    if (drawer) {
        drawer.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
    }
}

function qtsRenderDrawer(forceDate, preserveSelections) {
    var d = window.qtsData;
    if (!d) return;

    // 1. Determine selected date
    var curDateEl = document.getElementById('qts-date');
    var selectedDate = forceDate || (preserveSelections && curDateEl && curDateEl.value ? curDateEl.value : (d.suggested_date || d.today));
    if (curDateEl) {
        curDateEl.max = d.today;
        $('#qts-date').val(selectedDate);
        if (window.NexoraDate) window.NexoraDate.sync(curDateEl);
    }

    // 2. Populate Entry Types
    var etSel = document.getElementById('qts-entry-type');
    var draftEntries = Array.isArray(d.session_entries) ? d.session_entries : [];
    if (etSel) {
        var prevEt = preserveSelections ? etSel.value : '';
        var html = '';
        (d.entry_types || []).forEach(function (et) {
            html += '<option value="' + et.id + '" data-hours="' + et.working_hours + '">' + escHtml(et.name) + ' (' + et.working_hours + 'h)</option>';
        });
        etSel.innerHTML = html;
        if (draftEntries.length > 0 && draftEntries[0].entry_type_id) {
            etSel.value = String(draftEntries[0].entry_type_id);
        } else if (prevEt && etSel.querySelector('option[value="' + prevEt + '"]')) {
            etSel.value = prevEt;
        } else if (d.suggested_entry_type_id) {
            etSel.value = String(d.suggested_entry_type_id);
        }
        qtsSyncSelect(etSel);
    }

    // 3. Header Badge & Sub
    var missing = Array.isArray(d.missing_dates) ? d.missing_dates : [];
    if (missing.indexOf(selectedDate) !== -1) {
        $('#qts-header-badge').attr('class', 'qts-header-badge is-pending').text('Pending: ' + qtsFmtShortDate(selectedDate));
        $('#qts-header-sub').text('Complete this missing day to unlock Punch-In');
    } else if (d.today_submitted && selectedDate === d.today) {
        $('#qts-header-badge').attr('class', 'qts-header-badge is-done').text('Submitted');
        $('#qts-header-sub').text('Today\'s timesheet is already submitted (' + qtsFmtMins(d.today_submitted_minutes) + ')');
    } else {
        $('#qts-header-badge').attr('class', 'qts-header-badge').text(selectedDate === d.today ? 'Today • ' + qtsFmtShortDate(d.today) : qtsFmtShortDate(selectedDate));
        var workedStr = d.today_work_minutes > 0 && selectedDate === d.today ? (' • Worked ' + qtsFmtMins(d.today_work_minutes)) : '';
        $('#qts-header-sub').text('Add entries to draft and submit anytime after punch-out' + workedStr);
    }

    // 4. Alert Banner (Missing Dates OR Submitted Summary)
    qtsRenderAlertBanner(d, selectedDate);

    // 5. Tasks Helper Strip
    qtsRenderTasksBox(d);

    // 6. Recent Combos
    qtsRenderRecentChips(d);

    // 7. Draft Entries + Mismatch Check
    qtsRenderDrafts(d, selectedDate);

    // 8. Progress + Footer Hint
    qtsRenderProgress();
}

function qtsOnDateChange() {
    var d = window.qtsData;
    var selectedDate = $('#qts-date').val();
    if (!d || !selectedDate) return;
    qtsRenderAlertBanner(d, selectedDate);
    qtsRenderDrafts(d, selectedDate);
    qtsRenderProgress();
}

function qtsRenderAlertBanner(d, selectedDate) {
    var banner = $('#qts-alert-banner');
    var missing = Array.isArray(d.missing_dates) ? d.missing_dates : [];

    if (missing.length > 0) {
        var chips = missing.map(function (md) {
            var act = md === selectedDate ? ' active' : '';
            return '<button type="button" class="qts-miss-date-chip' + act + '" onclick="qtsSelectDate(\'' + escHtml(md) + '\')">' + escHtml(qtsFmtShortDate(md)) + '</button>';
        }).join('');
        banner.html(
            '<div class="qts-banner is-amber">' +
                '<div class="qts-banner-title"><i class="bi bi-exclamation-triangle-fill"></i> Pending Past Timesheet (' + missing.length + ' day' + (missing.length > 1 ? 's' : '') + ')</div>' +
                '<div class="qts-banner-sub">Select a pending date below to submit it first:</div>' +
                '<div class="qts-miss-chips">' + chips + '</div>' +
            '</div>'
        ).show();
        return;
    }

    if (selectedDate === d.today && d.today_submitted) {
        var rows = (d.today_submitted_entries || []).map(function (row) {
            return '<div class="qts-sub-row">' +
                '<div><strong>' + escHtml(row.customer_name || '-') + '</strong> • ' + escHtml(row.customer_project_name || row.service_name || '-') + ' <span class="text-muted">(' + escHtml(row.module_name || '-') + ')</span>' +
                '<div class="qts-sub-desc">' + escHtml(row.description || '') + '</div></div>' +
                '<span class="qts-sub-time">' + Number(row.hours || 0) + 'h ' + Number(row.minutes || 0) + 'm</span>' +
            '</div>';
        }).join('');
        banner.html(
            '<div class="qts-banner is-green">' +
                '<div class="qts-banner-title"><i class="bi bi-check-circle-fill"></i> Today\'s Timesheet Submitted (' + escHtml(qtsFmtMins(d.today_submitted_minutes)) + ')</div>' +
                '<div class="qts-sub-list">' + rows + '</div>' +
            '</div>'
        ).show();
        return;
    }

    banner.hide().empty();
}

function qtsSelectDate(dateStr) {
    var el = document.getElementById('qts-date');
    $('#qts-date').val(dateStr);
    if (el && window.NexoraDate) window.NexoraDate.sync(el);
    qtsOnDateChange();
}

function qtsRenderTasksBox(d) {
    var tasks = Array.isArray(d.today_tasks) ? d.today_tasks : [];
    var statuses = Array.isArray(d.task_statuses) ? d.task_statuses : [];
    var box = document.getElementById('qts-tasks-box');
    if (!box) return;
    if (tasks.length === 0) {
        box.style.display = 'none';
        return;
    }
    $('#qts-tasks-count').text(tasks.length);

    var html = tasks.map(function (t) {
        var opts = statuses.map(function (st) {
            var sel = String(st.id) === String(t.task_status_id || '') ? ' selected' : '';
            return '<option value="' + st.id + '"' + sel + '>' + escHtml(st.name) + '</option>';
        }).join('');

        var dueBadge = t.due_date
            ? '<span class="qts-task-due' + (t.is_overdue ? ' overdue' : '') + '">' + (t.is_overdue ? 'Overdue: ' : 'Due: ') + escHtml(t.due_date) + '</span>'
            : '';
        var updBadge = t.updated_today
            ? '<span class="qts-task-upd done"><i class="bi bi-check2"></i> Updated Today</span>'
            : '<span class="qts-task-upd">No update today</span>';

        return '<div class="qts-task-row" id="qts-task-row-' + t.id + '">' +
            '<div class="qts-task-top">' +
                '<div class="qts-task-name">' + escHtml(t.title || 'Untitled Task') + '</div>' +
                '<button type="button" class="qts-task-use" data-title="' + escHtml(t.title || '') + '" onclick="qtsCopyTaskToDesc(this.getAttribute(\'data-title\'))" title="Copy task into Timesheet description"><i class="bi bi-arrow-down-circle"></i> Use</button>' +
            '</div>' +
            '<div class="qts-task-meta">' + dueBadge + updBadge + '</div>' +
            '<div class="qts-task-controls">' +
                '<select class="form-select form-select-sm qts-task-status-sel" data-nx-select="false" onchange="qtsUpdateTaskStatus(' + t.id + ', this.value)">' + opts + '</select>' +
                '<input type="text" class="qts-task-rem-in" id="qts-task-rem-' + t.id + '" placeholder="' + (t.latest_remark ? 'Last: ' + escHtml(t.latest_remark) : 'Quick remark...') + '">' +
                '<button type="button" class="qts-task-rem-save" onclick="qtsSaveTaskRemark(' + t.id + ')">Save</button>' +
            '</div>' +
        '</div>';
    }).join('');

    $('#qts-tasks-list').html(html);
    box.style.display = 'block';
    document.getElementById('qts-tasks-list').style.display = window.qtsTasksExpanded ? 'flex' : 'none';
    document.getElementById('qts-tasks-chev').className = window.qtsTasksExpanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
}

function qtsToggleTasks() {
    window.qtsTasksExpanded = !window.qtsTasksExpanded;
    var list = document.getElementById('qts-tasks-list');
    var chev = document.getElementById('qts-tasks-chev');
    if (list) list.style.display = window.qtsTasksExpanded ? 'flex' : 'none';
    if (chev) chev.className = window.qtsTasksExpanded ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
}

function qtsCopyTaskToDesc(text) {
    if (!text) return;
    var cur = ($('#qts-description').val() || '').trim();
    $('#qts-description').val(cur ? (cur + '; ' + text) : text).focus();
}

function qtsUpdateTaskStatus(taskId, statusId) {
    $.ajax({
        url: '/task/' + taskId + '/update-status-id',
        method: 'POST',
        data: { _token: qtsGetCsrf(), task_status_id: statusId },
        success: function () {
            showDashAttToast('success', 'Task status updated.');
            loadQuickTimesheetWidget(false, 0);
        },
        error: function (xhr) {
            showDashAttToast('error', (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update task status.');
        }
    });
}

function qtsSaveTaskRemark(taskId) {
    var input = document.getElementById('qts-task-rem-' + taskId);
    var remark = input ? input.value.trim() : '';
    if (!remark) {
        if (input) input.focus();
        return;
    }
    $.ajax({
        url: window.qtsRoutes.saveRemark,
        method: 'POST',
        data: { _token: qtsGetCsrf(), task_id: taskId, remark: remark },
        success: function () {
            if (input) input.value = '';
            showDashAttToast('success', 'Task remark saved.');
            loadQuickTimesheetWidget(false, 0);
        },
        error: function (xhr) {
            showDashAttToast('error', (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save remark.');
        }
    });
}

function qtsRenderRecentChips(d) {
    var combos = Array.isArray(d.recent_combos) ? d.recent_combos : [];
    var wrap = document.getElementById('qts-recent-wrap');
    if (!wrap) return;
    if (combos.length === 0) {
        wrap.style.display = 'none';
        return;
    }
    var html = combos.slice(0, 4).map(function (c, idx) {
        var proj = c.customer_project_name || c.service_name || '';
        var cust = c.customer_name || '';
        var label = cust + (proj ? ' • ' + proj : '');
        return '<button type="button" class="qts-recent-chip" onclick="qtsApplyRecentCombo(' + idx + ')" title="' + escHtml(cust + ' — ' + proj + ' (' + (c.module_name || '') + ')') + '">' +
            escHtml(label) +
        '</button>';
    }).join('');
    $('#qts-recent-chips').html(html);
    wrap.style.display = 'flex';
}

function qtsApplyRecentCombo(idx) {
    var combos = (window.qtsData && Array.isArray(window.qtsData.recent_combos)) ? window.qtsData.recent_combos : [];
    var c = combos[idx];
    if (!c || !c.customer_id) return;

    var doChain = function () {
        $('#qts-customer').val(String(c.customer_id));
        qtsSyncSelect('#qts-customer');
        qtsLoadProjects(c.customer_id, function () {
            if (c.customer_project_name) {
                $('#qts-project').val(String(c.customer_project_name));
                qtsSyncSelect('#qts-project');
            }
            qtsLoadServices(c.customer_id, c.customer_project_name || '', function () {
                if (c.service_id) {
                    $('#qts-service').val(String(c.service_id));
                    qtsSyncSelect('#qts-service');
                    qtsLoadModules(c.service_id, function () {
                        if (c.module_id) {
                            $('#qts-module').val(String(c.module_id));
                            qtsSyncSelect('#qts-module');
                        }
                    });
                }
            });
        });
    };

    if (!window.qtsCustomersLoaded) {
        qtsLoadCustomers(doChain);
    } else {
        doChain();
    }
}

function qtsLoadCustomers(cb) {
    $.get(window.qtsRoutes.customers, function (data) {
        var html = '<option value="">Select Customer</option>';
        (data || []).forEach(function (c) {
            html += '<option value="' + c.id + '">' + escHtml(c.name) + '</option>';
        });
        $('#qts-customer').html(html);
        qtsSyncSelect('#qts-customer');
        window.qtsCustomersLoaded = true;
        if (typeof cb === 'function') cb();
    });
}

function qtsLoadProjects(customerId, cb) {
    $('#qts-project').html('<option value="">Loading...</option>');
    $('#qts-service').html('<option value="">Select Service</option>');
    $('#qts-module').html('<option value="">Select Module</option>');
    qtsSyncSelect('#qts-project');
    qtsSyncSelect('#qts-service');
    qtsSyncSelect('#qts-module');
    if (!customerId) {
        $('#qts-project').html('<option value="">Select Project</option>');
        qtsSyncSelect('#qts-project');
        return;
    }
    $.get(window.qtsRoutes.projectsByCustomer + '/' + encodeURIComponent(customerId), function (data) {
        var html = '<option value="">Select Project</option>';
        (data || []).forEach(function (p) {
            html += '<option value="' + escHtml(p.name) + '">' + escHtml(p.name) + '</option>';
        });
        $('#qts-project').html(html);
        qtsSyncSelect('#qts-project');
        if (typeof cb === 'function') cb();
    });
}

function qtsLoadServices(customerId, projectName, cb) {
    $('#qts-service').html('<option value="">Loading...</option>');
    $('#qts-module').html('<option value="">Select Module</option>');
    qtsSyncSelect('#qts-service');
    qtsSyncSelect('#qts-module');
    if (!customerId || !projectName) {
        $('#qts-service').html('<option value="">Select Service</option>');
        qtsSyncSelect('#qts-service');
        return;
    }
    $.get(window.qtsRoutes.servicesByCustomer + '/' + encodeURIComponent(customerId), { project_name: projectName }, function (data) {
        var html = '<option value="">Select Service</option>';
        (data || []).forEach(function (s) {
            html += '<option value="' + s.id + '">' + escHtml(s.name) + '</option>';
        });
        $('#qts-service').html(html);
        qtsSyncSelect('#qts-service');
        if (typeof cb === 'function') cb();
    });
}

function qtsLoadModules(serviceId, cb) {
    $('#qts-module').html('<option value="">Loading...</option>');
    qtsSyncSelect('#qts-module');
    if (!serviceId) {
        $('#qts-module').html('<option value="">Select Module</option>');
        qtsSyncSelect('#qts-module');
        return;
    }
    $.get(window.qtsRoutes.modulesByService + '/' + encodeURIComponent(serviceId), function (data) {
        var html = '<option value="">Select Module</option>';
        (data || []).forEach(function (m) {
            html += '<option value="' + m.id + '">' + escHtml(m.name) + '</option>';
        });
        $('#qts-module').html(html);
        qtsSyncSelect('#qts-module');
        if (typeof cb === 'function') cb();
    });
}

function qtsSetPresetTime(h, m) {
    $('#qts-hours').val(h);
    $('#qts-minutes').val(m);
    qtsRenderProgress();
}

function qtsFillRemaining() {
    var d = window.qtsData || {};
    var entries = Array.isArray(d.session_entries) ? d.session_entries : [];
    var selectedDate = $('#qts-date').val();
    var matching = entries.filter(function (e) { return e.work_date === selectedDate; });
    var draftMins = matching.reduce(function (s, e) { return s + (Number(e.total_minutes) || 0); }, 0);
    var expHrs = Number($('#qts-entry-type option:selected').attr('data-hours')) || 0;
    var rem = Math.max(0, Math.round(expHrs * 60) - draftMins);
    $('#qts-hours').val(Math.floor(rem / 60));
    $('#qts-minutes').val(rem % 60);
    qtsRenderProgress();
}

function qtsRenderDrafts(d, selectedDate) {
    var entries = Array.isArray(d.session_entries) ? d.session_entries : [];
    $('#qts-draft-count').text(entries.length);
    $('#qts-clear-btn').toggle(entries.length > 0);

    var mismatchBox = $('#qts-date-mismatch');
    if (entries.length > 0 && selectedDate && entries[0].work_date !== selectedDate) {
        mismatchBox.html(
            '<span><i class="bi bi-exclamation-circle"></i> Draft holds entries for <strong>' + escHtml(qtsFmtShortDate(entries[0].work_date)) + '</strong>.</span>' +
            '<button type="button" onclick="qtsClearDraft()">Clear old draft</button>'
        ).show();
    } else {
        mismatchBox.hide().empty();
    }

    if (entries.length === 0) {
        $('#qts-draft-list').html('<div class="qts-draft-empty">No entries in draft yet. Add your work above.</div>');
        return;
    }

    var html = entries.map(function (entry) {
        return '<div class="qts-draft-item">' +
            '<div class="qts-draft-info">' +
                '<div class="qts-draft-top">' +
                    '<strong>' + escHtml(entry.customer_name || 'N/A') + '</strong>' +
                    '<span class="qts-draft-proj">• ' + escHtml(entry.customer_project_name || entry.service_name || 'N/A') + '</span>' +
                    '<span class="qts-draft-dur">' + Number(entry.hours || 0) + 'h ' + Number(entry.minutes || 0) + 'm</span>' +
                '</div>' +
                '<div class="qts-draft-mod">' + escHtml(entry.service_name || '') + ' / ' + escHtml(entry.module_name || '') + ' <span class="text-muted">(' + escHtml(qtsFmtShortDate(entry.work_date)) + ')</span></div>' +
                '<div class="qts-draft-desc">' + escHtml(entry.description || '') + '</div>' +
            '</div>' +
            '<button type="button" class="qts-draft-del" onclick="qtsRemoveEntry(\'' + escHtml(entry.id) + '\')" title="Remove"><i class="bi bi-trash"></i></button>' +
        '</div>';
    }).join('');
    $('#qts-draft-list').html(html);
}

function qtsRenderProgress() {
    var d = window.qtsData || {};
    var entries = Array.isArray(d.session_entries) ? d.session_entries : [];
    var selectedDate = $('#qts-date').val();
    var sameDate = entries.filter(function (e) { return !selectedDate || e.work_date === selectedDate; });
    var draftMins = sameDate.reduce(function (s, e) { return s + (Number(e.total_minutes) || 0); }, 0);
    var expHrs = Number($('#qts-entry-type option:selected').attr('data-hours')) || 8;
    var expMins = Math.round(expHrs * 60);
    var pct = expMins > 0 ? Math.min(100, Math.round((draftMins / expMins) * 100)) : 0;
    var rem = Math.max(0, expMins - draftMins);

    $('#qts-progress-lbl').html('Logged <strong>' + qtsFmtMins(draftMins) + '</strong> / ' + qtsFmtMins(expMins));
    var fill = document.getElementById('qts-progress-fill');
    if (fill) {
        fill.style.width = pct + '%';
        fill.className = 'qts-progress-fill' + (draftMins >= expMins && expMins > 0 ? ' is-complete' : '');
    }

    if (rem > 0) {
        $('#qts-fill-rem-btn').show().text('Fill ' + qtsFmtMins(rem));
    } else {
        $('#qts-fill-rem-btn').hide();
    }

    // Strict Submit Button Disable Rules + Clear CTA State
    var hasMismatch = entries.length > 0 && selectedDate && entries[0].work_date !== selectedDate;
    var needsPunchOut = (selectedDate === d.today && !d.today_punched_out);
    var insufficientHours = (expMins > 0 && draftMins < expMins);
    var isDisabled = (entries.length === 0 || hasMismatch || insufficientHours || needsPunchOut);

    var submitBtn = document.getElementById('qts-submit-btn');
    if (submitBtn) {
        submitBtn.disabled = isDisabled;
        if (hasMismatch) {
            submitBtn.innerHTML = 'Clear Mismatched Draft First';
        } else if (entries.length === 0) {
            submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Submit Timesheet';
        } else if (insufficientHours) {
            submitBtn.innerHTML = 'Add ' + escHtml(qtsFmtMins(rem)) + ' More to Submit';
        } else if (needsPunchOut) {
            submitBtn.innerHTML = 'Punch Out Required to Submit';
        } else {
            submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Submit Timesheet';
        }
    }

    var hintEl = $('#qts-footer-hint');
    if (needsPunchOut) {
        var isMobile = window.latestMyAttendance && window.latestMyAttendance.can_web_punch === false;
        if (isMobile) {
            hintEl.html('<i class="bi bi-phone"></i> Punch out from <strong>Mobile App</strong> before final submit (draft is saved).').show();
        } else {
            hintEl.html('<i class="bi bi-info-circle"></i> Punch out first before submitting today\'s timesheet (draft stays saved).').show();
        }
    } else {
        hintEl.hide().empty();
    }
}

function qtsAddEntry(e) {
    if (e) e.preventDefault();
    var workDate = $('#qts-date').val();
    var entryTypeId = $('#qts-entry-type').val();
    var customerId = $('#qts-customer').val();
    var projectName = $('#qts-project').val();
    var serviceId = $('#qts-service').val();
    var moduleId = $('#qts-module').val();
    var hours = parseInt($('#qts-hours').val(), 10) || 0;
    var minutes = parseInt($('#qts-minutes').val(), 10) || 0;
    var desc = ($('#qts-description').val() || '').trim();

    if (!workDate || !entryTypeId || !customerId || !projectName || !serviceId || !moduleId || !desc) {
        showDashAttToast('error', 'Please fill all required timesheet fields.');
        return;
    }
    if (hours === 0 && minutes === 0) {
        showDashAttToast('error', 'Please enter hours or minutes spent.');
        return;
    }

    var btn = $('#qts-add-btn');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Adding...');

    $.ajax({
        url: window.qtsRoutes.addToSession,
        method: 'POST',
        data: {
            _token: qtsGetCsrf(),
            work_date: workDate,
            entry_type_id: entryTypeId,
            customer_id: customerId,
            project_name: projectName,
            service_id: serviceId,
            module_id: moduleId,
            hours: hours,
            minutes: minutes,
            description: desc
        },
        success: function (res) {
            btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Add to Draft');
            if (res && res.success) {
                $('#qts-description').val('');
                $('#qts-hours').val(0);
                $('#qts-minutes').val(0);
                showDashAttToast('success', res.message || 'Entry added to draft.');
                loadQuickTimesheetWidget(false, 0);
            } else {
                showDashAttToast('error', (res && res.message) ? res.message : 'Unable to add entry.');
            }
        },
        error: function (xhr) {
            btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Add to Draft');
            var err = xhr.responseJSON;
            var msg = (err && err.message) ? err.message : 'Failed to add entry.';
            if (err && err.errors) {
                msg = Object.values(err.errors).flat().join(', ');
            }
            showDashAttToast('error', msg);
        }
    });
}

function qtsRemoveEntry(entryId) {
    $.ajax({
        url: window.qtsRoutes.removeFromSession,
        method: 'POST',
        data: { _token: qtsGetCsrf(), entry_id: entryId },
        success: function () {
            loadQuickTimesheetWidget(false, 0);
        }
    });
}

function qtsClearDraft() {
    $.ajax({
        url: window.qtsRoutes.clearSession,
        method: 'POST',
        data: { _token: qtsGetCsrf() },
        success: function () {
            showDashAttToast('success', 'Draft cleared.');
            loadQuickTimesheetWidget(false, 0);
        }
    });
}

function qtsSubmit() {
    var btn = $('#qts-submit-btn');
    if (btn.prop('disabled')) return;
    var workDate = $('#qts-date').val();
    var entryTypeId = $('#qts-entry-type').val();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

    $.ajax({
        url: window.qtsRoutes.submit,
        method: 'POST',
        data: {
            _token: qtsGetCsrf(),
            work_date: workDate,
            entry_type_id: entryTypeId
        },
        success: function (res) {
            btn.html('<i class="bi bi-check2-circle"></i> Submit Timesheet');
            if (res && res.success) {
                showDashAttToast('success', res.message || 'Timesheet submitted successfully!');
                closeQuickTimesheetDrawer();
                loadQuickTimesheetWidget(false, 0);
                refreshMyAttendance();
            } else {
                qtsRenderProgress();
                showDashAttToast('error', (res && res.message) ? res.message : 'Submission failed.');
            }
        },
        error: function (xhr) {
            qtsRenderProgress();
            var err = xhr.responseJSON;
            var msg = (err && err.message) ? err.message : 'Failed to submit timesheet.';
            showDashAttToast('error', msg);
        }
    });
}

$(document).on('change', '#qts-customer', function () {
    qtsLoadProjects(this.value);
});
$(document).on('change', '#qts-project', function () {
    qtsLoadServices($('#qts-customer').val(), this.value);
});
$(document).on('change', '#qts-service', function () {
    qtsLoadModules(this.value);
});

function showAttPopover(type, anchorEl) {
    if (typeof hideApPopover === 'function') hideApPopover(true);
    if (typeof hideCalPopover === 'function') hideCalPopover(true);
    clearTimeout(attPopTimer);
    var pop = document.getElementById('att-hover-popover');
    if (!pop || !anchorEl) return;

    var names = (window.attNamesData && window.attNamesData[type]) ? window.attNamesData[type] : [];
    var isPresent = type === 'present';
    var titleText = isPresent ? 'Present Today' : 'Absent Today';
    var emptyText = isPresent ? 'No employees present' : 'No employees absent';

    var listHtml = '';
    if (!names.length) {
        listHtml = '<div class="att-pop-empty">' + emptyText + '</div>';
    } else {
        listHtml = names.map(function (name) {
            var parts = String(name).trim().split(/\s+/);
            var ini = parts.map(function (p) { return p ? p[0] : ''; }).join('').substring(0, 2).toUpperCase() || '?';
            return '<div class="att-pop-row">' +
                '<span class="att-pop-av ' + type + '">' + escHtml(ini) + '</span>' +
                '<span class="att-pop-name">' + escHtml(name) + '</span>' +
                '</div>';
        }).join('');
    }

    pop.innerHTML =
        '<div class="att-pop-head">' +
            '<span class="att-pop-title"><span class="att-pop-dot ' + type + '"></span>' + titleText + '</span>' +
            '<span class="att-pop-count ' + type + '">' + names.length + '</span>' +
        '</div>' +
        '<div class="att-pop-list">' + listHtml + '</div>';

    pop.classList.add('show');
    pop.setAttribute('aria-hidden', 'false');

    var rect = anchorEl.getBoundingClientRect();
    var popW = pop.offsetWidth || 224;
    var popH = pop.offsetHeight || 180;
    var left, top;

    if (type === 'present') {
        var donutEl = anchorEl.querySelector('.att-donut-box');
        var donutRect = donutEl ? donutEl.getBoundingClientRect() : null;
        if (donutRect && donutRect.right + 10 + popW <= window.innerWidth - 12) {
            left = donutRect.right + 10;
            top = Math.max(12, Math.min(rect.top, window.innerHeight - popH - 12));
        } else {
            left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
            top = (rect.top - popH - 8 >= 12) ? (rect.top - popH - 8) : Math.min(rect.bottom + 8, window.innerHeight - popH - 12);
        }
    } else if (rect.right + 10 + popW <= window.innerWidth - 12) {
        left = rect.right + 10;
        top = Math.max(12, Math.min(rect.top, window.innerHeight - popH - 12));
    } else if (rect.left - popW - 10 >= 12) {
        left = rect.left - popW - 10;
        top = Math.max(12, Math.min(rect.top, window.innerHeight - popH - 12));
    } else {
        left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
        top = (rect.top - popH - 8 >= 12) ? (rect.top - popH - 8) : Math.min(rect.bottom + 8, window.innerHeight - popH - 12);
    }

    pop.style.left = Math.round(left) + 'px';
    pop.style.top = Math.round(top) + 'px';
}

function hideAttPopover(immediate) {
    clearTimeout(attPopTimer);
    var pop = document.getElementById('att-hover-popover');
    if (!pop) return;
    if (immediate) {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        return;
    }
    attPopTimer = setTimeout(function () {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
    }, 130);
}

function showApPopover(type, anchorEl) {
    hideAttPopover(true);
    if (typeof hideCalPopover === 'function') hideCalPopover(true);
    clearTimeout(apPopTimer);
    var pop = document.getElementById('ap-hover-popover');
    if (!pop || !anchorEl || !type) return;

    window.activeApPopoverType = type;
    window.activeApPopoverAnchor = anchorEl;

    var metaMap = {
        ts: { title: 'Pending Timesheets', icon: 'bi-clock', iconClass: 'ap-ic-ts', empty: 'No pending timesheets' },
        at: { title: 'Pending Attendance', icon: 'bi-calendar-check', iconClass: 'ap-ic-at', empty: 'No pending attendance' },
        tk: { title: 'Pending Tasks', icon: 'bi-check2-square', iconClass: 'ap-ic-tk', empty: 'No pending tasks' },
        lv: { title: 'Pending Leaves', icon: 'bi-person', iconClass: 'ap-ic-lv', empty: 'No pending leaves' },
        pc: { title: 'Pending Petty Cash', icon: 'bi-wallet2', iconClass: 'ap-ic-pc', empty: 'No pending petty cash' }
    };
    var meta = metaMap[type];
    if (!meta) return;

    var entry = (window.approvalPreviewData && window.approvalPreviewData[type])
        ? window.approvalPreviewData[type]
        : { count: 0, list: [] };
    var list = Array.isArray(entry.list) ? entry.list : [];
    var count = Number(entry.count) || list.length;

    var bodyHtml = '';
    if (!list.length) {
        bodyHtml =
            '<div class="ap-pop-empty">' +
                '<i class="bi bi-check-circle"></i>' +
                '<span>' + meta.empty + '</span>' +
            '</div>';
    } else {
        bodyHtml = '<div class="ap-pop-list">' + list.map(function (item) {
            var info = formatApprovalItem(type, item);
            var btnHtml = '';
            if (type === 'tk') {
                btnHtml = '<span class="btn-approve-pill ap-pop-approve" onclick="event.stopPropagation();approveTask(\'' + info.safeId + '\', this)">Approve</span>';
            } else if (type !== 'ts') {
                btnHtml = '<span class="btn-approve-pill ap-pop-approve" onclick="event.stopPropagation();approveItem(\'' + type + '\', \'' + info.safeId + '\', this)">Approve</span>';
            }
            return '<div class="ap-pop-item">' +
                '<div class="ap-pop-item-info">' +
                    '<div class="ap-pop-item-title">' + info.title + '</div>' +
                    '<div class="ap-pop-item-sub">' + info.sub + '</div>' +
                '</div>' +
                btnHtml +
            '</div>';
        }).join('') + '</div>';
    }

    var footHtml = '';
    if (count > 0 || list.length > 0) {
        var footHint = count > list.length
            ? ('Showing latest ' + list.length + ' of ' + count)
            : (count + ' pending');
        footHtml =
            '<div class="ap-pop-foot">' +
                '<span>' + escHtml(footHint) + '</span>' +
                '<a class="ap-pop-link" href="' + escHtml(getApprovalRoute(type)) + '">View All →</a>' +
            '</div>';
    }

    var badgeClass = count > 0 ? 'has-items' : 'is-zero';
    pop.innerHTML =
        '<div class="ap-pop-head">' +
            '<span class="ap-pop-head-left">' +
                '<span class="ap-pop-head-icon ' + meta.iconClass + '"><i class="bi ' + meta.icon + '"></i></span>' +
                '<span class="ap-pop-title">' + meta.title + '</span>' +
            '</span>' +
            '<span class="ap-pop-badge ' + badgeClass + '">' + count + '</span>' +
        '</div>' +
        bodyHtml +
        footHtml;

    pop.classList.add('show');
    pop.setAttribute('aria-hidden', 'false');

    var rect = anchorEl.getBoundingClientRect();
    var popW = pop.offsetWidth || 344;
    var popH = pop.offsetHeight || 220;
    var left, top;

    if (rect.right + 10 + popW <= window.innerWidth - 12) {
        left = rect.right + 10;
        top = Math.max(12, Math.min(rect.top - 10, window.innerHeight - popH - 12));
    } else if (rect.left - popW - 10 >= 12) {
        left = rect.left - popW - 10;
        top = Math.max(12, Math.min(rect.top - 10, window.innerHeight - popH - 12));
    } else {
        left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
        top = (rect.top - popH - 8 >= 12) ? (rect.top - popH - 8) : Math.min(rect.bottom + 8, window.innerHeight - popH - 12);
    }

    pop.style.left = Math.round(left) + 'px';
    pop.style.top = Math.round(top) + 'px';
}

function hideApPopover(immediate) {
    clearTimeout(apPopTimer);
    var pop = document.getElementById('ap-hover-popover');
    if (!pop) return;
    if (immediate) {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        window.activeApPopoverType = null;
        window.activeApPopoverAnchor = null;
        return;
    }
    apPopTimer = setTimeout(function () {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        window.activeApPopoverType = null;
        window.activeApPopoverAnchor = null;
    }, 130);
}

var calPopTimer = null;
var calShowTimer = null;

function showCalPopover(ds, anchorEl) {
    hideAttPopover(true);
    hideApPopover(true);
    clearTimeout(calPopTimer);
    clearTimeout(calShowTimer);

    var pop = document.getElementById('cal-hover-popover');
    if (!pop || !anchorEl || !ds) return;
    var calModal = document.getElementById('modal-cal');
    if (calModal && calModal.style.display === 'flex') return;

    var renderAndPosition = function () {
        pop.innerHTML = buildCalDateHtml(ds, true);
        pop.classList.add('show');
        pop.setAttribute('aria-hidden', 'false');

        var rect = anchorEl.getBoundingClientRect();
        var popW = pop.offsetWidth || 296;
        var popH = pop.offsetHeight || 120;
        var left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
        var top = (rect.top - popH - 10 >= 12)
            ? (rect.top - popH - 10)
            : Math.min(rect.bottom + 10, window.innerHeight - popH - 12);

        pop.style.left = Math.round(left) + 'px';
        pop.style.top = Math.round(top) + 'px';
    };

    if (pop.classList.contains('show')) {
        renderAndPosition();
    } else {
        calShowTimer = setTimeout(renderAndPosition, 70);
    }
}

function hideCalPopover(immediate) {
    clearTimeout(calShowTimer);
    clearTimeout(calPopTimer);
    var pop = document.getElementById('cal-hover-popover');
    if (!pop) return;
    if (immediate) {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        return;
    }
    calPopTimer = setTimeout(function () {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
    }, 130);
}

var evPopTimer = null;
var evShowTimer = null;

function showEventPopover(kind, idx, anchorEl) {
    clearTimeout(evPopTimer);
    clearTimeout(evShowTimer);
    var pop = document.getElementById('event-hover-popover');
    if (!pop || !anchorEl) return;

    var list = kind === 'holiday' ? (window.dashHolidaysList || []) : (window.dashCelebrationsList || []);
    var item = list[Number(idx)];
    if (!item) return;

    var buildHtml = function () {
        if (kind === 'holiday') {
            var d = new Date(item.holiday_date);
            var fullDate = d.toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            var initial = (item.name || 'H').split(' ').filter(Boolean).map(function (n) { return n[0]; }).join('').substring(0, 2).toUpperCase();
            var color = item.is_rh ? '#3B82F6' : '#10B981';
            var pillBg = item.is_rh ? '#EFF6FF' : '#ECFDF5';
            var pillColor = item.is_rh ? '#1D4ED8' : '#047857';
            var typeLabel = item.is_rh ? '🔵 Restricted Holiday' : '🌴 Public Holiday';
            return '<div class="ev-pop-head">' +
                '<div class="ev-pop-avatar" style="background:' + color + ';">' + escHtml(initial) + '</div>' +
                '<div class="ev-pop-title-wrap">' +
                    '<div class="ev-pop-name">' + escHtml(item.name || 'Holiday') + '</div>' +
                    '<span class="ev-pop-pill" style="background:' + pillBg + ';color:' + pillColor + ';">' + typeLabel + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="ev-pop-meta"><i class="bi bi-calendar3"></i><span>' + escHtml(fullDate) + '</span></div>';
        }

        var cd = new Date(item.date);
        var dayMonth = cd.toLocaleDateString('en-IN', { day: 'numeric', month: 'long' });
        var initialC = (item.name || 'U').split(' ').filter(Boolean).map(function (n) { return n[0]; }).join('').substring(0, 2).toUpperCase();
        var icon = '🎉', colorC = '#8B5CF6', pillBgC = '#F5F3FF', labelColorC = '#6D28D9';
        if (item.type === 'birthday') { icon = '🎂'; colorC = '#EC4899'; pillBgC = '#FCE7F3'; labelColorC = '#BE185D'; }
        else if (item.type === 'work_anniversary') { icon = '💼'; colorC = '#434AFA'; pillBgC = '#EEF2FF'; labelColorC = '#434AFA'; }
        else if (item.type === 'anniversary') { icon = '💍'; colorC = '#F43F5E'; pillBgC = '#FFE4E6'; labelColorC = '#BE123C'; }
        var daysNum = typeof item.days_until === 'number' ? item.days_until : null;
        var countdown = daysNum === 0 ? 'Today 🎉' : (daysNum === 1 ? 'Tomorrow' : (daysNum !== null ? ('In ' + daysNum + ' days') : 'Upcoming'));

        return '<div class="ev-pop-head">' +
            '<div class="ev-pop-avatar" style="background:' + colorC + ';">' + escHtml(initialC) + '</div>' +
            '<div class="ev-pop-title-wrap">' +
                '<div class="ev-pop-name">' + escHtml(item.name || 'Employee') + '</div>' +
                '<span class="ev-pop-pill" style="background:' + pillBgC + ';color:' + labelColorC + ';">' + icon + ' ' + escHtml(item.label || 'Celebration') + '</span>' +
            '</div>' +
        '</div>' +
        '<div class="ev-pop-meta">' +
            '<span><i class="bi bi-calendar-heart"></i> ' + escHtml(dayMonth) + '</span>' +
            '<span class="ev-pop-dot">•</span>' +
            '<strong>' + escHtml(countdown) + '</strong>' +
        '</div>';
    };

    var renderAndPosition = function () {
        pop.innerHTML = buildHtml();
        pop.classList.add('show');
        pop.setAttribute('aria-hidden', 'false');

        var rect = anchorEl.getBoundingClientRect();
        var popW = pop.offsetWidth || 248;
        var popH = pop.offsetHeight || 92;
        var left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
        var top = (rect.top - popH - 8 >= 12)
            ? (rect.top - popH - 8)
            : Math.min(rect.bottom + 8, window.innerHeight - popH - 12);

        pop.style.left = Math.round(left) + 'px';
        pop.style.top = Math.round(top) + 'px';
    };

    if (pop.classList.contains('show')) {
        renderAndPosition();
    } else {
        evShowTimer = setTimeout(renderAndPosition, 90);
    }
}

function hideEventPopover(immediate) {
    clearTimeout(evShowTimer);
    clearTimeout(evPopTimer);
    var pop = document.getElementById('event-hover-popover');
    if (!pop) return;
    if (immediate) {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        return;
    }
    evPopTimer = setTimeout(function () {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
    }, 120);
}

var leavePopTimer = null;
var leaveShowTimer = null;

function showLeavePopover(idx, anchorEl) {
    clearTimeout(leavePopTimer);
    clearTimeout(leaveShowTimer);
    var pop = document.getElementById('leave-hover-popover');
    if (!pop || !anchorEl) return;

    var list = window.dashLeaveBalances || [];
    var item = list[Number(idx)];
    if (!item) return;

    var nameLower = (item.name || '').toLowerCase();
    var isEarnedLeave = nameLower.includes('earned');
    var isHolidayWorking = nameLower.includes('holiday') || nameLower.includes('working') || nameLower.includes('comp');

    var icon = '🌿';
    var iconBg = '#10B981';
    var tagPill = '<span class="ev-pop-pill" style="background:#ECFDF5;color:#047857;">Paid Leave</span>';
    var note = '<div style="margin-top:6px;font-size:11.5px;color:#64748B;">Available leave balance for personal use.</div>';

    if (isEarnedLeave) {
        icon = '🌿';
        iconBg = '#10B981';
        tagPill = '<span class="ev-pop-pill" style="background:#ECFDF5;color:#047857;">Earned Leave (EL)</span>';
        var elDays = window.dashEarnedLeaveDays !== undefined ? window.dashEarnedLeaveDays : '{{ $earnedLeaveDays }}';
        var pct = Math.min(100, Math.round((parseFloat(elDays) / 45) * 100));
        note = '<div style="margin-top:8px;padding-top:8px;border-top:1px solid #F1F5F9;font-size:11.5px;color:#64748B;">' +
            '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">' +
                '<span style="font-weight:500;">Accrual Progress:</span>' +
                '<strong style="color:#09090B;font-variant-numeric:tabular-nums;">' + escHtml(String(elDays)) + ' / 45 Days</strong>' +
            '</div>' +
            '<div style="background:#E2E8F0;height:5px;border-radius:999px;overflow:hidden;margin-bottom:6px;">' +
                '<div style="background:#10B981;height:100%;width:' + pct + '%;border-radius:999px;"></div>' +
            '</div>' +
            '<div style="font-size:10.5px;color:#94A3B8;line-height:1.35;">1 Earned Leave is credited after completing 45 working days.</div>' +
        '</div>';
    } else if (isHolidayWorking) {
        icon = '🏖️';
        iconBg = '#434AFA';
        tagPill = '<span class="ev-pop-pill" style="background:#EEF2FF;color:#434AFA;">Compensatory Off</span>';
        note = '<div style="margin-top:8px;padding-top:8px;border-top:1px solid #F1F5F9;font-size:11.5px;color:#64748B;">' +
            '<div style="font-size:11px;color:#475569;line-height:1.4;">Leave balance credited for working on scheduled company holidays or designated weekly offs.</div>' +
            '<div style="font-size:10.5px;color:#94A3B8;margin-top:4px;">Can be applied as a Comp-Off leave request.</div>' +
        '</div>';
    }

    var html = '<div class="ev-pop-head">' +
        '<div class="ev-pop-avatar" style="background:' + iconBg + ';font-size:15px;">' + icon + '</div>' +
        '<div class="ev-pop-title-wrap">' +
            '<div class="ev-pop-name" style="font-size:13px;font-weight:700;">' + escHtml(item.name) + '</div>' +
            tagPill +
        '</div>' +
    '</div>' +
    '<div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;padding:6px 10px;background:#F8FAFC;border-radius:8px;border:1px solid #E4E7EC;">' +
        '<span style="font-size:11.5px;color:#64748B;font-weight:500;">Available Balance:</span>' +
        '<strong style="font-size:13.5px;color:#434AFA;font-weight:700;">' + escHtml(String(item.balance)) + (item.balance == 1 ? ' Day' : ' Days') + '</strong>' +
    '</div>' +
    note;

    var renderAndPosition = function () {
        pop.innerHTML = html;
        pop.classList.add('show');
        pop.setAttribute('aria-hidden', 'false');

        var rect = anchorEl.getBoundingClientRect();
        var popW = pop.offsetWidth || 275;
        var popH = pop.offsetHeight || 110;
        var left = Math.max(12, Math.min(rect.left + (rect.width / 2) - (popW / 2), window.innerWidth - popW - 12));
        var top = (rect.top - popH - 8 >= 12)
            ? (rect.top - popH - 8)
            : Math.min(rect.bottom + 8, window.innerHeight - popH - 12);

        pop.style.left = Math.round(left) + 'px';
        pop.style.top = Math.round(top) + 'px';
    };

    if (pop.classList.contains('show')) {
        renderAndPosition();
    } else {
        leaveShowTimer = setTimeout(renderAndPosition, 80);
    }
}

function hideLeavePopover(immediate) {
    clearTimeout(leaveShowTimer);
    clearTimeout(leavePopTimer);
    var pop = document.getElementById('leave-hover-popover');
    if (!pop) return;
    if (immediate) {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
        return;
    }
    leavePopTimer = setTimeout(function () {
        pop.classList.remove('show');
        pop.setAttribute('aria-hidden', 'true');
    }, 120);
}

var WORK_FLASH_DURATION = 5000;
var wfItems = [];
var wfIndex = 0;
var wfTimer = null;
var wfDebounceTimer = null;
var wfRemainingMs = WORK_FLASH_DURATION;
var wfStartedAt = 0;
var wfHovered = false;
var wfMenuOpen = false;

function getWorkFlashStorageKey() {
    return 'workorio_workflash_hide_until_' + String(window.currentDashUserId || 'guest');
}

function getWorkFlashHideUntil() {
    try {
        var raw = localStorage.getItem(getWorkFlashStorageKey());
        var ts = raw ? parseInt(raw, 10) : 0;
        return isNaN(ts) ? 0 : ts;
    } catch (e) {
        return 0;
    }
}

function isWorkFlashSnoozed() {
    var until = getWorkFlashHideUntil();
    if (!until) return false;
    if (Date.now() < until) return true;
    try {
        localStorage.removeItem(getWorkFlashStorageKey());
    } catch (e) {}
    return false;
}

function closeWorkFlashSnoozeMenu() {
    var menu = document.getElementById('wf-snooze-menu');
    if (menu) menu.classList.remove('open');
    wfMenuOpen = false;
}

function toggleWorkFlashSnooze(e) {
    if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
    var menu = document.getElementById('wf-snooze-menu');
    if (!menu) return;
    var willOpen = !menu.classList.contains('open');
    menu.classList.toggle('open', willOpen);
    wfMenuOpen = willOpen;
    if (willOpen) {
        pauseWorkFlash(true);
    } else {
        resumeWorkFlash();
    }
}

function snoozeWorkFlash(mode) {
    closeWorkFlashSnoozeMenu();
    wfHovered = false;
    var now = new Date();
    var until = now.getTime() + 3600000;
    if (mode === '3h') {
        until = now.getTime() + 3 * 3600000;
    } else if (mode === '6h') {
        until = now.getTime() + 6 * 3600000;
    } else if (mode === 'day') {
        until = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999).getTime();
    }
    try {
        localStorage.setItem(getWorkFlashStorageKey(), String(until));
    } catch (e) {}

    clearTimeout(wfTimer);
    var box = document.getElementById('work-flash-box');
    var restore = document.getElementById('work-flash-restore');
    var restoreCnt = document.getElementById('wf-restore-count');
    if (box) box.style.display = 'none';
    if (restore && wfItems.length > 0) {
        if (restoreCnt) restoreCnt.textContent = String(wfItems.length);
        restore.style.display = 'inline-flex';
    }
}

function unhideWorkFlash() {
    try {
        localStorage.removeItem(getWorkFlashStorageKey());
    } catch (e) {}
    wfHovered = false;
    wfMenuOpen = false;
    refreshWorkFlash(true);
}

function buildWorkFlashItems() {
    var p = window.userDashPerms || {};
    var d = window.workFlashData || {};
    var r = window.workFlashRoutes || {};
    var items = [];

    if (p.calendar && d.todayFlyers && d.todayFlyers.items && d.todayFlyers.items.length) {
        var flyCount = d.todayFlyers.items.length;
        var firstFly = d.todayFlyers.items[0] ? (d.todayFlyers.items[0].title || 'Scheduled post') : 'Scheduled post';
        var subFly = flyCount > 1 ? (firstFly + ' +' + (flyCount - 1) + ' more today') : firstFly;
        var flyDs = d.todayFlyers.ds;
        items.push({
            key: 'flyers',
            theme: 'wf-pink',
            kicker: "TODAY'S FLYERS",
            icon: 'bi-calendar2-week',
            title: flyCount + (flyCount === 1 ? ' Flyer / Reel Today' : ' Flyers & Reels Today'),
            sub: subFly,
            actionText: 'Open Calendar →',
            onAction: function () {
                if (flyDs && typeof pickDate === 'function') {
                    pickDate(flyDs);
                }
            }
        });
    }

    if (p.tasks && d.tasksToMe && d.tasksToMe.count > 0) {
        var tkCount = d.tasksToMe.count;
        var subTk = tkCount > 1
            ? ((d.tasksToMe.topTitle || 'Task') + ' +' + (tkCount - 1) + ' more')
            : (d.tasksToMe.topTitle || 'Pending in your task list');
        items.push({
            key: 'tasks_tome',
            theme: 'wf-brand',
            kicker: 'ASSIGNED TO YOU',
            icon: 'bi-check2-square',
            title: tkCount + (tkCount === 1 ? ' Pending Task' : ' Pending Tasks'),
            sub: subTk,
            actionText: 'View Tasks →',
            onAction: function () {
                if (r.tasks && r.tasks !== '#') location.href = r.tasks;
            }
        });
    }

    if (p.tasks && p.isAdmin && d.tasksByMe && d.tasksByMe.count > 0) {
        var byCount = d.tasksByMe.count;
        var subBy = byCount > 1
            ? ((d.tasksByMe.topTitle || 'Task') + ' +' + (byCount - 1) + ' more assigned')
            : (d.tasksByMe.topTitle || 'Assigned to team members');
        items.push({
            key: 'tasks_byme',
            theme: 'wf-violet',
            kicker: 'TEAM TASKS',
            icon: 'bi-people',
            title: byCount + (byCount === 1 ? ' Task Assigned by You' : ' Tasks Assigned by You'),
            sub: subBy,
            actionText: 'Review Tasks →',
            onAction: function () {
                var target = r.tasksByMe || r.tasks;
                if (target && target !== '#') location.href = target;
            }
        });
    }

    if (p.sales && (d.salesFollowups > 0 || d.salesPending > 0)) {
        var sf = Number(d.salesFollowups) || 0;
        var sp = Number(d.salesPending) || 0;
        var salesTitle = sf > 0
            ? (sf + (sf === 1 ? ' Sales Follow-up Today' : ' Sales Follow-ups Today'))
            : (sp + (sp === 1 ? ' Pending Follow-up' : ' Pending Follow-ups'));
        var salesParts = [];
        if (sf > 0) salesParts.push(sf + ' Scheduled');
        if (sp > 0) salesParts.push(sp + ' Action Due');
        items.push({
            key: 'sales',
            theme: 'wf-amber',
            kicker: 'SALES FOLLOW-UPS',
            icon: 'bi-graph-up-arrow',
            title: salesTitle,
            sub: salesParts.join(' • '),
            actionText: 'Open Leads →',
            onAction: function () {
                location.href = sp > 0 ? r.salesPending : r.salesFollowups;
            }
        });
    }

    if (p.calling && (d.callingFollowups > 0 || d.callingPending > 0)) {
        var cf = Number(d.callingFollowups) || 0;
        var cp = Number(d.callingPending) || 0;
        var callTitle = cf > 0
            ? (cf + (cf === 1 ? ' Calling Follow-up Today' : ' Calling Follow-ups Today'))
            : (cp + (cp === 1 ? ' Pending Call Today' : ' Pending Calls Today'));
        var callParts = [];
        if (cf > 0) callParts.push(cf + ' Scheduled');
        if (cp > 0) callParts.push(cp + ' Pending');
        items.push({
            key: 'calling',
            theme: 'wf-purple',
            kicker: 'TELE-CALLING',
            icon: 'bi-telephone-outbound',
            title: callTitle,
            sub: callParts.join(' • '),
            actionText: 'Start Calling →',
            onAction: function () {
                location.href = cp > 0 ? r.callingPending : r.callingFollowups;
            }
        });
    }

    if (p.approvals && d.approvals && d.approvals.total > 0) {
        items.push({
            key: 'approvals',
            theme: 'wf-rose',
            kicker: 'APPROVALS QUEUE',
            icon: 'bi-shield-check',
            title: Number(d.approvals.total).toLocaleString('en-IN') + ' Pending Approvals',
            sub: d.approvals.summary || 'Review pending requests',
            actionText: 'Review Now →',
            onAction: function () {
                if (typeof openApprovalModal === 'function') {
                    openApprovalModal(d.approvals.firstType || 'ts');
                }
            }
        });
    }

    if (p.duePayments && d.duePayments && d.duePayments.count > 0) {
        var dpCnt = d.duePayments.count;
        var dpAmt = Number(d.duePayments.totalDue) || 0;
        items.push({
            key: 'due_payments',
            theme: 'wf-rose',
            kicker: 'DUE PAYMENTS',
            icon: 'bi-credit-card',
            title: dpCnt + (dpCnt === 1 ? ' Payment Overdue' : ' Payments Overdue'),
            sub: dpAmt > 0 ? ('Total Due: ₹' + dpAmt.toLocaleString('en-IN')) : 'Pending client invoices',
            actionText: 'View List →',
            onAction: function () {
                var listEl = document.getElementById('due-payments-list');
                var cardEl = listEl ? listEl.closest('.card') : null;
                if (cardEl && typeof cardEl.scrollIntoView === 'function') {
                    cardEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    cardEl.classList.remove('wf-target-highlight');
                    void cardEl.offsetWidth;
                    cardEl.classList.add('wf-target-highlight');
                    setTimeout(function () { cardEl.classList.remove('wf-target-highlight'); }, 1800);
                }
            }
        });
    }

    if (p.dueSubs && d.dueSubs && d.dueSubs.count > 0) {
        var subCnt = d.dueSubs.count;
        items.push({
            key: 'due_subs',
            theme: 'wf-blue',
            kicker: 'SUBSCRIPTIONS',
            icon: 'bi-arrow-repeat',
            title: subCnt + (subCnt === 1 ? ' Due Subscription' : ' Due Subscriptions'),
            sub: d.dueSubs.topName ? ('Next: ' + d.dueSubs.topName) : 'Upcoming client renewals',
            actionText: 'View All →',
            onAction: function () {
                if (r.subscriptions) location.href = r.subscriptions;
            }
        });
    }

    if (p.attendance && d.attendancePending) {
        items.push({
            key: 'attendance',
            theme: 'wf-emerald',
            kicker: 'ATTENDANCE',
            icon: 'bi-fingerprint',
            title: 'Attendance Not Marked Today',
            sub: 'Mark your daily punch-in to record In Time',
            actionText: 'Mark Now →',
            onAction: function () {
                if (r.attendance && r.attendance !== '#') location.href = r.attendance;
            }
        });
    }

    return items;
}

function restartWorkFlashProgress(durationMs) {
    var fill = document.getElementById('wf-progress-fill');
    if (!fill) return;
    fill.classList.remove('running');
    if (wfItems.length <= 1) {
        fill.style.transform = 'scaleX(0)';
        return;
    }
    fill.style.transform = '';
    fill.style.animationDuration = String(durationMs) + 'ms';
    void fill.offsetWidth;
    fill.classList.add('running');
}

function renderWorkFlashSlide(triggerFlash) {
    var box = document.getElementById('work-flash-box');
    if (!box || !wfItems.length) return;
    if (wfIndex >= wfItems.length) wfIndex = 0;
    if (wfIndex < 0) wfIndex = wfItems.length - 1;

    var item = wfItems[wfIndex];
    box.className = 'work-flash-box ' + (item.theme || 'wf-brand') + ((wfHovered || wfMenuOpen) ? ' is-paused' : '');

    var kickerEl = document.getElementById('wf-kicker');
    if (kickerEl) kickerEl.textContent = item.kicker || "TODAY'S WORK";

    var counterEl = document.getElementById('wf-counter');
    if (counterEl) counterEl.textContent = (wfIndex + 1) + ' / ' + wfItems.length;

    var navGroup = document.getElementById('wf-nav-group');
    if (navGroup) navGroup.style.display = wfItems.length > 1 ? 'inline-flex' : 'none';

    var bodyEl = document.getElementById('wf-body');
    if (bodyEl) {
        bodyEl.innerHTML =
            '<div class="wf-item-row">' +
                '<div class="wf-item-icon"><i class="bi ' + escHtml(item.icon || 'bi-lightning-charge') + '"></i></div>' +
                '<div class="wf-item-text">' +
                    '<div class="wf-item-title">' + escHtml(item.title || '') + '</div>' +
                    '<div class="wf-item-sub">' + escHtml(item.sub || '') + '</div>' +
                '</div>' +
            '</div>';
    }

    var actionBtn = document.getElementById('wf-action-btn');
    if (actionBtn) {
        actionBtn.textContent = item.actionText || 'Open →';
        actionBtn.onclick = function () {
            if (typeof item.onAction === 'function') item.onAction();
        };
    }

    if (triggerFlash) {
        box.classList.remove('wf-flash-pulse');
        void box.offsetWidth;
        box.classList.add('wf-flash-pulse');
    }

    wfRemainingMs = WORK_FLASH_DURATION;
    restartWorkFlashProgress(WORK_FLASH_DURATION);
}

function advanceWorkFlash() {
    if (!wfItems.length || wfHovered || wfMenuOpen || isWorkFlashSnoozed()) return;
    wfIndex = (wfIndex + 1) % wfItems.length;
    renderWorkFlashSlide(true);
    wfStartedAt = Date.now();
    wfTimer = setTimeout(advanceWorkFlash, wfRemainingMs);
}

function pauseWorkFlash(keepLabel) {
    clearTimeout(wfTimer);
    var box = document.getElementById('work-flash-box');
    if (box) box.classList.add('is-paused');
    if (wfStartedAt > 0) {
        wfRemainingMs = Math.max(300, wfRemainingMs - (Date.now() - wfStartedAt));
        wfStartedAt = 0;
    }
    if (!keepLabel) {
        var note = document.getElementById('wf-foot-note');
        if (note) note.textContent = 'Paused on hover';
    }
}

function resumeWorkFlash() {
    if (wfHovered || wfMenuOpen || isWorkFlashSnoozed()) return;
    var box = document.getElementById('work-flash-box');
    if (box) box.classList.remove('is-paused');
    var note = document.getElementById('wf-foot-note');
    if (note) note.textContent = wfItems.length > 1 ? 'Updates live • Hover to pause' : 'Updates live';
    clearTimeout(wfTimer);
    if (wfItems.length > 1) {
        wfStartedAt = Date.now();
        wfTimer = setTimeout(advanceWorkFlash, wfRemainingMs);
    }
}

function stepWorkFlash(delta) {
    if (wfItems.length <= 1) return;
    clearTimeout(wfTimer);
    wfIndex = (wfIndex + delta + wfItems.length) % wfItems.length;
    renderWorkFlashSlide(true);
    if (!wfHovered && !wfMenuOpen && !isWorkFlashSnoozed()) {
        wfStartedAt = Date.now();
        wfTimer = setTimeout(advanceWorkFlash, WORK_FLASH_DURATION);
    }
}

function refreshWorkFlash(forceShow) {
    var box = document.getElementById('work-flash-box');
    var restore = document.getElementById('work-flash-restore');
    var restoreCnt = document.getElementById('wf-restore-count');
    if (!box) return;

    var prevKey = (wfItems[wfIndex] && wfItems[wfIndex].key) ? wfItems[wfIndex].key : null;
    wfItems = buildWorkFlashItems();

    if (!wfItems.length) {
        clearTimeout(wfTimer);
        box.style.display = 'none';
        if (restore) restore.style.display = 'none';
        return;
    }

    if (prevKey) {
        var matchedIdx = wfItems.findIndex(function (it) { return it.key === prevKey; });
        wfIndex = matchedIdx !== -1 ? matchedIdx : 0;
    } else {
        wfIndex = 0;
    }

    if (isWorkFlashSnoozed()) {
        clearTimeout(wfTimer);
        box.style.display = 'none';
        if (restore) {
            if (restoreCnt) restoreCnt.textContent = String(wfItems.length);
            restore.style.display = 'inline-flex';
        }
        return;
    }

    if (restore) restore.style.display = 'none';
    var wasHidden = box.style.display === 'none';
    box.style.display = 'block';
    renderWorkFlashSlide(wasHidden || !!forceShow);

    var note = document.getElementById('wf-foot-note');
    if (note && !wfHovered) {
        note.textContent = wfItems.length > 1 ? 'Updates live • Hover to pause' : 'Updates live';
    }

    clearTimeout(wfTimer);
    if (!wfHovered && !wfMenuOpen && wfItems.length > 1) {
        wfStartedAt = Date.now();
        wfTimer = setTimeout(advanceWorkFlash, WORK_FLASH_DURATION);
    } else if (wfHovered || wfMenuOpen) {
        pauseWorkFlash(false);
    }
}

function scheduleWorkFlashRefresh() {
    clearTimeout(wfDebounceTimer);
    wfDebounceTimer = setTimeout(function () {
        refreshWorkFlash(false);
    }, 180);
}

$(function () {
    tick();setInterval(tick,1000);
    drawCallingChart();
    drawAttendanceDonut(0, 0);
    loadCalendarData(calY,calM);
    loadDashboardMetrics();
    if (window.canQuickWorklog && typeof loadQuickTimesheetWidget === 'function') {
        loadQuickTimesheetWidget(false, 0);
    }

    $(document).on('mouseenter', '#work-flash-box', function () {
        wfHovered = true;
        pauseWorkFlash(false);
    });
    $(document).on('mouseleave', '#work-flash-box', function () {
        wfHovered = false;
        resumeWorkFlash();
    });
    $(document).on('click', function (e) {
        if (wfMenuOpen && !$(e.target).closest('.wf-snooze-wrap').length) {
            closeWorkFlashSnoozeMenu();
            resumeWorkFlash();
        }
    });

    $(document).on('mouseenter', '#att-present-block', function () {
        showAttPopover('present', this);
    });
    $(document).on('mouseenter', '#att-absent-block', function () {
        showAttPopover('absent', this);
    });
    $(document).on('mouseleave', '#att-present-block, #att-absent-block', function () {
        hideAttPopover(false);
    });
    $(document).on('mouseenter', '#att-hover-popover', function () {
        clearTimeout(attPopTimer);
    });
    $(document).on('mouseleave', '#att-hover-popover', function () {
        hideAttPopover(false);
    });
    $(document).on('mouseenter', '.ap-summary-row[data-ap-type]', function () {
        showApPopover($(this).data('ap-type'), this);
    });
    $(document).on('mouseleave', '.ap-summary-row[data-ap-type]', function () {
        hideApPopover(false);
    });
    $(document).on('mouseenter', '#ap-hover-popover', function () {
        clearTimeout(apPopTimer);
    });
    $(document).on('mouseleave', '#ap-hover-popover', function () {
        hideApPopover(false);
    });
    $(document).on('mouseenter', '#cal-grid .cal-day[data-cal-date]', function () {
        showCalPopover($(this).attr('data-cal-date'), this);
    });
    $(document).on('mouseleave', '#cal-grid .cal-day[data-cal-date]', function () {
        hideCalPopover(false);
    });
    $(document).on('mouseenter', '#cal-hover-popover', function () {
        clearTimeout(calPopTimer);
    });
    $(document).on('mouseleave', '#cal-hover-popover', function () {
        hideCalPopover(false);
    });
    $(document).on('mouseenter', '.dash-event-card[data-ev-kind]', function () {
        showEventPopover($(this).attr('data-ev-kind'), $(this).attr('data-ev-idx'), this);
    });
    $(document).on('mouseleave', '.dash-event-card[data-ev-kind]', function () {
        hideEventPopover(false);
    });
    $(document).on('mouseenter', '#event-hover-popover', function () {
        clearTimeout(evPopTimer);
    });
    $(document).on('mouseleave', '#event-hover-popover', function () {
        hideEventPopover(false);
    });
    $(document).on('mouseenter', '.leave-bal-chip[data-leave-idx]', function () {
        var idx = $(this).attr('data-leave-idx');
        showLeavePopover(idx, this);
    });
    $(document).on('mouseleave', '.leave-bal-chip[data-leave-idx]', function () {
        hideLeavePopover(false);
    });
    $(document).on('mouseenter', '#leave-hover-popover', function () {
        clearTimeout(leavePopTimer);
    });
    $(document).on('mouseleave', '#leave-hover-popover', function () {
        hideLeavePopover(false);
    });
    $(window).on('scroll resize', function () {
        hideAttPopover(true);
        hideApPopover(true);
        hideCalPopover(true);
        hideEventPopover(true);
        hideLeavePopover(true);
        hideChartTooltip();
    });
    $('.main').on('scroll', function () {
        hideAttPopover(true);
        hideApPopover(true);
        hideCalPopover(true);
        hideEventPopover(true);
        hideLeavePopover(true);
        hideChartTooltip();
    });

    $(document).on('input', '#tkq-remark', function () {
        var len = (this.value || '').length;
        $('#tkq-char-count').text(len + ' / 1000');
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            var qm = document.getElementById('modal-task-quick');
            if (qm && qm.style.display === 'flex') closeTaskQuickModal();
            if (window.qtsDrawerOpen && typeof closeQuickTimesheetDrawer === 'function') closeQuickTimesheetDrawer();
        }
    });

    $(document).on('click', '.pibtn', function() {
        const btn = $(this);
        btn.prop('disabled', true).text('Processing...');
        $.ajax({
            url: '/attendance/punch-in',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                alert(res.message || 'Punched in successfully');
                btn.prop('disabled', false).text('Punch In');
                loadDashboardMetrics();
            },
            error: function(err) {
                alert(err.responseJSON?.message || 'Failed to punch in');
                btn.prop('disabled', false).text('Punch In');
            }
        });
    });
});
</script>
@endpush
