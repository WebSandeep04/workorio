@extends('layouts.app')

@section('title', 'Sales Analytics Dashboard')
@section('page_title', 'Sales Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Analytics</h5>
                        </div>
                        <div class="col-md-6">
                            <select id="userFilter" class="form-select">
                                <option value="">All Users</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="m-0 font-weight-bold">Tenant Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 id="tenantName">-</h5>
                            <p class="text-muted">Code: <span id="tenantCode">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created:</strong> <span id="tenantCreated">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalUsers">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sales Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="salesUsers">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Leads</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalLeads">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Follow-ups</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingFollowups">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Lead Trends Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="m-0 font-weight-bold">Lead Trends (Last 12 Months)</h6>
                </div>
                <div class="card-body">
                    <canvas id="leadTrendsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- User Distribution Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="m-0 font-weight-bold">User Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- User Analytics Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="m-0 font-weight-bold">User Analytics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="userAnalyticsTable">
                            <thead>
                                <tr>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Manager</th>
                                    <th>Total Leads</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="userAnalyticsBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h6 class="m-0 font-weight-bold">Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div id="recentActivities" style="max-height: 400px; overflow-y: auto;">
                        <!-- Activities will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Analytics Modal -->
<div class="modal fade" id="userAnalyticsModal" tabindex="-1" aria-labelledby="userAnalyticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="userAnalyticsModalLabel">User Analytics</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="userAnalyticsContent">
                    <!-- User analytics content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.font-weight-bold {
    font-weight: 700 !important;
}

.text-xs {
    font-size: 0.7rem;
}

.activity-item {
    padding: 10px;
    border-left: 3px solid #667eea;
    margin-bottom: 10px;
    background-color: #f8f9fc;
    border-radius: 0 5px 5px 0;
}

.activity-item:hover {
    background-color: #eaecf4;
}

.activity-timestamp {
    font-size: 0.8rem;
    color: #858796;
}

.activity-tenant {
    font-size: 0.8rem;
    color: #667eea;
    font-weight: 600;
}

.user-stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.user-stats-card h6 {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.user-stats-card .stat-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0;
}

.performance-chart {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.recent-leads-table {
    font-size: 0.9rem;
}

.recent-leads-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

.recent-leads-table td {
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let leadTrendsChart, userDistributionChart;
let currentUserFilter = '';

$(document).ready(function() {
    loadAnalytics();
    loadUsers();
    
    // User filter change
    $('#userFilter').on('change', function() {
        currentUserFilter = $(this).val();
        if (currentUserFilter) {
            loadUserAnalytics(currentUserFilter);
        } else {
            loadAnalytics();
        }
    });
    
    // Modal cleanup when closed
    $('#userAnalyticsModal').on('hidden.bs.modal', function() {
        $('#userAnalyticsContent').html('');
    });
});

function loadAnalytics() {
    $.ajax({
        url: '{{ route("sales-analytics.data") }}',
        type: 'GET',
        success: function(response) {
            updateStatistics(response);
            updateUserTable(response.tenant_info);
            updateRecentActivities(response.recent_activities);
            createCharts(response);
        },
        error: function(xhr) {
            console.error('Error loading analytics:', xhr.responseText);
        }
    });
}

function loadUsers() {
    $.ajax({
        url: '{{ route("sales-analytics.users") }}',
        type: 'GET',
        success: function(response) {
            let options = '<option value="">All Users</option>';
            response.forEach(function(user) {
                options += `<option value="${user.id}">${user.name} (${user.role})</option>`;
            });
            $('#userFilter').html(options);
        },
        error: function(xhr) {
            console.error('Error loading users:', xhr.responseText);
        }
    });
}

function loadUserAnalytics(userId) {
    $.ajax({
        url: '{{ route("sales-analytics.user") }}',
        type: 'GET',
        data: {
            user_id: userId
        },
        success: function(response) {
            showUserAnalyticsModal(response);
        },
        error: function(xhr) {
            console.error('Error loading user analytics:', xhr.responseText);
        }
    });
}

function updateStatistics(data) {
    // Update tenant information
    if (data.tenant_info) {
        $('#tenantName').text(data.tenant_info.tenant_name);
        $('#tenantCode').text(data.tenant_info.tenant_code);
        $('#tenantCreated').text(data.tenant_info.created_at);
        $('#totalUsers').text(data.tenant_info.total_users);
        $('#salesUsers').text(data.tenant_info.sales_users);
        $('#totalLeads').text(data.tenant_info.total_leads);
    }
    
    // Update follow-up statistics
    if (data.followup_statistics) {
        $('#pendingFollowups').text(data.followup_statistics.pending_followups || 0);
    }
}

function updateUserTable(tenantInfo) {
    let html = '';
    if (tenantInfo && tenantInfo.users) {
        tenantInfo.users.forEach(function(user) {
            html += `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.role}</td>
                    <td>${user.manager}</td>
                    <td>${user.leads_count}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewUserAnalytics(${user.id})">
                            <i class="bi bi-eye"></i> View Details
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    $('#userAnalyticsBody').html(html);
}

function updateRecentActivities(activities) {
    let html = '';
    activities.forEach(function(activity) {
        html += `
            <div class="activity-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <p class="mb-1">${activity.message}</p>
                    </div>
                    <small class="activity-timestamp">${activity.timestamp}</small>
                </div>
            </div>
        `;
    });
    $('#recentActivities').html(html);
}

function createCharts(data) {
    // Lead Trends Chart
    const leadTrendsCtx = document.getElementById('leadTrendsChart').getContext('2d');
    if (leadTrendsChart) {
        leadTrendsChart.destroy();
    }
    
    const leadTrendsData = data.lead_statistics.leads_by_month || [];
    leadTrendsChart = new Chart(leadTrendsCtx, {
        type: 'line',
        data: {
            labels: leadTrendsData.map(item => item.month),
            datasets: [{
                label: 'Leads Created',
                data: leadTrendsData.map(item => item.count),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // User Distribution Chart
    const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
    if (userDistributionChart) {
        userDistributionChart.destroy();
    }
    
    const userDistributionData = data.user_distribution || [];
    const labels = userDistributionData.map(item => item.role_name);
    const dataValues = userDistributionData.map(item => item.count);
    const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'];
    
    userDistributionChart = new Chart(userDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: colors.slice(0, labels.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function showUserAnalyticsModal(data) {
    let html = `
        <div class="row">
            <div class="col-12">
                <div class="user-stats-card">
                    <h6>User Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ${data.user_info.name}</p>
                            <p><strong>Email:</strong> ${data.user_info.email}</p>
                            <p><strong>Role:</strong> ${data.user_info.role}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tenant:</strong> ${data.user_info.tenant}</p>
                            <p><strong>Manager:</strong> ${data.user_info.manager}</p>
                            <p><strong>Is Manager:</strong> ${data.user_info.is_manager}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary">${data.lead_statistics.total_leads}</h5>
                        <p class="card-text">Total Leads</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">${data.followup_statistics.completed_followups}</h5>
                        <p class="card-text">Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">${data.followup_statistics.pending_followups}</h5>
                        <p class="card-text">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">${data.followup_statistics.today_followups}</h5>
                        <p class="card-text">Today</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h6 class="mb-0">Recent Leads</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm recent-leads-table">
                                <thead>
                                    <tr>
                                        <th>Lead</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
    `;
    
    if (data.recent_leads && data.recent_leads.length > 0) {
        data.recent_leads.forEach(function(lead) {
            html += `
                <tr>
                    <td>${lead.leads_name || 'N/A'}</td>
                    <td><span class="badge bg-primary">${lead.status || 'N/A'}</span></td>
                    <td>${lead.created_at || 'N/A'}</td>
                </tr>
            `;
        });
    } else {
        html += '<tr><td colspan="3" class="text-center">No recent leads found</td></tr>';
    }
    
    html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#userAnalyticsContent').html(html);
    $('#userAnalyticsModal').modal('show');
}

function viewUserAnalytics(userId) {
    // Load user analytics modal
    loadUserAnalytics(userId);
}
</script>
@endpush
