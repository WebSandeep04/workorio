@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">📊 Attendance Analytics & Statistics</h4>
                    <div>
                        <button class="btn btn-info btn-sm" onclick="refreshStats()">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <a href="{{ route('attendance') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-clock"></i> Back to Attendance
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading State -->
                    <div id="loadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading your attendance statistics...</p>
                    </div>

                    <!-- Main Stats Content -->
                    <div id="statsContent" style="display: none;">
                        <!-- Overview Stats -->
                        <div class="row mb-4" id="overviewStats">
                            <div class="col-12">
                                <h5 class="mb-3">📈 Overview Statistics</h5>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-primary text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                        <h6>Total Days</h6>
                                        <h3 id="totalDays">0</h3>
                                        <small>Days worked</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x mb-2"></i>
                                        <h6>Total Hours</h6>
                                        <h3 id="totalHours">0</h3>
                                        <small>Hours logged</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-info text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <h6>Office Hours</h6>
                                        <h3 id="totalOfficeHours">0</h3>
                                        <small>In office</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-warning text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                                        <h6>Field Hours</h6>
                                        <h3 id="totalFieldHours">0</h3>
                                        <small>Field work</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-secondary text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-coffee fa-2x mb-2"></i>
                                        <h6>Break Time</h6>
                                        <h3 id="totalBreakTime">0</h3>
                                        <small>Hours on break</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                                <div class="card bg-dark text-white h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                        <h6>Avg Hours/Day</h6>
                                        <h3 id="avgHoursPerDay">0</h3>
                                        <small>Daily average</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Stats -->
                        <div class="row mb-4" id="todayStats">
                            <div class="col-12">
                                <h5 class="mb-3">🌅 Today's Performance</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-primary text-white">
                                    <div class="card-body text-center">
                                        <h6>Today's Hours</h6>
                                        <h3 id="todayHours">0</h3>
                                        <div class="progress mt-2" style="height: 5px;">
                                            <div class="progress-bar bg-white" id="todayProgress" style="width: 0%"></div>
                                        </div>
                                        <small>vs 8hr target</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-success text-white">
                                    <div class="card-body text-center">
                                        <h6>Office Today</h6>
                                        <h3 id="todayOfficeHours">0</h3>
                                        <small>hrs in office</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-info text-white">
                                    <div class="card-body text-center">
                                        <h6>Field Today</h6>
                                        <h3 id="todayFieldHours">0</h3>
                                        <small>hrs in field</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-gradient-warning text-white">
                                    <div class="card-body text-center">
                                        <h6>Cycles Today</h6>
                                        <h3 id="todayCycles">0</h3>
                                        <small>total cycles</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Trends & Productivity -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6>📈 Trends & Consistency</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h6>Hours Trend</h6>
                                                    <h4 id="hoursTrend">
                                                        <span class="badge bg-secondary">Stable</span>
                                                    </h4>
                                                    <small class="text-muted">Last 7 days vs previous 7</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h6>Consistency Score</h6>
                                                    <h4 id="consistencyScore">0%</h4>
                                                    <div class="progress mt-2">
                                                        <div class="progress-bar" id="consistencyProgress" style="width: 0%"></div>
                                                    </div>
                                                    <small class="text-muted">Higher is better</small>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Recent Average</small>
                                                <div><strong id="recentAvgHours">0</strong> hrs/day</div>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Previous Average</small>
                                                <div><strong id="previousAvgHours">0</strong> hrs/day</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h6>🎯 Productivity Analysis</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h6>Productive Days</h6>
                                                    <h4 id="productiveDays" class="text-success">0</h4>
                                                    <small class="text-muted">≥6 hours work</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <h6>Productivity Rate</h6>
                                                    <h4 id="productivityRate">0%</h4>
                                                    <div class="progress mt-2">
                                                        <div class="progress-bar bg-success" id="productivityProgress" style="width: 0%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="text-center">
                                            <small class="text-muted">Average Productive Hours</small>
                                            <div><strong id="avgProductiveHours">0</strong> hrs on productive days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly & Monthly Charts -->
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>📊 Weekly Performance Trends</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="weeklyChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>🔄 Work Distribution</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="distributionChart"></canvas>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-square text-primary"></i> Office</span>
                                                <span id="officePercentage">0%</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-square text-success"></i> Field</span>
                                                <span id="fieldPercentage">0%</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span><i class="fas fa-square text-warning"></i> Break</span>
                                                <span id="breakPercentage">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Stats Table -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>📅 Monthly Breakdown</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="monthlyStatsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Days</th>
                                                        <th>Total Hours</th>
                                                        <th>Office Hours</th>
                                                        <th>Field Hours</th>
                                                        <th>Avg/Day</th>
                                                        <th>Cycles</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="monthlyStatsBody">
                                                    <!-- Data will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Patterns & Insights -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>🗓️ Day of Week Patterns</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="dayOfWeekChart" height="150"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>⏰ Activity Patterns</h6>
                                    </div>
                                    <div class="card-body">
                                        <h6>Most Active Hours</h6>
                                        <div id="mostActiveHours" class="mb-3">
                                            <!-- Will be populated -->
                                        </div>
                                        <h6>Peak Performance Days</h6>
                                        <div id="peakDays">
                                            <!-- Will be populated -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let weeklyChart, distributionChart, dayOfWeekChart;

// Load stats on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAdvancedStats();
});

function refreshStats() {
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('statsContent').style.display = 'none';
    loadAdvancedStats();
}

function loadAdvancedStats() {
    $.ajax({
        url: '/attendance/advanced-stats',
        method: 'GET',
        success: function(response) {
            console.log('Advanced stats response:', response);
            populateStats(response);
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('statsContent').style.display = 'block';
        },
        error: function(xhr, status, error) {
            console.error('Error loading advanced stats:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            document.getElementById('loadingState').innerHTML = 
                '<div class="alert alert-danger">Failed to load statistics. Please try again.</div>';
        }
    });
}

function populateStats(data) {
    // Overview Stats
    const overview = data.overview;
    document.getElementById('totalDays').textContent = overview.total_days;
    document.getElementById('totalHours').textContent = overview.total_hours;
    document.getElementById('totalOfficeHours').textContent = overview.total_office_hours;
    document.getElementById('totalFieldHours').textContent = overview.total_field_hours;
    document.getElementById('totalBreakTime').textContent = overview.total_break_time;
    document.getElementById('avgHoursPerDay').textContent = overview.avg_hours_per_day;

    // Today's Stats
    const today = overview.today;
    document.getElementById('todayHours').textContent = today.hours;
    document.getElementById('todayOfficeHours').textContent = today.office_hours;
    document.getElementById('todayFieldHours').textContent = today.field_hours;
    
    const todayCycles = today.cycles.office + today.cycles.field + today.cycles.break;
    document.getElementById('todayCycles').textContent = todayCycles;
    
    // Today's progress bar
    const todayProgress = Math.min((today.hours / 8) * 100, 100);
    document.getElementById('todayProgress').style.width = todayProgress + '%';

    // Trends
    const trends = data.trends;
    const trendElement = document.getElementById('hoursTrend');
    const trendBadgeClass = trends.hours_trend === 'increasing' ? 'bg-success' : 
                           trends.hours_trend === 'decreasing' ? 'bg-danger' : 'bg-secondary';
    const trendIcon = trends.hours_trend === 'increasing' ? '📈' : 
                     trends.hours_trend === 'decreasing' ? '📉' : '➡️';
    
    trendElement.innerHTML = `<span class="badge ${trendBadgeClass}">${trendIcon} ${trends.hours_trend}</span>`;
    
    document.getElementById('consistencyScore').textContent = trends.consistency_score + '%';
    document.getElementById('consistencyProgress').style.width = trends.consistency_score + '%';
    document.getElementById('recentAvgHours').textContent = trends.recent_avg_hours;
    document.getElementById('previousAvgHours').textContent = trends.previous_avg_hours;

    // Productivity
    const productivity = data.productivity;
    document.getElementById('productiveDays').textContent = productivity.productive_days;
    document.getElementById('productivityRate').textContent = productivity.productivity_rate + '%';
    document.getElementById('productivityProgress').style.width = productivity.productivity_rate + '%';
    document.getElementById('avgProductiveHours').textContent = productivity.avg_productive_hours;

    // Work Distribution Percentages
    const totalWorkHours = overview.total_office_hours + overview.total_field_hours + overview.total_break_time;
    if (totalWorkHours > 0) {
        document.getElementById('officePercentage').textContent = 
            Math.round((overview.total_office_hours / totalWorkHours) * 100) + '%';
        document.getElementById('fieldPercentage').textContent = 
            Math.round((overview.total_field_hours / totalWorkHours) * 100) + '%';
        document.getElementById('breakPercentage').textContent = 
            Math.round((overview.total_break_time / totalWorkHours) * 100) + '%';
    }

    // Populate charts
    createWeeklyChart(data.weekly);
    createDistributionChart(overview);
    createDayOfWeekChart(data.patterns.day_of_week_stats);
    
    // Populate monthly table
    populateMonthlyTable(data.monthly);
    
    // Populate patterns
    populatePatterns(data.patterns, data.productivity);
}

function createWeeklyChart(weeklyData) {
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    
    if (weeklyChart) {
        weeklyChart.destroy();
    }
    
    weeklyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyData.map(week => week.week_start + ' - ' + week.week_end.split(',')[0]),
            datasets: [
                {
                    label: 'Total Hours',
                    data: weeklyData.map(week => week.total_hours),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Office Hours',
                    data: weeklyData.map(week => week.office_hours),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Field Hours',
                    data: weeklyData.map(week => week.field_hours),
                    borderColor: 'rgb(255, 206, 86)',
                    backgroundColor: 'rgba(255, 206, 86, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            }
        }
    });
}

function createDistributionChart(overview) {
    const ctx = document.getElementById('distributionChart').getContext('2d');
    
    if (distributionChart) {
        distributionChart.destroy();
    }
    
    distributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Office', 'Field', 'Break'],
            datasets: [{
                data: [overview.total_office_hours, overview.total_field_hours, overview.total_break_time],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function createDayOfWeekChart(dayOfWeekStats) {
    const ctx = document.getElementById('dayOfWeekChart').getContext('2d');
    
    if (dayOfWeekChart) {
        dayOfWeekChart.destroy();
    }
    
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const hours = days.map(day => dayOfWeekStats[day] ? dayOfWeekStats[day].avg_hours : 0);
    
    dayOfWeekChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days.map(day => day.substring(0, 3)),
            datasets: [{
                label: 'Average Hours',
                data: hours,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function populateMonthlyTable(monthlyData) {
    const tbody = document.getElementById('monthlyStatsBody');
    let html = '';
    
    monthlyData.forEach(month => {
        const totalCycles = month.cycles.office + month.cycles.field + month.cycles.break;
        html += `
            <tr>
                <td><strong>${month.month}</strong></td>
                <td>${month.days}</td>
                <td>${month.total_hours} hrs</td>
                <td>${month.office_hours} hrs</td>
                <td>${month.field_hours} hrs</td>
                <td>${month.avg_hours_per_day} hrs</td>
                <td>
                    <span class="badge bg-primary me-1">O:${month.cycles.office}</span>
                    <span class="badge bg-success me-1">F:${month.cycles.field}</span>
                    <span class="badge bg-warning">B:${month.cycles.break}</span>
                </td>
            </tr>
        `;
    });
    
    if (html === '') {
        html = '<tr><td colspan="7" class="text-center text-muted">No monthly data available</td></tr>';
    }
    
    tbody.innerHTML = html;
}

function populatePatterns(patterns, productivity) {
    // Most Active Hours
    const activeHoursHtml = patterns.most_active_hours.map(hour => {
        const displayHour = hour === 0 ? '12 AM' : 
                           hour < 12 ? hour + ' AM' : 
                           hour === 12 ? '12 PM' : 
                           (hour - 12) + ' PM';
        return `<span class="badge bg-info me-2">${displayHour}</span>`;
    }).join('');
    document.getElementById('mostActiveHours').innerHTML = activeHoursHtml;
    
    // Peak Performance Days
    const peakDaysHtml = productivity.top_peak_days.slice(0, 3).map(day => 
        `<div class="d-flex justify-content-between">
            <span>${day.date}</span>
            <span class="badge bg-success">${day.hours} hrs</span>
        </div>`
    ).join('');
    document.getElementById('peakDays').innerHTML = peakDaysHtml || '<p class="text-muted">No peak days data</p>';
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}
.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #117a8b);
}
.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #d39e00);
}
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endsection
