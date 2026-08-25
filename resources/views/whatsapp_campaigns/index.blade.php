@extends('layouts.app')

@section('title', 'WhatsApp Campaigns')
@section('page_title', 'WhatsApp Campaigns')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
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
                            <th>Total Members</th>
                            <th>Sent</th>
                            <th>Failed</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center py-4">Loading...</td>
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

<!-- Send Campaign Modal -->
<div class="modal fade" id="sendCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Campaign via MSG91</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendCampaignForm">
                <input type="hidden" id="send_campaign_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send_template" class="form-label">Select MSG91 Template <span class="text-danger">*</span></label>
                                <select class="form-select" id="send_template" name="template_name" required>
                                    <option value="">Loading templates...</option>
                                </select>
                            </div>
                            <div id="dynamic_variables_container"></div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center align-items-center rounded p-4" style="background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
                            <div id="whatsapp_preview_container" style="width: 100%; max-width: 340px; display: none;">
                                <div class="whatsapp-bubble p-3" style="background-color: #dcf8c6; border-radius: 0px 8px 8px 8px; box-shadow: 0 1px 1px rgba(0,0,0,0.15);">
                                    <div id="wa_preview_header" class="fw-bold mb-2 text-dark"></div>
                                    <div id="wa_preview_body" class="mb-1 text-dark" style="font-size: 0.95rem; line-height: 1.4; white-space: pre-wrap; font-family: Helvetica, Arial, sans-serif;"></div>
                                    <div id="wa_preview_footer" class="text-muted d-flex justify-content-end" style="font-size: 0.7rem; margin-top: 4px;"></div>
                                </div>
                                <div id="wa_preview_buttons" class="mt-2 d-flex flex-column gap-1"></div>
                            </div>
                            <div id="whatsapp_preview_placeholder" class="text-muted text-center w-100 bg-white p-4 rounded shadow-sm opacity-75">
                                <i class="bi bi-whatsapp fs-1 text-success"></i>
                                <p class="mt-2 fw-bold">Select a template to see preview</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="btn-send">Send Now</button>
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
                    html = `<tr><td colspan="8" class="empty-state"><i class="bi bi-inbox fs-1"></i><p>No campaigns found.</p></td></tr>`;
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
                                <td><b>${campaign.members_count || 0}</b></td>
                                <td class="text-success"><b>${campaign.sent_count || 0}</b></td>
                                <td class="text-danger"><b>${campaign.failed_count || 0}</b></td>
                                <td><span class="status-badge ${statusClass}">${campaign.status}</span></td>
                                <td>${date}</td>
                                <td class="text-center">
                                    <a href="/whatsapp-campaigns/${campaign.id}" class="btn btn-sm btn-info action-btn text-white me-1" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-primary action-btn me-1" onclick="openEditModal(${campaign.id}, '${campaign.name.replace(/'/g, "\\'")}', '${campaign.status}')" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success action-btn me-1" onclick="openSendModal(${campaign.id})" title="Send">
                                        <i class="bi bi-send"></i>
                                    </button>
                                    <a href="/whatsapp-campaigns/${campaign.id}/report-view" class="btn btn-sm text-white action-btn me-1" style="background-color: #20c997;" title="View Report">
                                        <i class="bi bi-card-list"></i>
                                    </a>
                                    ${campaign.status === 'Draft' ? `
                                    <button class="btn btn-sm btn-danger action-btn" onclick="deleteCampaign(${campaign.id})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    ` : ''}
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
                $('#campaigns_table tbody').html(`<tr><td colspan="8" class="text-center text-danger py-4">Error loading data.</td></tr>`);
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

    let msg91Templates = [];

    function openSendModal(id) {
        $('#send_campaign_id').val(id);
        $('#send_template').html('<option value="">Loading templates...</option>');
        $('#dynamic_variables_container').html('');
        $('#sendCampaignModal').modal('show');
        
        $.ajax({
            url: `{{ route('whatsapp-campaigns.fetch-msg91-templates') }}`,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    msg91Templates = res.data;
                    let html = '<option value="">-- Select Template --</option>';
                    res.data.forEach(t => {
                        html += `<option value="${t.name}">${t.name}</option>`;
                    });
                    $('#send_template').html(html);
                } else {
                    $('#send_template').html(`<option value="">Error: ${res.message}</option>`);
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Failed to load templates. Ensure MSG91 Settings are configured.';
                $('#send_template').html(`<option value="">Error: ${msg}</option>`);
            }
        });
    }

    $(document).on('change', '#send_template', function() {
        let selectedTemplateName = $(this).val();
        let container = $('#dynamic_variables_container');
        let previewContainer = $('#whatsapp_preview_container');
        let previewPlaceholder = $('#whatsapp_preview_placeholder');
        container.html(''); // clear

        if (!selectedTemplateName) {
            previewContainer.hide();
            previewPlaceholder.show();
            return;
        }

        let template = msg91Templates.find(t => t.name === selectedTemplateName);
        if (template) {
            // Render Preview
            previewPlaceholder.hide();
            previewContainer.show();
            
            $('#wa_preview_header, #wa_preview_body, #wa_preview_footer, #wa_preview_buttons').html('');

            if (template.components && template.components.length > 0) {
                template.components.forEach(comp => {
                    if (comp.type === 'HEADER') {
                        if (comp.format === 'TEXT') {
                            $('#wa_preview_header').text(comp.text);
                        } else {
                            let icon = comp.format === 'DOCUMENT' ? 'bi-file-earmark-pdf' : (comp.format === 'VIDEO' ? 'bi-play-btn' : 'bi-image');
                            $('#wa_preview_header').html(`<div class="bg-secondary bg-opacity-25 rounded d-flex justify-content-center align-items-center mb-2" style="height: 100px;"><i class="bi ${icon} fs-1 text-secondary"></i></div>`);
                        }
                    } else if (comp.type === 'BODY') {
                        let text = comp.text || '';
                        // Highlight variables, avoid Blade curly braces syntax error
                        text = text.replace(/\{\{(\d+)\}\}/g, '<span class="bg-warning text-dark px-1 rounded mx-1">@{{$1}}</span>');
                        $('#wa_preview_body').html(text);
                    } else if (comp.type === 'FOOTER') {
                        $('#wa_preview_footer').text(comp.text);
                    } else if (comp.type === 'BUTTONS' && comp.buttons) {
                        let buttonsHtml = '';
                        comp.buttons.forEach(btn => {
                            let icon = btn.type === 'URL' ? 'bi-box-arrow-up-right' : (btn.type === 'PHONE_NUMBER' ? 'bi-telephone' : 'bi-reply');
                            buttonsHtml += `<button type="button" class="btn btn-light btn-sm w-100 text-primary border shadow-sm fw-bold"><i class="bi ${icon} me-1"></i> ${btn.text}</button>`;
                        });
                        $('#wa_preview_buttons').html(buttonsHtml);
                    }
                });
            } else {
                $('#wa_preview_body').html('<pre class="small">' + JSON.stringify(template, null, 2) + '</pre>');
            }
        }
    });

    $('#sendCampaignForm').submit(function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to send this campaign? This action cannot be undone.')) return;

        $('#btn-send').prop('disabled', true).text('Sending...');
        let id = $('#send_campaign_id').val();
        
        let formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: `/whatsapp-campaigns/${id}/send`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#sendCampaignModal').modal('hide');
                loadCampaigns(currentPage);
                alert(res.message);
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Error sending campaign.';
                alert(msg);
            },
            complete: function() {
                $('#btn-send').prop('disabled', false).text('Send Now');
            }
        });
    });


</script>
@endpush
