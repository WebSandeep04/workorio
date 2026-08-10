@extends('layouts.app')

@section('title', 'WhatsApp Campaigns')
@section('page_title', 'WhatsApp Campaigns')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.6rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .metric-arrow {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    position: absolute;
    right: 6px;
    bottom: 6px;
    font-size: 0.8rem;
  }

  .metric-arrow:hover {
    background: #5b59f7;
    color: #fff;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .summary-card-icon img, .summary-card-icon i {
    width: 20px;
    height: 20px;
    object-fit: contain;
    font-size: 1.25rem;
  }

  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); color: white; }
  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); color: white; }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #000;
    font-family: Montserrat;
  }

  .data-table-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    box-shadow: 0px 4px 4px 0px #0000000A;
    overflow: hidden;
  }

  .data-table-card .table-responsive {
    max-height: calc(100vh - 250px);
    overflow-y: auto;
    overflow-x: auto;
  }

  .data-table-card .custom-table {
    margin-bottom: 0;
    font-family: Montserrat;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .data-table-card .custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .table-search-field {
    flex: 1;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f4f5f7;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    padding: 0.35rem 0.9rem;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434afa;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
  }

  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }

  .status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
  }
  .status-draft { background: #ffe0b2; color: #e65100; }
  .status-active { background: #c8e6c9; color: #2e7d32; }
  .status-completed { background: #bbdefb; color: #1565c0; }

  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
  }

  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }

  .action-btn {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px;
    transition: all 0.2s;
  }

  .empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-violet">
                <i class="bi bi-megaphone fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Campaigns</div>
                <div class="summary-card-value text-dark" id="summary-total">0</div>
            </div>
            <a href="#" class="metric-arrow">
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="summary-card card-5">
            <div class="summary-card-icon icon-rose">
                <i class="bi bi-check-circle fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Active Campaigns</div>
                <div class="summary-card-value text-danger" id="summary-active">0</div>
            </div>
        </div>
        <div class="summary-card card-1">
            <div class="summary-card-icon icon-sunrise">
                <i class="bi bi-pencil-square fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Draft Campaigns</div>
                <div class="summary-card-value text-primary" id="summary-draft">0</div>
            </div>
        </div>
    </div>

    <!-- Search & Add (Separate Row) -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="filter_search" placeholder="Search campaigns..." />
        </div>
        <button type="button" class="table-search-btn" onclick="openCreateModal()">
            <i class="bi bi-plus me-1"></i>Add
        </button>
    </div>

    <!-- Data Table -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table" id="campaigns_table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Members Count</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="text-center py-4">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center" style="background: #fff; border-top: 1px solid #f1f3f5; font-family: Montserrat;">
            <div id="campaignsRangeInfo" style="font-size: 10px; color: #6c757d; font-weight: 500;"></div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Create Campaign Modal -->
<div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Campaign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCampaignForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="create_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-create">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Campaign Modal -->
<div class="modal fade" id="editCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Campaign</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCampaignForm">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="Draft">Draft</option>
                            <option value="Active">Active</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-update">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;

    $(document).ready(function() {
        loadCampaigns(currentPage);
    });

    function loadCampaigns(page = 1) {
        $.ajax({
            url: `{{ route('whatsapp-campaigns.fetch') }}?page=${page}`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                let data = response.campaigns.data;
                let html = '';

                // Update summary cards
                $('#summary-total').text(response.summary.total);
                $('#summary-active').text(response.summary.active);
                $('#summary-draft').text(response.summary.draft);

                if (data.length === 0) {
                    html = `<tr><td colspan="6" class="empty-state"><i class="bi bi-inbox fs-1"></i><p>No campaigns found.</p></td></tr>`;
                } else {
                    data.forEach(campaign => {
                        let statusClass = 'status-draft';
                        if(campaign.status === 'Active') statusClass = 'status-active';
                        if(campaign.status === 'Completed') statusClass = 'status-completed';

                        let date = new Date(campaign.created_at).toLocaleDateString('en-GB');

                        html += `
                            <tr>
                                <td>${campaign.id}</td>
                                <td><strong>${campaign.name}</strong></td>
                                <td>${campaign.members_count} members</td>
                                <td><span class="status-badge ${statusClass}">${campaign.status}</span></td>
                                <td>${date}</td>
                                <td class="text-center">
                                    <a href="/whatsapp-campaigns/${campaign.id}" class="btn btn-sm btn-info action-btn text-white me-1" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-primary action-btn me-1" onclick="openEditModal(${campaign.id}, '${campaign.name.replace(/'/g, "\\'")}', '${campaign.status}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger action-btn" onclick="deleteCampaign(${campaign.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#campaigns_table tbody').html(html);

                buildSimplePagination($('#paginationLinks'), response.campaigns.current_page, response.campaigns.last_page);
                updateRangeInfo(response.campaigns.from, response.campaigns.to, response.campaigns.total);
            },
            error: function() {
                $('#campaigns_table tbody').html(`<tr><td colspan="6" class="text-center text-danger py-4">Error loading data.</td></tr>`);
            }
        });
    }

    function openCreateModal() {
        $('#createCampaignForm')[0].reset();
        $('#createCampaignModal').modal('show');
    }

    function openEditModal(id, name, status) {
        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_status').val(status);
        $('#editCampaignModal').modal('show');
    }

    $('#createCampaignForm').submit(function(e) {
        e.preventDefault();
        $('#btn-create').prop('disabled', true).text('Creating...');
        
        $.ajax({
            url: `{{ route('whatsapp-campaigns.store') }}`,
            type: 'POST',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                $('#createCampaignModal').modal('hide');
                loadCampaigns(currentPage);
                alert(res.message);
            },
            error: function(xhr) {
                alert('Error creating campaign.');
            },
            complete: function() {
                $('#btn-create').prop('disabled', false).text('Create Campaign');
            }
        });
    });

    $('#editCampaignForm').submit(function(e) {
        e.preventDefault();
        $('#btn-update').prop('disabled', true).text('Saving...');
        let id = $('#edit_id').val();
        
        $.ajax({
            url: `/whatsapp-campaigns/${id}`,
            type: 'PUT',
            data: $(this).serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                $('#editCampaignModal').modal('hide');
                loadCampaigns(currentPage);
                alert(res.message);
            },
            error: function(xhr) {
                alert('Error updating campaign.');
            },
            complete: function() {
                $('#btn-update').prop('disabled', false).text('Save Changes');
            }
        });
    });

    function deleteCampaign(id) {
        if(confirm('Are you sure you want to delete this campaign?')) {
            $.ajax({
                url: `/whatsapp-campaigns/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    loadCampaigns(currentPage);
                    alert(res.message);
                },
                error: function() {
                    alert('Error deleting campaign.');
                }
            });
        }
    }

    function buildSimplePagination($container, current, last) {
        let html = '';
        if (last > 1) {
            html += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current - 1}">&laquo;</a>
            </li>`;
            
            let start = Math.max(1, current - 2);
            let end = Math.min(last, current + 2);
            
            for (let i = start; i <= end; i++) {
                html += `<li class="page-item ${i === current ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }
            
            html += `<li class="page-item ${current === last ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${current + 1}">&raquo;</a>
            </li>`;
        }
        $container.html(html);
    }

    $(document).on('click', '#paginationLinks .page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page && page !== currentPage) {
            currentPage = page;
            loadCampaigns(page);
        }
    });

    function updateRangeInfo(from, to, total) {
        if (!from || !to) {
            $('#campaignsRangeInfo').text('Showing 0 data');
            return;
        }
        $('#campaignsRangeInfo').text(`Showing ${from}-${to} from ${total} data`);
    }
</script>
@endpush
