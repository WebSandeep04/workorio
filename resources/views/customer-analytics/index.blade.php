@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-graph-up me-3 fs-4"></i>
                        <h4 class="card-title mb-0 text-white">Customer Analytics Dashboard</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Customer Selection -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="customerSelect" class="form-label fw-bold">
                                <i class="bi bi-person-badge me-2"></i>Select Customer
                            </label>
                            <select class="form-select form-select-lg border-2" id="customerSelect">
                                <option value="">Choose a customer...</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary btn-lg px-4" id="loadAnalyticsBtn">
                                <i class="bi bi-chart-bar me-2"></i> Load Analytics
                            </button>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div id="customerInfo" class="row mb-4" style="display: none;">
                        <div class="col-12">
                            <div class="card border-0 bg-gradient-primary text-white shadow">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-info-circle me-2"></i>Customer Information
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2 fs-4"></i>
                                                <div>
                                                    <small class="text-light">Name</small>
                                                    <div class="fw-bold" id="customerName"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building me-2 fs-4"></i>
                                                <div>
                                                    <small class="text-light">Company</small>
                                                    <div class="fw-bold" id="customerCompany"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-envelope me-2 fs-4"></i>
                                                <div>
                                                    <small class="text-light">Email</small>
                                                    <div class="fw-bold" id="customerEmail"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-telephone me-2 fs-4"></i>
                                                <div>
                                                    <small class="text-light">Phone</small>
                                                    <div class="fw-bold" id="customerPhone"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Dashboard -->
                    <div id="statisticsDashboard" class="mb-4 ms-2" style="display: none;">
                        <div class="row g-2">
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-primary text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="totalLeads">0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-people me-1"></i>Total Leads
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-success text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="closeWinLeads">0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-trophy me-1"></i>Close Win
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-danger text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="closeLostLeads">0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-x-circle me-1"></i>Close Lost
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-warning text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="activeLeads">0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-activity me-1"></i>Active Leads
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-info text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="conversionRate">0%</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-percent me-1"></i>Conversion Rate
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-secondary text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="recentLeads">0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-calendar-event me-1"></i>Recent (30 days)
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-dark text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="totalTicketValue">₹0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-currency-rupee me-1"></i>Total Value
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="card border-0 bg-gradient-success text-white shadow h-100" style="width: 140px;">
                                    <div class="card-body text-center p-3">
                                        <div class="h4 mb-2" id="closeWinValue">₹0</div>
                                        <p class="mb-0 small fw-bold">
                                            <i class="bi bi-cash-stack me-1"></i>Won Value
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div id="chartsSection" class="row mb-4" style="display: none;">
                        <div class="col-md-6">
                            <div class="card border-0 shadow h-100">
                                <div class="card-header bg-white border-0 d-flex align-items-center">
                                    <i class="bi bi-pie-chart me-2 text-primary"></i>
                                    <h6 class="mb-0 fw-bold">Leads by Status</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow h-100">
                                <div class="card-header bg-white border-0 d-flex align-items-center">
                                    <i class="bi bi-bar-chart me-2 text-success"></i>
                                    <h6 class="mb-0 fw-bold">Leads by Month (Last 6 months)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Leads Table -->
                    <div id="leadsTable" class="row" style="display: none;">
                        <div class="col-12">
                            <div class="card border-0 shadow">
                                <div class="card-header bg-light border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="bi bi-table me-2 text-primary"></i>Detailed Leads Information
                                        </h5>
                                        <div class="d-flex align-items-center">
                                            <div class="input-group" style="width: 300px;">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-search text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0" id="searchLead" placeholder="Search leads by name, contact, email...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="leadsDataTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0">
                                                        <i class="bi bi-person me-2 text-primary"></i>Lead Name
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-person-badge me-2 text-primary"></i>Contact Person
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-telephone me-2 text-primary"></i>Contact Number
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-envelope me-2 text-primary"></i>Email
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-flag me-2 text-primary"></i>Status
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-calendar-plus me-2 text-primary"></i>Created Date
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-calendar-check me-2 text-primary"></i>Next Follow-up
                                                    </th>
                                                    <th class="border-0">
                                                        <i class="bi bi-currency-rupee me-2 text-primary"></i>Ticket Value
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="leadsTableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="paginationLinks" class="d-flex justify-content-center p-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #343a40 0%, #212529 100%);
}

.badge {
    font-size: 0.8em;
    padding: 0.5em 0.8em;
    border-radius: 20px;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.page-link {
    margin: 0 3px;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    text-decoration: none;
    color: #434afa;
    font-weight: 500;
    transition: all 0.2s ease;
}

.page-link.active {
    background-color: #434afa;
    color: white;
    border-color: #434afa;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
}

.page-link:hover {
    background-color: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    text-decoration: none;
    transform: translateY(-1px);
}

.form-select-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

.display-6 {
    font-size: 2.5rem;
    font-weight: 700;
}

.input-group-text {
    border-color: #dee2e6;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075) !important;
}

.shadow {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let currentCustomerId = null;
let statusChart = null;
let monthChart = null;

// Load customers when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
    
    // Add event listener for load analytics button
    document.getElementById('loadAnalyticsBtn').addEventListener('click', loadCustomerAnalytics);
    
    // Search functionality
    document.getElementById('searchLead').addEventListener('input', function() {
        if (currentCustomerId) loadFilteredLeads();
    });
});

function loadCustomers() {
    fetch('{{ route("customer-analytics.get-customers") }}')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('customerSelect');
            select.innerHTML = '<option value="">Choose a customer...</option>';
            data.forEach(function(customer) {
                const option = document.createElement('option');
                option.value = customer.id;
                option.textContent = `${customer.name} - ${customer.company_name}`;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Failed to load customers:', error);
            alert('Failed to load customers.');
        });
}

function loadCustomerAnalytics() {
    const customerId = document.getElementById('customerSelect').value;
    if (!customerId) {
        alert('Please select a customer first.');
        return;
    }

    currentCustomerId = customerId;
    
    fetch(`/customer-analytics/${customerId}`)
        .then(response => response.json())
        .then(data => {
            displayCustomerInfo(data.customer);
            displayStatistics(data.statistics);
            displayCharts(data.leads_by_status, data.leads_by_month);
            displayLeadsTable(data.detailed_leads);
        })
        .catch(error => {
            console.error('Failed to load customer analytics:', error);
            alert('Failed to load customer analytics.');
        });
}

function displayCustomerInfo(customer) {
    document.getElementById('customerName').textContent = customer.name;
    document.getElementById('customerCompany').textContent = customer.company_name;
    document.getElementById('customerEmail').textContent = customer.email;
    document.getElementById('customerPhone').textContent = customer.phone;
    document.getElementById('customerInfo').style.display = 'block';
}

function displayStatistics(stats) {
    document.getElementById('totalLeads').textContent = stats.total_leads;
    document.getElementById('closeWinLeads').textContent = stats.close_win_leads;
    document.getElementById('closeLostLeads').textContent = stats.close_lost_leads;
    document.getElementById('activeLeads').textContent = stats.active_leads;
    document.getElementById('conversionRate').textContent = stats.conversion_rate + '%';
    document.getElementById('recentLeads').textContent = stats.recent_leads;
    document.getElementById('totalTicketValue').textContent = '₹' + stats.total_ticket_value.toLocaleString();
    document.getElementById('closeWinValue').textContent = '₹' + stats.close_win_ticket_value.toLocaleString();
    
    document.getElementById('statisticsDashboard').style.display = 'block';
    document.getElementById('chartsSection').style.display = 'flex';
}

function displayCharts(statusData, monthData) {
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    if (statusChart) statusChart.destroy();
    
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6c757d', '#6f42c1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Month Chart
    const monthCtx = document.getElementById('monthlyChart').getContext('2d');
    if (monthChart) monthChart.destroy();
    
    monthChart = new Chart(monthCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(monthData),
            datasets: [{
                label: 'Leads',
                data: Object.values(monthData),
                backgroundColor: '#007bff',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
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

function displayLeadsTable(leads) {
    const tbody = document.getElementById('leadsTableBody');
    tbody.innerHTML = '';

    leads.forEach(function(lead) {
        // Handle missing or undefined status data
        const statusName = lead.status_name || 'Unknown';
        const statusClass = lead.is_close_win ? 'badge bg-success' : 
                           lead.is_close_lost ? 'badge bg-danger' : 'badge bg-warning';
        
        // Handle missing or undefined ticket value
        const ticketValue = lead.ticket_value || 0;
        
        // Handle missing or undefined dates
        const createdDate = lead.created_date || 'N/A';
        const nextFollowUp = lead.next_follow_up || 'N/A';
        
        const row = tbody.insertRow();
        row.innerHTML = `
            <td class="align-middle">
                <div class="fw-bold">${lead.leads_name || 'N/A'}</div>
            </td>
            <td class="align-middle">${lead.contact_person || 'N/A'}</td>
            <td class="align-middle">${lead.contact_number || 'N/A'}</td>
            <td class="align-middle">${lead.email || 'N/A'}</td>
            <td class="align-middle">
                <span class="${statusClass}">${statusName}</span>
            </td>
            <td class="align-middle">${createdDate}</td>
            <td class="align-middle">${nextFollowUp}</td>
            <td class="align-middle">
                <span class="fw-bold text-success">₹${ticketValue.toLocaleString()}</span>
            </td>
        `;
    });

    document.getElementById('leadsTable').style.display = 'block';
}

function loadFilteredLeads(page = 1) {
    const searchTerm = document.getElementById('searchLead').value;
    
    fetch(`/customer-analytics/${currentCustomerId}/leads?search=${encodeURIComponent(searchTerm)}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            displayLeadsTable(data.data);
            displayPagination(data);
        })
        .catch(error => {
            console.error('Failed to load filtered leads:', error);
            alert('Failed to load filtered leads.');
        });
}

function displayPagination(data) {
    let links = '';
    if (data.prev_page_url) {
        links += `<a href="#" class="page-link" data-page="${data.current_page - 1}">Previous</a> `;
    }
    
    for (let i = 1; i <= data.last_page; i++) {
        const activeClass = i === data.current_page ? 'active' : '';
        links += `<a href="#" class="page-link ${activeClass}" data-page="${i}">${i}</a> `;
    }
    
    if (data.next_page_url) {
        links += `<a href="#" class="page-link" data-page="${data.current_page + 1}">Next</a>`;
    }
    
    document.getElementById('paginationLinks').innerHTML = links;
}

// Pagination click handler
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('page-link')) {
        e.preventDefault();
        const page = e.target.dataset.page;
        if (page && currentCustomerId) {
            loadFilteredLeads(parseInt(page));
        }
    }
});
</script>
@endpush
