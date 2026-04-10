@extends('layouts.app')

@section('title', 'Calling Analytics')
@section('page_title', 'Calling Analytics Dashboard')

@section('content')
<div class="container-fluid calling-analytics">
    <!-- Hero Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #434AFA 0%, #667eea 100%); color: white; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1" style="font-family: Montserrat;">Performance Overview</h4>
                            <p class="mb-0 opacity-75">Visualizing your calling activities and lead conversion trends</p>
                        </div>
                        <div class="text-end">
                            <span class="d-block small opacity-75">Total Leads Managed</span>
                            <h2 class="fw-bold mb-0" id="statLeads">0</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Activity Trend -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0" style="font-family: Montserrat;">Acquisition Trend</h6>
                    <small class="text-muted">Count of leads processed over the last 30 days</small>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="callingTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold text-dark mb-0" style="font-family: Montserrat;">Status Distribution</h6>
                    <small class="text-muted">Breakdown by current lead stage</small>
                </div>
                <div class="card-body p-4">
                    <div style="height: 300px;">
                        <canvas id="callingDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-3">
                <a href="{{ route('calling.mycalling') }}" class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 600; font-family: Montserrat;">
                    <i class="bi bi-telephone me-2"></i> Start Calling Now
                </a>
                <a href="{{ route('calling.index') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600; font-family: Montserrat;">
                    <i class="bi bi-layers me-2"></i> Master Lead Pool
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calling-analytics { padding: 1rem; background: #f8f9fc; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadAnalyticsData();
});

function loadAnalyticsData() {
    $.get('{{ route("calling.analytics.data") }}', function(resp) {
        $('#statLeads').text(resp.user_info.total_leads);
        renderCharts(resp);
    });
}

function renderCharts(data) {
    // Trend Chart
    const trendCtx = document.getElementById('callingTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: data.trends.map(t => t.date),
            datasets: [{
                label: 'Leads Processed',
                data: data.trends.map(t => t.count),
                borderColor: '#434AFA',
                backgroundColor: 'rgba(67, 74, 250, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Distribution Chart
    const distCtx = document.getElementById('callingDistChart').getContext('2d');
    const distLabels = data.distribution.map(d => d.name || 'Unassigned');
    const distValues = data.distribution.map(d => d.count);
    
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: distLabels,
            datasets: [{
                data: distValues,
                backgroundColor: [
                    '#434AFA', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#0ea5e9'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Montserrat' } } }
            }
        }
    });
}
</script>
@endpush
