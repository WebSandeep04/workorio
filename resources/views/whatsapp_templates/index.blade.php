@extends('layouts.app')

@section('title', 'WhatsApp Templates')
@section('page_title', 'WhatsApp Templates')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-violet">
                <i class="bi bi-layout-text-window text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Templates</div>
                <div class="summary-card-value text-primary" id="summaryTotal">0</div>
            </div>
        </div>
        <div class="summary-card card-5">
            <div class="summary-card-icon icon-rose">
                <i class="bi bi-check-circle fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Approved Templates</div>
                <div class="summary-card-value text-success" id="summaryApproved">0</div>
            </div>
        </div>
        <div class="summary-card card-1">
            <div class="summary-card-icon icon-sunrise">
                <i class="bi bi-exclamation-circle fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Rejected / Pending</div>
                <div class="summary-card-value text-warning" id="summaryRejected">0</div>
            </div>
        </div>
    </div>

    <!-- Search & Add -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="searchTemplates" placeholder="Search templates...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Language</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="templatesTableBody">
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Loading templates...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center" style="background: #fff; border-top: 1px solid #f1f3f5; font-family: Montserrat;">
            <div id="templatesRangeInfo" style="font-size: 10px; color: #6c757d; font-weight: 500;">Showing 0 data</div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Preview Template Modal -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="previewModalTitle">Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex justify-content-center align-items-center rounded p-4" style="background-color: #efeae2; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); min-height: 400px;">
                    <div id="whatsapp_preview_container" style="width: 100%; max-width: 340px;">
                        <div class="whatsapp-bubble p-3" style="background-color: #dcf8c6; border-radius: 0px 8px 8px 8px; box-shadow: 0 1px 1px rgba(0,0,0,0.15);">
                            <div id="wa_preview_header" class="fw-bold mb-2 text-dark"></div>
                            <div id="wa_preview_body" class="mb-1 text-dark" style="font-size: 0.95rem; line-height: 1.4; white-space: pre-wrap; font-family: Helvetica, Arial, sans-serif;"></div>
                            <div id="wa_preview_footer" class="text-muted d-flex justify-content-end" style="font-size: 0.7rem; margin-top: 4px;"></div>
                        </div>
                        <div id="wa_preview_buttons" class="mt-2 d-flex flex-column gap-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Variables Modal -->
<div class="modal fade" id="mapVariablesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalTitle">Map Template Variables</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="mapVariablesForm">
                <input type="hidden" id="map_template_name" name="template_name">
                <div class="modal-body">
                    <div id="mapping_container">
                        <div class="text-center text-muted py-3">Loading variables...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" id="btn-save-mapping">Save Mapping</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Test Message Modal -->
<div class="modal fade" id="testMessageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="testMessageForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Template Name</label>
                        <input type="text" class="form-control" id="test_template_name" name="template_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Phone Number</label>
                        <input type="text" class="form-control" name="phone_number" placeholder="Enter 10 digit number" required pattern="\d{10,12}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-send-test">Send Test</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let currentPage = 1;
    let searchTimer;
    let allTemplates = [];

    $(document).ready(function() {
        loadTemplates(1);
    });

    $('#searchTemplates').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentPage = 1;
            loadTemplates(1);
        }, 500);
    });

    function loadTemplates(page) {
        let search = $('#searchTemplates').val();
        $('#templatesTableBody').html('<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Loading...</td></tr>');

        $.ajax({
            url: `{{ route('whatsapp-templates.fetch') }}?page=${page}`,
            type: 'POST',
            data: {
                search: search,
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.success) {
                    allTemplates = res.templates.data; // Store for preview
                    renderTemplates(res.templates.data);
                    renderPagination(res.templates.current_page, res.templates.last_page);
                    
                    let from = (res.templates.current_page - 1) * 10 + 1;
                    let to = Math.min(res.templates.current_page * 10, res.templates.total);
                    updateRangeInfo(from, to, res.templates.total);

                    // Update Summaries
                    $('#summaryTotal').text(res.summary.total);
                    $('#summaryApproved').text(res.summary.approved);
                    $('#summaryRejected').text(res.summary.rejected);
                } else {
                    $('#templatesTableBody').html(`<tr><td colspan="5" class="text-center text-danger py-4">${res.message}</td></tr>`);
                    updateRangeInfo(0, 0, 0);
                    $('#paginationLinks').html('');
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Error fetching data';
                $('#templatesTableBody').html(`<tr><td colspan="5" class="text-center text-danger py-4">${msg}</td></tr>`);
                updateRangeInfo(0, 0, 0);
                $('#paginationLinks').html('');
            }
        });
    }

    function renderTemplates(data) {
        let html = '';
        if (data.length === 0) {
            html = '<tr><td colspan="5" class="text-center py-4 text-muted">No templates found</td></tr>';
        } else {
            data.forEach((item, index) => {
                let statusBadge = '';
                let statusClass = '';
                if(item.status.toLowerCase() === 'approved') {
                    statusClass = 'bg-success bg-opacity-10 text-success';
                } else if(item.status.toLowerCase() === 'rejected') {
                    statusClass = 'bg-danger bg-opacity-10 text-danger';
                } else {
                    statusClass = 'bg-warning bg-opacity-10 text-warning';
                }
                statusBadge = `<span class="badge ${statusClass} rounded-pill px-3 py-2 border-0">${item.status}</span>`;

                html += `
                    <tr>
                        <td class="fw-bold text-dark">${item.name}</td>
                        <td class="text-muted">${item.category}</td>
                        <td class="text-muted">${item.language}</td>
                        <td>${statusBadge}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm text-white px-3 py-1 shadow-sm me-1" style="background-color: #434afa; border-radius: 4px; font-weight: 500;" onclick="showPreview(${index})" title="Show Preview">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                            <button type="button" class="btn btn-sm text-white px-3 py-1 shadow-sm me-1" style="background-color: #20c997; border-radius: 4px; font-weight: 500;" onclick="openMappingModal(${index})" title="Map Variables">
                                <i class="bi bi-diagram-3"></i> Map Variables
                            </button>
                            <button type="button" class="btn btn-sm text-white px-3 py-1 shadow-sm" style="background-color: #f39c12; border-radius: 4px; font-weight: 500;" onclick="openTestModal('${item.name}')" title="Test Template">
                                <i class="bi bi-send"></i> Test
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        $('#templatesTableBody').html(html);
    }

    function renderPagination(current, last) {
        let html = '';
        let $container = $('#paginationLinks');
        
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
            loadTemplates(page);
        }
    });

    function updateRangeInfo(from, to, total) {
        if (!from || !to) {
            $('#templatesRangeInfo').text('Showing 0 data');
            return;
        }
        $('#templatesRangeInfo').text(`Showing ${from}-${to} from ${total} data`);
    }

    function showPreview(index) {
        let template = allTemplates[index];
        if (!template) return;

        $('#previewModalTitle').text(template.name + ' - Preview');
        
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

        $('#previewTemplateModal').modal('show');
    }
    function openMappingModal(index) {
        let template = allTemplates[index];
        if (!template) return;
        
        $('#mapModalTitle').text('Map Variables - ' + template.name);
        $('#map_template_name').val(template.name);
        $('#mapping_container').html('<div class="text-center text-muted py-3">Loading variables...</div>');
        $('#mapVariablesModal').modal('show');

        // Fetch existing mappings
        $.ajax({
            url: `/whatsapp-templates/mapping/${template.name}`,
            type: 'GET',
            success: function(res) {
                renderMappingForm(template, res.mapping);
            },
            error: function() {
                renderMappingForm(template, null);
            }
        });
    }

    function renderMappingForm(template, existingMapping) {
        let container = $('#mapping_container');
        container.html('');

        let mappings = existingMapping ? existingMapping.mappings : {};
        let mediaUrls = existingMapping ? existingMapping.media_urls : {};

        if (template.variables && template.variables.length > 0) {
            let html = '';
            template.variables.forEach(variable => {
                if (variable.startsWith('header_')) {
                    let existingImg = mediaUrls && mediaUrls[variable] ? `<div class="mt-2"><a href="${mediaUrls[variable]}" target="_blank" class="small text-primary">View current media</a></div>` : '';
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">${variable} (Image/Document)</label>
                            <input type="file" class="form-control" name="media[${variable}]" accept="image/*,application/pdf">
                            ${existingImg}
                        </div>
                    `;
                } else {
                    let mapValue = mappings && mappings[variable] ? mappings[variable] : '';
                    let isName = mapValue === 'name' ? 'selected' : '';
                    let isPhone = mapValue === 'phone_number' ? 'selected' : '';
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">${variable}</label>
                            <select class="form-select" name="mappings[${variable}]" required>
                                <option value="">-- Map to field --</option>
                                <option value="name" ${isName}>Name</option>
                                <option value="phone_number" ${isPhone}>Phone Number</option>
                            </select>
                        </div>
                    `;
                }
            });
            container.html(html);
        } else {
            container.html('<div class="alert alert-info border-0 shadow-sm">This template does not require any dynamic variables to be mapped.</div>');
        }
    }

    $('#mapVariablesForm').submit(function(e) {
        e.preventDefault();
        $('#btn-save-mapping').prop('disabled', true).text('Saving...');
        
        let formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');
        
        let mappingObj = {};
        $(this).serializeArray().forEach(item => {
            if (item.name.startsWith('mappings[')) {
                let key = item.name.match(/\[(.*?)\]/)[1];
                mappingObj[key] = item.value;
            }
        });
        formData.set('mappings', JSON.stringify(mappingObj));

        $.ajax({
            url: `{{ route('whatsapp-templates.mapping.store') }}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#mapVariablesModal').modal('hide');
                alert(res.message);
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Error saving mapping.';
                alert(msg);
            },
            complete: function() {
                $('#btn-save-mapping').prop('disabled', false).text('Save Mapping');
            }
        });
    });

    function openTestModal(templateName) {
        $('#test_template_name').val(templateName);
        $('#testMessageForm')[0].reset();
        $('#test_template_name').val(templateName); // Restore after reset
        $('#testMessageModal').modal('show');
    }

    $('#testMessageForm').submit(function(e) {
        e.preventDefault();
        let btn = $('#btn-send-test');
        btn.prop('disabled', true).text('Sending...');

        let formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: `{{ route('whatsapp-templates.test') }}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#testMessageModal').modal('hide');
                alert(res.message);
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Error sending test message.';
                alert(msg);
            },
            complete: function() {
                btn.prop('disabled', false).text('Send Test');
            }
        });
    });
</script>
@endpush
