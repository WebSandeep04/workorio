@extends('layouts.app')

@section('title', 'Team Analytics')
@section('page_title', 'Team Analytics')

@section('content')
<div class="container mt-4">
    <!-- Team Member Selection -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Select Team Member</h5>
                </div>
                <div class="card-body">
                    <select id="teamMemberSelect" class="form-select">
                        <option value="">Select a team member</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Team Overview</h5>
                </div>
                <div class="card-body">
                    <button id="loadTeamOverview" class="btn btn-primary">Load Team Overview</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Overview Section -->
    <div id="teamOverviewSection" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Team Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stats-card blue">
                                <h3 id="teamTotalLeads">0</h3>
                                <p>Total Team Leads</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card green">
                                <h3 id="teamLeadsToday">0</h3>
                                <p>Team Leads Today</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card orange">
                                <h3 id="teamFollowUpsToday">0</h3>
                                <p>Team Follow-ups Today</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card purple">
                                <h3 id="teamMemberCount">0</h3>
                                <p>Team Members</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Team Status Distribution -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h6>Team Status Distribution</h6>
                                <div id="teamStatusChart">
                                    <canvas id="teamStatusChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h6>Individual Member Stats</h6>
                                <div id="memberStatsTable"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Individual Member Analytics Section -->
    <div id="memberAnalyticsSection" class="row mb-4" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Member Analytics - <span id="selectedMemberName"></span></h5>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="stats-card blue">
                                <h3 id="totalLeads">0</h3>
                                <p>Total Leads</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card green">
                                <h3 id="leadsThisMonth">0</h3>
                                <p>This Month</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card orange">
                                <h3 id="leadsThisWeek">0</h3>
                                <p>This Week</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card purple">
                                <h3 id="leadsToday">0</h3>
                                <p>Today</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card pink">
                                <h3 id="followUpsToday">0</h3>
                                <p>Follow-ups Today</p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stats-card cyan">
                                <h3 id="followUpsThisWeek">0</h3>
                                <p>Follow-ups This Week</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                <h3 id="todayDone">0</h3>
                                <p>Today Done</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h3 id="todayPending">0</h3>
                                <p>Today Pending</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h3 id="todayUnderProcess">0</h3>
                                <p>Today Under Process</p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Tables -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h6>Status Distribution</h6>
                                <div id="statusChart">
                                    <canvas id="statusChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-container">
                                <h6>Monthly Trend</h6>
                                <div id="monthlyTrendChart">
                                    <canvas id="monthlyTrendChartCanvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Enhanced Card Styling */
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
        transition: transform 0.2s ease-in-out;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        border: none;
        padding: 1.25rem;
    }
    
    .card-header h5 {
        margin: 0;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Statistics Cards */
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
    
    .stats-card h3 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .stats-card p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    /* Different colored stat cards */
    .stats-card.blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stats-card.green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .stats-card.orange {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stats-card.purple {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stats-card.pink {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .stats-card.cyan {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        color: #333;
    }
    
    /* Button Styling */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    
    /* Select Styling */
    .form-select {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    /* Table Styling */
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 1rem;
    }
    
    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Chart Container Styling */
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }
    
    .chart-container h6 {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 1rem;
        text-align: center;
    }
    
    /* Section Headers */
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .section-header h5 {
        margin: 0;
        font-weight: 600;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-card h3 {
            font-size: 2rem;
        }
        
        .card-header {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Fallback if Chart.js fails to load
if (typeof Chart === 'undefined') {
    // Try alternative CDN
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
    script.onload = function() {
        console.log('Chart.js loaded from fallback CDN');
    };
    script.onerror = function() {
        console.error('Failed to load Chart.js from both CDNs');
    };
    document.head.appendChild(script);
}
</script>
<script>
$(document).ready(function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }
    
    loadTeamMembers();
    
    // Load team members
    function loadTeamMembers() {
        $.ajax({
            url: '{{ route("team-analytics.members") }}',
            type: 'GET',
            success: function(response) {
                let options = '<option value="">Select a team member</option>';
                response.forEach(function(member) {
                    options += `<option value="${member.id}">${member.name}</option>`;
                });
                $('#teamMemberSelect').html(options);
            },
            error: function(xhr) {
                console.error('Error loading team members:', xhr.responseText);
            }
        });
    }
    
    // Handle team member selection
    $('#teamMemberSelect').on('change', function() {
        const memberId = $(this).val();
        const memberName = $(this).find('option:selected').text();
        
        if (memberId) {
            $('#selectedMemberName').text(memberName);
            loadMemberAnalytics(memberId);
            $('#memberAnalyticsSection').show();
        } else {
            $('#memberAnalyticsSection').hide();
        }
    });
    
    // Load team overview
    $('#loadTeamOverview').on('click', function() {
        loadTeamOverview();
    });
    
    // Load member analytics
    function loadMemberAnalytics(memberId) {
        $.ajax({
            url: '{{ route("team-analytics.member") }}',
            type: 'POST',
            data: {
                member_id: memberId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                try {
                    updateMemberStats(response);
                    if (response.status_distribution) {
                        createStatusChart(response.status_distribution);
                    }
                    if (response.monthly_trend) {
                        createMonthlyTrendChart(response.monthly_trend);
                    }

                } catch (error) {
                    console.error('Error processing member analytics:', error);
                }
            },
            error: function(xhr) {
                console.error('Error loading member analytics:', xhr.responseText);
            }
        });
    }
    
    // Load team overview
    function loadTeamOverview() {
        $.ajax({
            url: '{{ route("team-analytics.overview") }}',
            type: 'GET',
            success: function(response) {
                try {
                    updateTeamOverview(response);
                    if (response.team_status_distribution) {
                        createTeamStatusChart(response.team_status_distribution);
                    }
                    if (response.member_stats) {
                        updateMemberStatsTable(response.member_stats);
                    }
                    $('#teamOverviewSection').show();
                } catch (error) {
                    console.error('Error processing team overview:', error);
                }
            },
            error: function(xhr) {
                console.error('Error loading team overview:', xhr.responseText);
            }
        });
    }
    
    // Update member statistics
    function updateMemberStats(data) {
        $('#totalLeads').text(data.total_leads);
        $('#leadsThisMonth').text(data.leads_this_month);
        $('#leadsThisWeek').text(data.leads_this_week);
        $('#leadsToday').text(data.leads_today);
        $('#followUpsToday').text(data.follow_ups_today);
        $('#followUpsThisWeek').text(data.follow_ups_this_week);
        $('#todayDone').text(data.today_done);
        $('#todayPending').text(data.today_pending);
        $('#todayUnderProcess').text(data.today_under_process);
    }
    
    // Update team overview
    function updateTeamOverview(data) {
        $('#teamTotalLeads').text(data.team_total_leads);
        $('#teamLeadsToday').text(data.team_leads_today);
        $('#teamFollowUpsToday').text(data.team_follow_ups_today);
        $('#teamMemberCount').text(data.member_stats.length);
    }
    
    // Create status distribution chart
    function createStatusChart(statusData) {
        const canvas = document.getElementById('statusChartCanvas');
        if (!canvas) {
            console.error('Canvas element statusChartCanvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2D context from canvas');
            return;
        }
        
        // Destroy existing chart if it exists
        if (window.statusChart && typeof window.statusChart.destroy === 'function') {
            window.statusChart.destroy();
        }
        
        // Check if we have data
        if (!statusData || statusData.length === 0) {
            console.log('No status data available for chart');
            return;
        }
        
        const labels = statusData.map(item => item.status_name);
        const data = statusData.map(item => item.count);
        
        try {
            window.statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            console.log('Status chart created successfully');
        } catch (error) {
            console.error('Error creating status chart:', error);
        }
    }
    
    // Create monthly trend chart
    function createMonthlyTrendChart(trendData) {
        const canvas = document.getElementById('monthlyTrendChartCanvas');
        if (!canvas) {
            console.error('Canvas element monthlyTrendChartCanvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2D context from canvas');
            return;
        }
        
        // Destroy existing chart if it exists
        if (window.monthlyTrendChart && typeof window.monthlyTrendChart.destroy === 'function') {
            window.monthlyTrendChart.destroy();
        }
        
        // Check if we have data
        if (!trendData || trendData.length === 0) {
            console.log('No trend data available for chart');
            return;
        }
        
        const labels = trendData.map(item => item.month);
        const data = trendData.map(item => item.count);
        
        try {
            window.monthlyTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Leads',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            console.log('Monthly trend chart created successfully');
        } catch (error) {
            console.error('Error creating monthly trend chart:', error);
        }
    }
    
    // Create team status chart
    function createTeamStatusChart(statusData) {
        const canvas = document.getElementById('teamStatusChartCanvas');
        if (!canvas) {
            console.error('Canvas element teamStatusChartCanvas not found');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.error('Could not get 2D context from canvas');
            return;
        }
        
        // Destroy existing chart if it exists
        if (window.teamStatusChart && typeof window.teamStatusChart.destroy === 'function') {
            window.teamStatusChart.destroy();
        }
        
        // Check if we have data
        if (!statusData || statusData.length === 0) {
            console.log('No team status data available for chart');
            return;
        }
        
        const labels = statusData.map(item => item.status_name);
        const data = statusData.map(item => item.count);
        
        try {
            window.teamStatusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            console.log('Team status chart created successfully');
        } catch (error) {
            console.error('Error creating team status chart:', error);
        }
    }
    

    
    // Update member stats table
    function updateMemberStatsTable(memberStats) {
        let html = '';
        memberStats.forEach(function(member) {
            html += `
                <tr>
                    <td>${member.name}</td>
                    <td>${member.total_leads}</td>
                    <td>${member.leads_today}</td>
                    <td>${member.follow_ups_today}</td>
                </tr>
            `;
        });
        
        const tableHtml = `
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Total Leads</th>
                        <th>Leads Today</th>
                        <th>Follow-ups Today</th>
                    </tr>
                </thead>
                <tbody>
                    ${html}
                </tbody>
            </table>
        `;
        $('#memberStatsTable').html(tableHtml);
    }
});
</script>
@endpush
