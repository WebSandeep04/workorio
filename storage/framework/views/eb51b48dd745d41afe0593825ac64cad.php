<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page_title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<?php
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
?>

<div class="dashboard-wrapper">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <div>
            <div style="font-size:10.5px;color:#9ca3af;margin-bottom:4px">Viewing dashboard as:</div>
            <div class="rtabs">
                <button class="rtab act" onclick="sw('founder',this)">My Dashboard</button>
                <?php if($hasSubordinates): ?>
                <button class="rtab" onclick="sw('sales',this)">Teams Dashboard</button>
                <?php endif; ?>
            </div>
        </div>
        <div style="font-size:11.5px;color:#6b7280;text-align:right">
            <div style="font-weight:600;color:#111827"><?php echo e(date('l, d M Y')); ?></div>
            <div id="clk-display">Workorio Pro</div>
        </div>
    </div>

    <!-- ===== FOUNDER VIEW ===== -->
    <div class="d-view act" id="vf">
        <div class="wbar">
            <div><h2>Welcome back, <?php echo e($firstName); ?>! 👋</h2><p>Here is your business overview for today.</p></div>
        </div>
        <!-- <div class="alert-w">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="#f59e0b" style="flex-shrink:0"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span><b>Subscription Expiring:</b> Workorio Pro expires in <b>18 days</b> (May 13, 2025). &nbsp;<span style="color:#2563eb;cursor:pointer;text-decoration:underline">Renew Now →</span></span>
        </div> -->

        <?php if($isSales || $isCalling): ?>
        <div style="margin-bottom:20px">
            <?php if($isSales): ?>
            <div style="margin-bottom:10px">
                <div style="font-size:16px;font-weight:700;color:#111827">Sales Intelligence</div>
                <div style="font-size:11.5px;color:#6b7280">Real-time performance metrics for your sales pipeline</div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px">
                <div class="sc" onclick="location.href='<?php echo e(route('todayfollowupstable')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>Today's Followups</div><div class="sval" id="todayfollowups">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('underprocesstable')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>Under Process</div><div class="sval" id="underprocess">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('todaycompletedtable')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>Today Completed</div><div class="sval" id="todaycompleted">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('todaypendingtable')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>Today Pending</div><div class="sval" id="todaypending">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('todaynewtable')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>New Followups</div><div class="sval" id="todaynew">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('myleads')); ?>'" style="cursor:pointer"><div class="slbl"><svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>All Leads</div><div class="sval" id="allleads">0<span class="sarr">→</span></div></div>
            </div>
            <?php endif; ?>

            <?php if($isCalling): ?>
            <div style="margin-bottom:10px; margin-top: 20px;">
                <div style="font-size:16px;font-weight:700;color:#111827">Tele-Calling Status</div>
                <div style="font-size:11.5px;color:#6b7280">Daily calling activity and follow-up tracking</div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px">
                <div class="sc" onclick="location.href='<?php echo e(route('calling.todayfollowupstable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#3b82f6;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></div>Today's Followups</div><div class="sval" id="c_todayfollowups">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('calling.underprocesstable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#f59e0b;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>Under Process</div><div class="sval" id="c_underprocess">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('calling.todaycompletedtable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#10b981;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>Today Completed</div><div class="sval" id="c_todaycompleted">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('calling.todaypendingtable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#ef4444;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div>Today Pending</div><div class="sval" id="c_todaypending">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('calling.todaynewtable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#a855f7;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg></div>New Followups</div><div class="sval" id="c_todaynew">0<span class="sarr">→</span></div></div>
                <div class="sc" onclick="location.href='<?php echo e(route('calling.allleadstable')); ?>'" style="cursor:pointer"><div class="slbl"><div style="background:#1f2937;border-radius:6px;width:24px;height:24px;display:flex;align-items:center;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" style="width:14px;height:14px"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></div>All Leads</div><div class="sval" id="c_allleads">0<span class="sarr">→</span></div></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="hcards">
            <?php if($isAttendance): ?>
            <div class="card" style="border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px;">
                <div class="chead" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    <span class="ctitle" style="font-size: 15px; font-weight: 700; color: #1e293b;">Team Attendance Today</span>
                    <span class="va" onclick="location.href='<?php echo e(route('attendance.history')); ?>'" style="color: #2563eb; font-size: 12px; font-weight: 500; cursor: pointer; text-decoration: none;">view all</span>
                </div>
                <div class="att-grid" style="display: grid; grid-template-columns: 2.3fr 1fr; gap: 16px;">
                    <!-- Present Block -->
                    <div id="att-present-block" style="background: #f4fbf7; border: 1px solid #c6f6d5; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; position: relative;" title="No employees present">
                        
                        <!-- Top Row: Present Text, Large Number, and the Icon on the Right -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                            <div>
                                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Present Today</div>
                                <div style="font-size: 40px; font-weight: 800; color: #15803d; line-height: 1;" id="att-present">0</div>
                            </div>
                            
                            <!-- Icon Box -->
                            <div style="background: #e6f9ed; width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Bottom Row: Pills -->
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-top: 20px;">
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9999px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #334155;">
                                <span style="display: inline-block; width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                                In Office <b style="color: #15803d; font-weight: 700; margin-left: 2px;" id="att-office">0</b>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9999px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #334155;">
                                <span style="display: inline-block; width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                                Remote <b style="color: #15803d; font-weight: 700; margin-left: 2px;" id="att-remote">0</b>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9999px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #334155;">
                                <span style="display: inline-block; width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                                In Field <b style="color: #15803d; font-weight: 700; margin-left: 2px;" id="att-field">0</b>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 9999px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #334155;">
                                <span style="display: inline-block; width: 6px; height: 6px; background: #16a34a; border-radius: 50%;"></span>
                                WFH <b style="color: #15803d; font-weight: 700; margin-left: 2px;" id="att-wfh">0</b>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Absent Block -->
                    <div id="att-absent-block" style="background: #fff5f5; border: 1px solid #fee2e2; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center;" title="No employees absent">
                        <div style="font-size: 46px; font-weight: 800; color: #ef4444; line-height: 1;" id="att-absent">0</div>
                        <div style="font-size: 13.5px; color: #b91c1c; font-weight: 700; margin-top: 8px; text-transform: capitalize;">Absent</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($isSales): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Lead Source Breakdown</span><span style="color:#9ca3af;font-size:16px;cursor:pointer;letter-spacing:2px">···</span></div>
                <div class="dw">
                    <div class="dc"><canvas id="dc1" width="110" height="110"></canvas><div class="dctr"><div class="n" id="total-leads-donut">0</div><div class="l">Leads</div></div></div>
                    <ul class="leg" id="lead-source-leg">
                        <!-- Dynamic legend -->
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="hcards">
            <?php if($isTasks): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Task</span><span class="va">view all</span></div>
                <div class="subtabs">
                    <button class="subtab act" onclick="swTaskTab('byMe',this)">Assigned by You</button>
                    <button class="subtab" onclick="swTaskTab('toMe',this)">Assigned to You</button>
                </div>
                <div id="tk-byMe" class="tk-cnt act">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">Loading tasks...</div>
                </div>
                <div id="tk-toMe" class="tk-cnt">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">Loading tasks...</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($isSales): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Due Payments</span><span id="overdue-badge" style="display:none;font-size:11px;background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:8px;font-weight:600">0 Overdue</span></div>
                <div id="due-payments-list">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending payments</div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="hcards">
            <?php if($isSubs): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Due Subscriptions</span><span class="va" onclick="location.href='<?php echo e(route('subscriptions.index')); ?>'">view all</span></div>
                <div id="due-subscriptions-list">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No active subscriptions</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($isPettyCash): ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; gap: 12px; height: 100%;">
                <!-- Petty Cash Section -->
                <div>
                    <div class="chead">
                        <span class="ctitle">Petty Cash</span>
                        <select id="pc-period" onchange="updatePC()" style="border:1px solid #e5e7eb;border-radius:6px;padding:3px 8px;font-size:11.5px;color:#374151;background:#fff;cursor:pointer">
                            <option value="month" selected>This month</option>
                            <option value="year">This year</option>
                            <option value="fin">This year Financial</option>
                        </select>
                    </div>
                    <div class="pcgrid">
                        <div class="pcb" style="background:#eff6ff"><div class="pcb-val" id="pc-open" style="color:#1d4ed8">₹0</div><div class="pcb-lbl">Opening Balance</div></div>
                        <div class="pcb" style="background:#fee2e2"><div class="pcb-val" id="pc-used" style="color:#b91c1c">₹0</div><div class="pcb-lbl">Balance Used</div></div>
                        <div class="pcb" style="background:#f0fdf4"><div class="pcb-val" id="pc-rem" style="color:#15803d">₹0</div><div class="pcb-lbl">Remaining</div></div>
                    </div>
                </div>

                <!-- Divider -->
                <hr style="border:0; border-top:1px solid #e5e7eb; margin:4px 0;">

                <!-- Upcoming Leaves Section -->
                <div>
                    <div class="chead">
                        <span class="ctitle">Upcoming Leaves</span>
                        <span class="va" onclick="location.href='<?php echo e(route('leave.index')); ?>'">view all</span>
                    </div>
                    <div id="upcoming-leaves-list" style="display: flex; flex-direction: column; gap: 6px; margin-top: 4px;">
                        <div style="text-align:center;padding:10px;color:#9ca3af;font-size:12px">No upcoming leaves</div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="hcards">
            <?php if($isCalendar): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">SMM Calendar</span><span class="va">view all</span></div>
                <div class="cal-hdr">
                    <button class="cal-nav" onclick="prevM()">‹</button>
                    <span class="cal-month-lbl" id="cal-ml">April 2025</span>
                    <button class="cal-nav" onclick="nextM()">›</button>
                </div>
                <div class="cal-dnames">
                    <div class="cal-dn">SUN</div><div class="cal-dn">MON</div><div class="cal-dn">TUE</div><div class="cal-dn">WED</div><div class="cal-dn">THU</div><div class="cal-dn">FRI</div><div class="cal-dn">SAT</div>
                </div>
                <div class="cal-grid" id="cal-grid"></div>
            </div>
            <?php endif; ?>

            <?php if($isApproval && $hasSubordinates): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Pending Approvals</span><span class="va">view all</span></div>
                <div class="subtabs" style="overflow-x:auto;white-space:nowrap;display:block">
                    <?php if($isWorklog): ?>
                    <button class="subtab act" onclick="swApprovalTab('ts',this)" style="display:inline-block">Timesheet<span class="notif-badge" id="ap-ts-badge">0</span></button>
                    <?php endif; ?>
                    <?php if($isAttendance): ?>
                    <button class="subtab <?php echo e(!$isWorklog ? 'act' : ''); ?>" onclick="swApprovalTab('at',this)" style="display:inline-block">Attendance<span class="notif-badge" id="ap-at-badge">0</span></button>
                    <?php endif; ?>
                    <?php if($isTasks): ?>
                    <button class="subtab <?php echo e(!$isWorklog && !$isAttendance ? 'act' : ''); ?>" onclick="swApprovalTab('tk',this)" style="display:inline-block">Task<span class="notif-badge" id="ap-tk-badge">0</span></button>
                    <?php endif; ?>
                    <?php if($isAttendance): ?>
                    <button class="subtab" onclick="swApprovalTab('lv',this)" style="display:inline-block">Leave<span class="notif-badge" id="ap-lv-badge">0</span></button>
                    <?php endif; ?>
                    <?php if($isPettyCash): ?>
                    <button class="subtab <?php echo e(!$isWorklog && !$isAttendance && !$isTasks ? 'act' : ''); ?>" onclick="swApprovalTab('pc',this)" style="display:inline-block">Petty Cash<span class="notif-badge" id="ap-pc-badge">0</span></button>
                    <?php endif; ?>
                </div>
                
                <?php if($isWorklog): ?>
                <div id="ap-ts" class="ap-cnt act">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending timesheets</div>
                </div>
                <?php endif; ?>
                
                <?php if($isAttendance): ?>
                <div id="ap-at" class="ap-cnt <?php echo e(!$isWorklog ? 'act' : ''); ?>">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending attendance</div>
                </div>
                <?php endif; ?>
                
                <?php if($isTasks): ?>
                <div id="ap-tk" class="ap-cnt <?php echo e(!$isWorklog && !$isAttendance ? 'act' : ''); ?>">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending tasks</div>
                </div>
                <?php endif; ?>
                
                <?php if($isAttendance): ?>
                <div id="ap-lv" class="ap-cnt">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending leaves</div>
                </div>
                <?php endif; ?>
                
                <?php if($isPettyCash): ?>
                <div id="ap-pc" class="ap-cnt <?php echo e(!$isWorklog && !$isAttendance && !$isTasks ? 'act' : ''); ?>">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending petty cash</div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="hcards">
            <?php if($isAttendance): ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Upcoming Holidays</span><span class="va" onclick="location.href='<?php echo e(route('holidays.index')); ?>'">view all</span></div>
                <div id="holidays-list" style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">Loading holidays...</div>
                </div>
            </div>
            <?php endif; ?>
            <div class="card">
                <div class="chead"><span class="ctitle">Celebrations</span></div>
                <div id="celebrations-list" style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">Loading celebrations...</div>
                </div>
            </div>
        </div>
    </div>


</div>

<div class="modal-ov" id="modal-cal" onclick="closeModal(event)">
    <div class="modal-cnt" onclick="event.stopPropagation()">
        <div class="m-close" onclick="closeModal(event)">✕</div>
        <div id="cal-detail-modal"></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.dashboard-wrapper{font-family:'Segoe UI',system-ui,sans-serif;font-size:14px}
.rtabs{display:flex;gap:4px;background:#e5e7eb;padding:3px;border-radius:9px;width:fit-content}
.rtab{padding:6px 16px;border-radius:7px;font-size:12.5px;font-weight:500;cursor:pointer;color:#6b7280;border:none;background:transparent;transition:all .2s}
.rtab.act{background:#2563eb;color:#fff}
.wbar{background:#fff;border-radius:9px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;border:1px solid #e5e7eb}
.wbar h2{font-size:17px;font-weight:700;color:#111827;margin:0}
.wbar p{font-size:12px;color:#9ca3af;margin-top:2px;margin-bottom:0}
.pibtn{background:#16a34a;color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer}
.alert-w{border-radius:9px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:8px;font-size:12.5px;background:#fffbeb;border:1px solid #fcd34d;color:#92400e}
.sc{background:#fff;border-radius:9px;padding:12px 14px;border:1px solid #e5e7eb;transition: all 0.2s ease;}
.sc:hover{border-color: #2563eb; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05);}
.slbl{font-size:11px;color:#6b7280;display:flex;align-items:center;gap:5px;margin-bottom:6px}
.slbl svg{width:13px;height:13px}
.sval{font-size:20px;font-weight:700;color:#111827;display:flex;align-items:center;justify-content:space-between}
.sarr{color:#d1d5db;font-size:14px}
.hcards{display:grid;grid-template-columns:repeat(auto-fit, minmax(450px, 1fr));gap:14px;margin-bottom:14px}
.card{background:#fff;border-radius:9px;border:1px solid #e5e7eb;padding:14px}
.chead{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.ctitle{font-size:13.5px;font-weight:600;color:#111827}
.va{color:#2563eb;font-size:11.5px;cursor:pointer;text-decoration:none}
.ti{display:flex;align-items:center;gap:9px;padding:7px 0;border-bottom:1px solid #f3f4f6}
.ti:last-child{border-bottom:none}
.tdot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.tdot.r{background:#ef4444}.tdot.g{background:#22c55e}.tdot.o{background:#f97316}
.tnm{font-size:12.5px;color:#374151;font-weight:500}
.tdue{font-size:11px;color:#9ca3af;margin-top:1px}
.tm{color:#d1d5db;font-size:15px;cursor:pointer;letter-spacing:2px}
.dw{display:flex;align-items:center;gap:16px}
.dc{position:relative;width:110px;height:110px;flex-shrink:0}
.dctr{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center}
.dctr .n{font-size:17px;font-weight:700;color:#111827}
.dctr .l{font-size:10px;color:#9ca3af}
.leg{list-style:none;display:flex;flex-direction:column;gap:5px;padding-left:0;margin-bottom:0}
.li{display:flex;align-items:center;gap:5px;font-size:11.5px;color:#374151}
.ld{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.lp{margin-left:auto;color:#6b7280;font-size:10.5px;min-width:28px;text-align:right}
.pdi{padding:8px 0;border-bottom:1px solid #f3f4f6}
.pdi:last-child{border-bottom:none}
.pdi-top{display:flex;align-items:flex-start;justify-content:space-between;gap:6px}
.pdi-nm{font-size:12.5px;font-weight:600;color:#374151}
.pdi-due{font-size:10.5px;color:#9ca3af;margin-top:1px}
.pdi-amt{font-size:13px;font-weight:700;color:#ef4444;white-space:nowrap}
.pdi-fu{display:flex;align-items:center;gap:5px;margin-top:5px;flex-wrap:wrap}
.pdi-tag{padding:2px 7px;border-radius:4px;font-size:10.5px;font-weight:500}
.pdi-tag.nc{background:#fee2e2;color:#b91c1c}
.pdi-tag.prom{background:#dcfce7;color:#15803d}
.pdi-tag.pend{background:#fffbeb;color:#92400e}
.pcgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;text-align:center}
.pcb{border-radius:8px;padding:12px 8px}
.pcb-val{font-size:17px;font-weight:700;margin-bottom:3px}
.pcb-lbl{font-size:10.5px;color:#6b7280}
.cal-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.cal-nav{background:none;border:1px solid #e5e7eb;border-radius:6px;width:28px;height:28px;cursor:pointer;font-size:15px;color:#374151;display:flex;align-items:center;justify-content:center;line-height:1}
.cal-nav:hover{background:#f3f4f6}
.cal-month-lbl{font-size:14px;font-weight:600;color:#111827}
.cal-dnames{display:grid;grid-template-columns:repeat(7,1fr);margin-bottom:3px}
.cal-dn{text-align:center;font-size:10px;font-weight:600;color:#9ca3af;padding:3px 0}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px}
.cal-day{min-height:36px;border-radius:7px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;cursor:pointer;font-size:12px;color:#374151;padding:4px 2px;user-select:none}
.cal-day:hover{background:#f0f4ff}
.cal-day.today{background:#2563eb;color:#fff;font-weight:700}
.cal-day.today:hover{background:#1d4ed8}
.cal-day.sel-day{outline:2px solid #2563eb;border-radius:7px}
.cal-day.today.sel-day{outline:2px solid #93c5fd;outline-offset:1px}
.cal-day.empty{cursor:default}
.cal-day.empty:hover{background:transparent}
.cal-dots{display:flex;gap:2px;margin-top:2px;justify-content:center}
.cal-dot{width:4px;height:4px;border-radius:50%}
.cal-pills{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:10px}
.cpill{font-size:11px;font-weight:600;padding:3px 10px;border-radius:12px}
.cpill.done{background:#dcfce7;color:#15803d}
.cpill.miss{background:#fee2e2;color:#b91c1c}
.cpill.rem{background:#fffbeb;color:#92400e}
.cpill.sch{background:#dbeafe;color:#1d4ed8}
.cpill.canc{background:#f3f4f6;color:#6b7280}
.cpill.resch{background:#fdf4ff;color:#7e22ce}
.cal-sec{margin-bottom:9px}
.cal-sec:last-child{margin-bottom:0}
.cal-sec-title{font-size:10.5px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px}
.cal-entry{display:flex;align-items:center;gap:7px;padding:5px 8px;background:#fff;border-radius:6px;margin-bottom:3px;border:1px solid #e5e7eb}
.cal-entry:last-child{margin-bottom:0}
.cplat{font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;min-width:50px;text-align:center}
.cplat.ig{background:#fce7f3;color:#be185d}
.cplat.fb{background:#dbeafe;color:#1e3a8a}
.cplat.li{background:#e0f2fe;color:#0369a1}
.cclient{font-size:12px;color:#374151;font-weight:500}
.cnote{font-size:10.5px;color:#9ca3af;margin-left:auto}
.cal-empty-msg{text-align:center;font-size:12px;color:#9ca3af;padding:10px}
.fi{display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid #f3f4f6}
.fi:last-child{border-bottom:none}
.fav{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0}
.fnm{font-size:12.5px;font-weight:500;color:#374151}
.fdt{font-size:11px;color:#9ca3af;margin-top:1px}
.ft{font-size:11px;color:#6b7280;background:#f3f4f6;padding:2px 7px;border-radius:9px;white-space:nowrap}
.ft.urg{color:#ef4444;background:#fee2e2}
.pstages{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;text-align:center}
.ps{border-radius:7px;padding:10px 6px}
.psn{font-size:20px;font-weight:700}
.psl{font-size:10.5px;color:#6b7280;margin-top:3px}

.d-view { display: none !important; }
.d-view.act { display: block !important; }
.subtabs { display: flex; gap: 4px; background: #f3f4f6; padding: 3px; border-radius: 8px; margin-bottom: 12px; width: fit-content; }
.subtab { padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer; color: #6b7280; border: none; background: transparent; transition: all .2s; }
.subtab.act { background: #fff; color: #2563eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.notif-badge { display: inline-flex; align-items: center; justify-content: center; width: 16px; height: 16px; border-radius: 50%; background: #ef4444; color: #fff; font-size: 9px; font-weight: 700; margin-left: 6px; vertical-align: text-bottom; }
.tk-cnt, .ap-cnt { display: none; }
.tk-cnt.act, .ap-cnt.act { display: block; }
.modal-ov { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 2000; backdrop-filter: blur(2px); }
.modal-cnt { background: #fff; width: 380px; border-radius: 12px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; animation: mIn 0.2s ease-out; }
@keyframes mIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.m-close { position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #9ca3af; border-radius: 50%; }
.m-close:hover { background: #f3f4f6; color: #374151; }

@media (max-width: 991.98px) {
    .hcards { grid-template-columns: 1fr; }
    .sgrid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 767.98px) {
    .sgrid { grid-template-columns: repeat(2, 1fr); }
    .pstages { grid-template-columns: repeat(3, 1fr); }
    .wbar { flex-direction: column; align-items: flex-start; gap: 12px; }
    .pibtn { width: 100%; }
    .att-grid { grid-template-columns: 1fr !important; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
var charts={};
var calY=<?php echo e(date('Y')); ?>,calM=<?php echo e(date('n') - 1); ?>;
var selDs=null;

window.tenantFeatures = {
    isSales: <?php echo e($isSales ? 'true' : 'false'); ?>,
    isCalling: <?php echo e($isCalling ? 'true' : 'false'); ?>,
    isAttendance: <?php echo e($isAttendance ? 'true' : 'false'); ?>,
    isTasks: <?php echo e($isTasks ? 'true' : 'false'); ?>,
    isSubs: <?php echo e($isSubs ? 'true' : 'false'); ?>,
    isPettyCash: <?php echo e($isPettyCash ? 'true' : 'false'); ?>,
    isCalendar: <?php echo e($isCalendar ? 'true' : 'false'); ?>,
    isApproval: <?php echo e(($isApproval && $hasSubordinates) ? 'true' : 'false'); ?>,
    isWorklog: <?php echo e($isWorklog ? 'true' : 'false'); ?>

};

var smmData={};

var MONTHS=['January','February','March','April','May','June','July','August','September','October','November','December'];

function renderCal(y,m){
  document.getElementById('cal-ml').textContent=MONTHS[m]+' '+y;
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
        if((data.done||[]).length) dc.push('#22c55e');
        if((data.rem||[]).length) dc.push('#f59e0b');
        if((data.canc||[]).length) dc.push('#9ca3af');
      } else if(isPast){
        if((data.done||[]).length) dc.push('#22c55e');
        if((data.missed||[]).length) dc.push('#ef4444');
        if((data.canc||[]).length) dc.push('#9ca3af');
      } else {
        if((data.sch||[]).length) dc.push('#2563eb');
        if((data.resch||[]).length) dc.push('#8b5cf6');
        if((data.canc||[]).length) dc.push('#9ca3af');
      }
      var dotInner=isToday?dc.map(()=>'<div class="cal-dot" style="background:rgba(255,255,255,0.85)"></div>').join(''):dc.map(c=>'<div class="cal-dot" style="background:'+c+'"></div>').join('');
      dots='<div class="cal-dots">'+dotInner+'</div>';
    }
    var cls='cal-day'+(isToday?' today':'')+(ds===selDs?' sel-day':'');
    html+='<div class="'+cls+'" id="cd-'+ds+'" onclick="pickDate(\''+ds+'\')">'+(isToday?'<b>'+d+'</b>':d)+dots+'</div>';
  }
  document.getElementById('cal-grid').innerHTML=html;
}

function prevM(){calM--;if(calM<0){calM=11;calY--;}loadCalendarData(calY,calM);selDs=null;}
function nextM(){calM++;if(calM>11){calM=0;calY++;}loadCalendarData(calY,calM);selDs=null;}

function platHtml(p){
  var n=p==='ig'?'Instagram':p==='fb'?'Facebook':'LinkedIn';
  return '<span class="cplat '+p+'">'+n+'</span>';
}
function entryRow(e,extra){
  var note=e.r?'<span class="cnote">'+e.r+'</span>':'';
  var td=extra?'style="text-decoration:line-through;opacity:0.65"':'';
  return '<div class="cal-entry">'+platHtml(e.p)+'<span class="cclient" '+td+'>'+e.c+'</span>'+note+'</div>';
}

function pickDate(ds){
  if(selDs){var prev=document.getElementById('cd-'+selDs);if(prev){prev.classList.remove('sel-day');}}
  selDs=ds;
  var el=document.getElementById('cd-'+ds);
  if(el) el.classList.add('sel-day');
  var data=smmData[ds];
  var d=new Date(ds+'T00:00:00');
  var dLabel=d.toLocaleDateString('en-IN',{weekday:'short',day:'numeric',month:'long',year:'numeric'});
  var det=document.getElementById('cal-detail-modal');
  var html='';
  if(!data){
    html='<div class="cal-detail-date" style="font-weight:700; margin-bottom:10px;">📅 '+dLabel+'</div><div class="cal-empty-msg">No posts scheduled on this date.</div>';
  } else {
    html='<div class="cal-detail-date" style="font-weight:700; margin-bottom:10px;">📅 '+dLabel+'</div>';
    if(data.t==='today'){
      var pills='';
      if((data.done||[]).length) pills+='<span class="cpill done">✓ '+(data.done.length)+' Posted</span>';
      if((data.rem||[]).length) pills+='<span class="cpill rem">⏳ '+(data.rem.length)+' Remaining</span>';
      if((data.canc||[]).length) pills+='<span class="cpill canc">✕ '+(data.canc.length)+' Cancelled</span>';
      html+='<div class="cal-pills">'+pills+'</div>';
      if((data.done||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#15803d">Posted</div>';data.done.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.rem||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#92400e">Remaining</div>';data.rem.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.canc||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title">Cancelled</div>';data.canc.forEach(e=>{html+=entryRow(e,true);});html+='</div>';}
    } else if(data.t==='past'){
      var pills='';
      if((data.done||[]).length) pills+='<span class="cpill done">✓ '+(data.done.length)+' Posted</span>';
      if((data.missed||[]).length) pills+='<span class="cpill miss">✗ '+(data.missed.length)+' Missed</span>';
      if((data.canc||[]).length) pills+='<span class="cpill canc">↩ '+(data.canc.length)+' Cancelled</span>';
      html+='<div class="cal-pills">'+pills+'</div>';
      if((data.done||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#15803d">Posted</div>';data.done.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.missed||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#b91c1c">Missed</div>';data.missed.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.canc||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title">Cancelled / Rescheduled</div>';data.canc.forEach(e=>{html+=entryRow(e,true);});html+='</div>';}
    } else {
      var pills='';
      if((data.sch||[]).length) pills+='<span class="cpill sch">📅 '+(data.sch.length)+' Scheduled</span>';
      if((data.resch||[]).length) pills+='<span class="cpill resch">↩ '+(data.resch.length)+' Rescheduled</span>';
      if((data.canc||[]).length) pills+='<span class="cpill canc">✕ '+(data.canc.length)+' Cancelled</span>';
      html+='<div class="cal-pills">'+pills+'</div>';
      if((data.sch||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#1d4ed8">Scheduled</div>';data.sch.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.resch||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title" style="color:#7e22ce">Rescheduled</div>';data.resch.forEach(e=>{html+=entryRow(e);});html+='</div>';}
      if((data.canc||[]).length){html+='<div class="cal-sec"><div class="cal-sec-title">Cancelled</div>';data.canc.forEach(e=>{html+=entryRow(e,true);});html+='</div>';}
    }
  }
  det.innerHTML=html;
  document.getElementById('modal-cal').style.display='flex';
}

function closeModal(){document.getElementById('modal-cal').style.display='none';}

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

function updatePC(){
    var period = document.getElementById('pc-period').value;
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

function drawDonut(id,data,colors){var ctx=document.getElementById(id);if(!ctx)return; if(charts[id]) charts[id].destroy();charts[id]=new Chart(ctx,{type:'doughnut',data:{datasets:[{data:data,backgroundColor:colors,borderWidth:2,borderColor:'#fff'}]},options:{cutout:'72%',plugins:{legend:{display:false},tooltip:{enabled:false}},animation:{duration:700}}});}

window.isTeamView = false;

function sw(role,btn){
  document.querySelectorAll('.rtab').forEach(t=>t.classList.remove('act'));btn.classList.add('act');
  window.isTeamView = (role === 'sales');
  
  const titleEl = document.querySelector('.wbar h2');
  if (titleEl) {
      titleEl.innerHTML = window.isTeamView ? 'Welcome back, Team Viewer! 👋' : 'Welcome back, <?php echo e($firstName); ?>! 👋';
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
                return;
            }
            const labels = response.map(r => r.label);
            const values = response.map(r => r.value);
            const total = values.reduce((a, b) => a + b, 0);
            if (el) el.textContent = total;
            
            const colors = ['#2563eb','#f97316','#ec4899','#f59e0b','#8b5cf6','#10b981','#6366f1','#ef4444'];
            drawDonut(canvasId, values, colors.slice(0, values.length));
            
            // Update legend
            let legHtml = '';
            response.forEach((r, i) => {
                const percent = total > 0 ? Math.round((r.value / total) * 100) : 0;
                legHtml += `<li class="li"><span class="ld" style="background:${colors[i % colors.length]}"></span>${r.label}<span class="lp">${percent}%</span></li>`;
            });
            const legEl = document.getElementById(canvasId === 'dc1' ? 'lead-source-leg' : 'lead-source-leg-sales');
            if(legEl) legEl.innerHTML = legHtml;
        }
    });
}

function tick(){var n=new Date(),t=n.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true}),ce=document.getElementById('clk-display');if(ce)ce.textContent='Workorio Pro • '+t;}

function fetchMetric(url, elementId, key) {
    var qp = window.isTeamView ? (url.indexOf('?') !== -1 ? '&team=1' : '?team=1') : '';
    $.ajax({
        url: url + qp,
        method: 'GET',
        success: function (response) {
            const value = Number(response && response[key]) || 0;
            const el = document.getElementById(elementId);
            if (el) el.textContent = value;
        },
        error: function () {
            const el = document.getElementById(elementId);
            if (el) el.textContent = '0';
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
        fetchMetric('/calling/todayfollowups', 'c_todayfollowups', 'totalLeads');
        fetchMetric('/calling/underprocess', 'c_underprocess', 'underprocess');
        fetchMetric('/calling/todaycompleted', 'c_todaycompleted', 'todaycompleted');
        fetchMetric('/calling/todaypending', 'c_todaypending', 'todaypending');
        fetchMetric('/calling/todaynew', 'c_todaynew', 'todaynew');
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

                if (res.present_names && res.present_names.length > 0) {
                    $('#att-present-block').attr('title', 'Present:\n' + res.present_names.join('\n'));
                } else {
                    $('#att-present-block').attr('title', 'No employees present');
                }

                if (res.absent_names && res.absent_names.length > 0) {
                    $('#att-absent-block').attr('title', 'Absent:\n' + res.absent_names.join('\n'));
                } else {
                    $('#att-absent-block').attr('title', 'No employees absent');
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
        $.ajax({
            url: '/upcoming-leaves/summary' + qp,
            method: 'GET',
            success: function(res) {
                renderUpcomingLeaves(res.list || []);
            }
        });
    }

    // Pending Approvals
    if (f.isApproval) {
        $.ajax({
            url: '/pending-approvals/summary' + qp,
            method: 'GET',
            success: function(res) {
                if (f.isAttendance) $('#ap-at-badge').text(res.attendance);
                if (f.isAttendance) $('#ap-lv-badge').text(res.leave);
                if (f.isPettyCash) $('#ap-pc-badge').text(res.petty_cash);
                if (f.isWorklog) $('#ap-ts-badge').text(res.timesheet || 0);
                if (f.isTasks) $('#ap-tk-badge').text(res.task || 0);
                
                if (res.lists) {
                    if (f.isAttendance) renderApprovals('at', res.lists.attendance);
                    if (f.isAttendance) renderApprovals('lv', res.lists.leave);
                    if (f.isPettyCash) renderApprovals('pc', res.lists.petty_cash);
                    if (f.isWorklog) renderApprovals('ts', res.lists.timesheet);
                    if (f.isTasks) renderApprovals('tk', res.lists.task);
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
                renderPayments(res.list || [], res.total_due || 0, res.count || 0);
            }
        });
    }

    // Due Subscriptions
    if (f.isSubs) {
        $.ajax({
            url: '/due-subscriptions/summary' + qp,
            method: 'GET',
            success: function(res) {
                renderSubscriptions(res.list || []);
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
        loadLeadSourceDonut('dc2', 'total-leads-donut-sales');
    }

    if (f.isCalling) {
        // Add call metrics if needed for sales view
        // fetchMetric('/calling/summary', 's_calls', 'completed'); // Example
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
                renderTasks('tk-byMe', res.data || []);
            }
        });
        $.ajax({
            url: '/user-tasks?type=toMe' + qpAmp,
            method: 'GET',
            success: function(res) {
                renderTasks('tk-toMe', res.data || []);
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
            res.forEach(item => {
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
            renderCal(y, m);
        }
    });
}

function renderUpcomingLeaves(list) {
    const cont = document.getElementById('upcoming-leaves-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div style="text-align:center;padding:10px;color:#9ca3af;font-size:12px">No upcoming leaves</div>';
        return;
    }
    let html = '';
    list.forEach(item => {
        const start = new Date(item.start_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const end = new Date(item.end_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        html += `<div style="display:flex; justify-content:space-between; align-items:center; padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px;">
            <div>
                <div style="font-weight: 600; color: #111827;">${item.user_name}</div>
                <div style="color: #6b7280; font-size: 12px;">${item.leave_type_name} • ${start} - ${end}</div>
            </div>
            <div style="font-weight: 500; color: #374151;">${item.total_days} d</div>
        </div>`;
    });
    cont.innerHTML = html;
}

function renderPayments(list, totalDue = 0, totalCount = 0) {
    const cont = document.getElementById('due-payments-list');
    if (!cont) return;
    if (!list.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending payments</div>';
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
        const badgeColor = p.source_type === 'subscription' ? 'background:#dbeafe;color:#1e3a8a;' : 'background:#f3e8ff;color:#6b21a8;';
        const badgeText = p.source_type === 'subscription' ? 'Subscription' : 'Invoice';
        const badgeHtml = `<span style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:2px 6px; border-radius:4px; margin-left:6px; vertical-align:middle; ${badgeColor}">${badgeText}</span>`;
        
        html += `<div class="pdi">
            <div class="pdi-top"><div><div class="pdi-nm" style="display:flex;align-items:center;">${p.customer_name || 'Customer'} ${badgeHtml}</div><div class="pdi-due">Due ${due}</div></div><div class="pdi-amt">₹${Number(p.remaining).toLocaleString()}</div></div>
        </div>`;
    });
    cont.innerHTML = html;
}

function renderSubscriptions(list) {
    const cont = document.getElementById('due-subscriptions-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No active subscriptions</div>';
        return;
    }
    let html = '';
    list.forEach(s => {
        const due = new Date(s.due_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        html += `<div class="pdi">
            <div class="pdi-top">
                <div>
                    <div class="pdi-nm">${s.customer_name || 'Customer'}</div>
                    <div class="pdi-due">${s.product_name || s.subscription_name || 'Product'} • Due ${due}</div>
                </div>
                <div class="pdi-amt">₹${Number(s.amount).toLocaleString()}</div>
            </div>
        </div>`;
    });
    cont.innerHTML = html;
}

function renderCelebrations(list) {
    const cont = document.getElementById('celebrations-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No upcoming celebrations</div>';
        return;
    }
    let html = '';
    list.slice(0, 1).forEach(c => {
        const d = new Date(c.date);
        const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const initial = c.name.split(' ').map(n => n[0]).join('').toUpperCase();
        const icon = c.type === 'birthday' ? '🎂' : '💼';
        const color = c.type === 'birthday' ? '#fca5a5' : '#60a5fa';
        const labelColor = c.type === 'birthday' ? '#db2777' : '#2563eb';
        
        html += `<div style="background: #f9fafb; border: 1px solid #f3f4f6; padding: 14px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: ${color}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">${initial}</div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #111827;">${c.name}</div>
                            <div style="font-size: 12px; color: ${labelColor}; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-top: 2px;">${icon} ${c.label}</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 13px; font-weight: 700; color: #1f2937;">Upcoming</div>
                        <div style="font-size: 11px; color: #6b7280;">${dateStr}</div>
                    </div>
                </div>`;
    });
    cont.innerHTML = html;
}

function renderHolidays(list) {
    const cont = document.getElementById('holidays-list');
    if (!cont) return;
    if (!list || !list.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No upcoming holidays</div>';
        return;
    }
    let html = '';
    list.slice(0, 1).forEach(h => {
        const d = new Date(h.holiday_date);
        const dateStr = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        const initial = h.name.split(' ').filter(n => n).map(n => n[0]).join('').substring(0,2).toUpperCase();
        const color = h.is_rh ? '#93c5fd' : '#6ee7b7';
        const labelColor = h.is_rh ? '#2563eb' : '#059669';
        const icon = h.is_rh ? '🔵' : '🌴';
        const typeLabel = h.is_rh ? 'Restricted' : 'Public Holiday';
        const weekday = d.toLocaleDateString('en-IN', { weekday: 'long' });

        html += `<div style="background: #f9fafb; border: 1px solid #f3f4f6; padding: 14px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: ${color}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">${initial}</div>
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #111827;">${h.name}</div>
                            <div style="font-size: 12px; color: ${labelColor}; font-weight: 600; display: flex; align-items: center; gap: 4px; margin-top: 2px;">${icon} ${typeLabel}</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 13px; font-weight: 700; color: #1f2937;">${weekday}</div>
                        <div style="font-size: 11px; color: #6b7280;">${dateStr}</div>
                    </div>
                </div>`;
    });
    cont.innerHTML = html;
}

function renderFollowups(leads) {
    const cont = document.getElementById('followups-list');
    if (!cont) return;
    if (!leads || !leads.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No follow-ups for today</div>';
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

function renderApprovals(type, list) {
    const cont = document.getElementById('ap-' + type);
    if (!cont) return;
    if (!list || !list.length) {
        const labels = { ts: 'timesheets', at: 'attendance', tk: 'tasks', lv: 'leaves', pc: 'petty cash' };
        cont.innerHTML = `<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No pending ${labels[type] || 'items'}</div>`;
        return;
    }
    let html = '';
    list.forEach(item => {
        let title = '';
        let sub = '';
        if (type === 'at') {
            title = `${item.user_name} - ${new Date(item.date).toLocaleDateString()}`;
            sub = 'Attendance regularization';
        } else if (type === 'lv') {
            title = `${item.user_name} - ${item.leave_type_name}`;
            const start = new Date(item.start_date).toLocaleDateString();
            const end = new Date(item.end_date).toLocaleDateString();
            sub = `${start} - ${end} • ${item.total_days} days`;
        } else if (type === 'pc') {
            title = `${item.expense_name || 'Expense'} - ${item.department_name || 'Dept'}`;
            sub = `₹${item.price.toLocaleString()} • ${item.remark || 'No remark'}`;
        } else if (type === 'ts') {
            title = `${item.user_name} - ${new Date(item.work_date).toLocaleDateString()}`;
            sub = `Logged: ${item.hours}h ${item.minutes}m`;
        } else if (type === 'tk') {
            title = `${item.task_name}`;
            const due = item.due_date ? new Date(item.due_date).toLocaleDateString() : 'No due date';
            sub = `Assigned to: ${item.user_name || 'N/A'} • Due: ${due}`;
        }
        let btnHtml = `<span class="va" style="color:#16a34a">Approve</span>`;
        if (type === 'tk') {
            btnHtml = `<span class="va" style="color:#16a34a;cursor:pointer" onclick="approveTask('${item.id}')">Approve</span>`;
        }
        html += `<div class="ti"><div style="flex:1"><div class="tnm">${title}</div><div class="tdue">${sub}</div></div>${btnHtml}</div>`;
    });
    cont.innerHTML = html;
}

function approveTask(id) {
    if (!confirm('Are you sure you want to mark this task as done?')) return;
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
        }
    });
}

function renderTasks(containerId, tasks) {
    const cont = document.getElementById(containerId);
    if (!cont) return;
    if (!tasks.length) {
        cont.innerHTML = '<div style="text-align:center;padding:20px;color:#9ca3af;font-size:12px">No tasks found</div>';
        return;
    }
    let html = '';
    tasks.forEach(t => {
        const dot = t.priority === 'High' ? 'r' : (t.priority === 'Medium' ? 'o' : 'g');
        const due = t.due_date ? new Date(t.due_date).toLocaleDateString() : 'No due date';
        const label = containerId === 'tk-byMe' ? 'Assigned to: ' + (t.assigned_to_name || 'N/A') : 'Assigned by: ' + (t.assigned_by_name || 'N/A');
        html += `<div class="ti"><div class="tdot ${dot}"></div><div style="flex:1"><div class="tnm">${t.task_name}</div><div class="tdue">${label} • Due ${due}</div></div><span class="tm">···</span></div>`;
    });
    cont.innerHTML = html;
}

$(function () {
    tick();setInterval(tick,1000);
    loadCalendarData(calY,calM);
    loadDashboardMetrics();
    
    $(document).on('click', '.pibtn', function() {
        const btn = $(this);
        btn.prop('disabled', true).text('Processing...');
        $.ajax({
            url: '/attendance/punch-in',
            method: 'POST',
            data: { _token: '<?php echo e(csrf_token()); ?>' },
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/dashboard.blade.php ENDPATH**/ ?>