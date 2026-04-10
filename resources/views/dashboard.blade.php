@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
@php
    $userDisplayName = auth()->check()
        ? auth()->user()->name
        : (session('user_name') ?? 'there');
    $firstName = explode(' ', trim($userDisplayName))[0] ?: 'there';
@endphp
<div class="neo-dashboard">
    <div class="dashboard-header mb-3">
        <h4 class="fw-bold" style="font-family: Montserrat; color: #101828;">Sales Intelligence</h4>
        <p class="text-muted small">Real-time performance metrics for your sales pipeline</p>
    </div>
    <div class="neo-metric-grid">
        <div class="neo-metric-card">
            <div class="metric-icon icon-sunrise">
                <img src="{{ asset('img/icons/call.png') }}" alt="Calls">
            </div>
            <div class="metric-content">
                <p>Today's Followups</p>
                <h3 id="todayfollowups">0</h3>  
            </div>
            <a href="{{ route('todayfollowupstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon icon-amber">
                <img src="{{ asset('img/icons/underprocess.png') }}" alt="Hourglass">
            </div>
            <div class="metric-content">
                <p>Under Process</p>
                <h3 id="underprocess">0</h3>
            </div>
            <a href="{{ route('underprocesstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon icon-emerald">
                <img src="{{ asset('img/icons/tick.png') }}" alt="Completed">
            </div>
            <div class="metric-content">    
                <p>Today Completed</p>
                <h3 id="todaycompleted">0</h3>
            </div>
            <a href="{{ route('todaycompletedtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon icon-rose">
                <img src="{{ asset('img/icons/pending.png') }}" alt="Pending">
            </div>
            <div class="metric-content">
                <p>Today Pending</p>
                <h3 id="todaypending">0</h3>
            </div>
            <a href="{{ route('todaypendingtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon icon-sky">
                <img src="{{ asset('img/icons/new.png') }}" alt="New">
            </div>
            <div class="metric-content">
                <p>New Followups</p>
                <h3 id="todaynew">0</h3>
            </div>
            <a href="{{ route('todaynewtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card-blue">
            <div class="metric-icon icon-violet">
                <img src="{{ asset('img/icons/all.png') }}" alt="All Leads">
            </div>
            <div class="metric-content-blue">
                <p>My All Leads</p>
                <h3 id="allleads">0</h3>
            </div>
            <a href="{{ route('myleads') }}" class="metric-arrow-blue"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <div class="dashboard-header mb-3 mt-5">
        <h4 class="fw-bold" style="font-family: Montserrat; color: #101828;">Tele-Calling Status</h4>
        <p class="text-muted small">Daily calling activity and follow-up tracking</p>
    </div>
    <div class="neo-metric-grid">
        <div class="neo-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #434AFA, #667eea);">
                <i class="bi bi-telephone-outbound text-white"></i>
            </div>
            <div class="metric-content">
                <p>Today's Followups</p>
                <h3 id="c_todayfollowups">0</h3>  
            </div>
            <a href="{{ route('calling.todayfollowupstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                <i class="bi bi-hourglass-split text-white"></i>
            </div>
            <div class="metric-content">
                <p>Under Process</p>
                <h3 id="c_underprocess">0</h3>
            </div>
            <a href="{{ route('calling.underprocesstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                <i class="bi bi-check-circle text-white"></i>
            </div>
            <div class="metric-content">    
                <p>Today Completed</p>
                <h3 id="c_todaycompleted">0</h3>
            </div>
            <a href="{{ route('calling.todaycompletedtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);">
                <i class="bi bi-exclamation-triangle text-white"></i>
            </div>
            <div class="metric-content">
                <p>Today Pending</p>
                <h3 id="c_todaypending">0</h3>
            </div>
            <a href="{{ route('calling.todaypendingtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card">
            <div class="metric-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                <i class="bi bi-person-plus text-white"></i>
            </div>
            <div class="metric-content">
                <p>New Followups</p>
                <h3 id="c_todaynew">0</h3>
            </div>
            <a href="{{ route('calling.todaynewtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="neo-metric-card-blue">
            <div class="metric-icon" style="background: #101828;">
                <i class="bi bi-collection text-white"></i>
            </div>
            <div class="metric-content-blue">
                <p>My All Leads</p>
                <h3 id="c_allleads">0</h3>
            </div>
            <a href="{{ route('calling.allleadstable') }}" class="metric-arrow-blue"><i class="bi bi-arrow-right"></i></a>
        </div>
    </div>

    <!-- <div class="neo-panel">
        <div class="neo-panel-header">
            <div>
                <p class="neo-eyebrow">Productivity</p>
                <h3 class="neo-panel-title">My Tasks</h3>
            </div>
            <div class="panel-tags">
                <span class="tag active">All</span>
                <span class="tag">High</span>
                <span class="tag">Completed</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="neo-task-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned By</th>
                        <th>Created On</th>
                        <th>Urgency</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tasksTableBody">
                    <tr class="empty-row">
                        <td colspan="5">Loading tasks...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> -->
</div>
@endsection

@push('styles')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .neo-dashboard {
        padding: 10px;
        /* font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; */
        background: #f6f6f6;
    }

    .neo-eyebrow {
        text-transform: uppercase;
        letter-spacing: 0.18em;
        font-size: 0.65rem;
        color: #a0a6b5;
        margin-bottom: 0.3rem;
    }

    .neo-metric-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
        margin-bottom: 1.5rem;
    }

    .neo-metric-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef3;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0px 4px 4px 0px #0000000A;
        position: relative;
        overflow: hidden;
    }

    .neo-metric-card-blue {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef3;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0px 4px 4px 0px #0000000A;
        position: relative;
        overflow: hidden;
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
    }

    .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
    .icon-amber   { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .icon-rose    { background: linear-gradient(135deg, #fb7185, #f43f5e); }
    .icon-sky     { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-violet  { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  

    .metric-content p {
        margin: 0;
        font-size: 0.8rem;
        color: #000;
        font-family: Montserrat !important;
        font-weight: 700;        

    }

    .metric-content-blue p {
        margin: 0;
        font-size: 0.8rem;  
        color: #000;
        font-family: Montserrat !important;
        font-weight: 700; 
    }

    .metric-content h3 {
        margin: 0;
        font-size: 1.9rem;
        color: #101828;
        font-weight: 700;
        font-family: Montserrat;
    }

    .metric-content-blue h3 {
        margin: 0;
        font-size: 1.9rem;
        color: #000;
        font-weight: 700;
        font-family: Montserrat;
    }

    .metric-arrow {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        /* border: 1px solid #eceef3; */
        /* background: #f4f4ff; */
        color: #000;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        position: absolute;
        right: 16px;
        bottom: 16px;
    }

    .metric-arrow-blue {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        /* border: 1px solid #eceef3; */
        /* background: #f4f4ff; */
        color: #000;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        position: absolute;
        right: 16px;
        bottom: 16px;
    }

    .metric-arrow:hover {
        background: #5b59f7;
        color: #fff;
    }

    .neo-panel {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #eceef3;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.05);
        padding: 1.5rem;
    }

    .neo-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .neo-panel-title {
        margin: 0;
        font-size: 1.2rem;
        color: #111827;
    }

    .panel-tags .tag {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        border: 1px solid #eceef3;
        font-size: 0.75rem;
        margin-right: 0.35rem;
        color: #6b7280;
        cursor: pointer;
    }

    .panel-tags .tag.active {
        border-color: #c7cbe1;
        background: #f4f4ff;
        color: #4338ca;
        font-weight: 600;
    }

    .neo-task-table {
        width: 100%;
        border-collapse: collapse;
    }

    .neo-task-table th {
        text-transform: uppercase;
        letter-spacing: 0.18em;
        font-size: 0.65rem;
        color: #a0a6b5;
        font-weight: 600;
        padding-bottom: 0.75rem;
    }

    .neo-task-table td {
        padding: 0.9rem 0;
        border-top: 1px solid #f1f3f9;
        color: #1f2937;
    }

    .neo-task-table tr:first-child td {
        border-top: none;
    }

    .table-checkbox {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        font-weight: 600;
        color: #111827;
    }

    .table-checkbox input {
        width: 16px;
        height: 16px;
    }

    .status-pill,
    .urgency-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-green { background: rgba(34, 197, 94, 0.15); color: #15803d; }
    .status-amber { background: rgba(251, 191, 36, 0.2); color: #92400e; }
    .status-red   { background: rgba(248, 113, 113, 0.2); color: #b91c1c; }
    .status-gray  { background: rgba(148, 163, 184, 0.3); color: #475569; }

    .priority-high   { background: rgba(239, 68, 68, 0.15); color: #b91c1c; }
    .priority-medium { background: rgba(251, 191, 36, 0.15); color: #92400e; }
    .priority-low    { background: rgba(34, 197, 94, 0.15); color: #15803d; }

    .empty-row td {
        text-align: center;
        color: #9ca3af;
        padding: 1.5rem 0;
    }

    @media (max-width: 767px) {
        .neo-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .neo-date-chip {
            width: 100%;
            text-align: center;
        }

        .neo-metric-card{
            margin-bottom: 10px;
        }

        .neo-metric-grid{
            display: block;
        }

        .neo-dashboard {
            margin-left: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * (end - start) + start);
            element.textContent = current;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    function fetchMetric(url, elementId, key) {
        $.ajax({
            url,
            method: 'GET',
            success: function (response) {
                const value = Number(response && response[key]) || 0;
                const el = document.getElementById(elementId);
                if (el) animateValue(el, 0, value, 900);
            },
            error: function () {
                const el = document.getElementById(elementId);
                if (el) el.textContent = '0';
            }
        });
    }

    function loadDashboardMetrics() {
        // Sales Metrics
        fetchMetric('/todayfollowups', 'todayfollowups', 'totalLeads');
        fetchMetric('/underprocess', 'underprocess', 'underprocess');
        fetchMetric('/todaycompleted', 'todaycompleted', 'todaycompleted');
        fetchMetric('/todaypending', 'todaypending', 'todaypending');
        fetchMetric('/todaynew', 'todaynew', 'todaynew');
        fetchMetric('/allleads', 'allleads', 'allleads');

        // Calling Metrics
        fetchMetric('/calling/todayfollowups', 'c_todayfollowups', 'totalLeads');
        fetchMetric('/calling/underprocess', 'c_underprocess', 'underprocess');
        fetchMetric('/calling/todaycompleted', 'c_todaycompleted', 'todaycompleted');
        fetchMetric('/calling/todaypending', 'c_todaypending', 'todaypending');
        fetchMetric('/calling/todaynew', 'c_todaynew', 'todaynew');
        fetchMetric('/calling/allleads', 'c_allleads', 'allleads');
    }

    function loadTasks() {
        $.ajax({
            url: '/user-tasks',
            method: 'GET',
            success: function (response) {
                const tasks = response && response.tasks ? response.tasks : [];
                renderTaskTable(tasks);
            },
            error: function () {
                renderTaskTable([]);
            }
        });
    }

    function renderTaskTable(tasks) {
        const body = $('#tasksTableBody');
        if (!tasks || tasks.length === 0) {
            body.html(`<tr class="empty-row"><td colspan="5">You're all caught up – no tasks assigned 🎉</td></tr>`);
            return;
        }

        const rows = tasks.slice(0, 6).map(task => `
            <tr>
                <td>
                    <label class="table-checkbox">
                        <input type="checkbox" />
                        <span>${task.task_name || 'Untitled Task'}</span>
                    </label>
                </td>
                <td>${task.user_name || 'N/A'}</td>
                <td>${formatDate(task.created_at)}</td>
                <td><span class="urgency-pill ${priorityClass(task.priority_name)}">${task.priority_name || '—'}</span></td>
                <td><span class="status-pill ${statusClass(task.status_name)}">${task.status_name || 'Pending'}</span></td>
            </tr>
        `).join('');

        body.html(rows);
    }

    function statusClass(status) {
        if (!status) return 'status-gray';
        const map = {
            'completed': 'status-green',
            'in progress': 'status-amber',
            'pending': 'status-gray',
            'cancelled': 'status-red'
        };
        return map[status.toLowerCase()] || 'status-gray';
    }

    function priorityClass(priority) {
        if (!priority) return 'priority-medium';
        const map = {
            'high': 'priority-high',
            'medium': 'priority-medium',
            'low': 'priority-low'
        };
        return map[priority.toLowerCase()] || 'priority-medium';
    }

    function formatDate(dateString) {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    $(function () {
        loadDashboardMetrics();
        loadTasks();
    });
</script>
@endpush

