@extends('layouts.app')

@section('title', 'Campaign Master')
@section('page_title', 'Campaign Management')

@push('styles')
<style>
    .calling-page { padding: 0.5rem; background: #f7f8fc; }

    /* Hero Metrics */
    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; }
    .hero-metric-card {
        background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.75rem 1rem; width: 100%;
        box-shadow: 0px 4px 4px 0px #0000000A; display: flex; align-items: center; gap: 0.75rem; transition: all 0.3s ease;
    }
    .hero-metric-card:hover { transform: translateY(-2px); box-shadow: 0px 8px 8px 0px #0000000A; }
    .hero-metric-icon { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hero-metric-icon img { width: 24px; height: 24px; object-fit: contain; }
    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

    .metric-label { display: block; font-size: 0.65rem; color: #000; letter-spacing: 0.05em; margin-bottom: 0.2rem; font-weight: 600; font-family: Montserrat; }
    .metric-value { font-size: 1.2rem; font-weight: 700; color: #101828; font-family: Montserrat; }

    /* Filter Box */
    .filterBox {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 0.5rem;
        background: #434AFA; padding: 0.75rem; border-radius: 5px; color: #fff; border: 1px solid #434AFA;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); margin-bottom: 1rem; font-family: Montserrat;
    }
    .form-label-modern { color: #fff; font-size: 10px; font-weight: 600; margin-bottom: 0.25rem; font-family: Montserrat; }
    .form-control-modern { border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 6px; padding: 0.35rem 0.5rem; background: #fff; color: #000; font-size: 10px; font-family: Montserrat; width: 100%; }
    .filter-reset-btn { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 6px; background: rgba(255, 255, 255, 0.18); color: white; padding: 0.35rem 0.5rem; font-weight: 600; font-size: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }

    /* Search Section */
    .table-search-field {
        width: 100%; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; margin-bottom: 1rem;
    }
    .table-search-field i { color: #9ca3af; }
    .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; font-family: Montserrat; }

    /* Table System */
    .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; margin-bottom: 1rem; }
    .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
    .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-family: Montserrat; }
    .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #1f2937; border-bottom: 1px solid #f4f4f6; white-space: nowrap; }
    .custom-table tbody tr:hover { background: #f8f9ff; transform: translateY(-1px); }

    .chip-btn { border-radius: 4px; border: none; padding: 0.4rem 0.8rem; font-size: 0.7rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s; font-family: Montserrat; }
    .chip-btn.primary { background: linear-gradient(135deg, #434AFA, #667eea); color: #fff; box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); }
    .chip-btn.primary:hover:not(:disabled) { transform: translateY(-1px); }
    .chip-btn.ghost { background: #f3f5ff; color: #434afa; }

    .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-family: Montserrat; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
    .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; font-family: Montserrat; }

    /* Selection Command Center */
    .selection-command-bar {
        background: #fff; border-radius: 12px; border: 1px solid #e0e0e0; padding: 0.75rem 1.25rem;
        margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); z-index: 100; font-family: Montserrat;
    }
    .selection-badge {
        background: #434AFA; color: #fff; font-weight: 800; font-size: 11px; padding: 0.35rem 0.85rem;
        border-radius: 40px; letter-spacing: 0.05em; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    }
    .selection-action-btn {
        border-radius: 8px; border: none; padding: 0.5rem 1rem; font-size: 11px; font-weight: 700;
        display: inline-flex; align-items: center; transition: all 0.2s; font-family: Montserrat;
    }
    .selection-action-btn.primary { background: #f0f7ff; color: #007bff; }
    .selection-action-btn.primary:hover { background: #007bff; color: white; }
    .selection-action-btn.ghost { background: #fff5f5; color: #e53e3e; }
    .selection-action-btn.ghost:hover { background: #e53e3e; color: white; }
    .bg-soft-primary { background-color: #f0f4ff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 calling-page">
    <div id="alertContainer"></div>

    <!-- Hero Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="{{ asset('img/icons/call.png') }}" alt="Total Records">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Records</span>
                <span class="metric-value" id="totalCallings">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="{{ asset('img/icons/tick.png') }}" alt="Selected">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Selected Count</span>
                <span class="metric-value" id="heroSelected">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="{{ asset('img/icons/underprocess.png') }}" alt="Active Filters">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Active Filters</span>
                <span class="metric-value" id="activeFilters">0</span>
            </div>
        </div>
    </div>

    <div class="filterBox">
        <div>
            <label for="filter_campaign" class="form-label-modern"><i class="bi bi-megaphone"></i> Campaign View</label>
            <select id="filter_campaign" class="form-control-modern">
                <option value="">Master Lead Pool</option>
            </select>
        </div>
        <div>
            <label for="filter_list" class="form-label-modern"><i class="bi bi-list-task"></i> Lead List</label>
            <select id="filter_list" class="form-control-modern">
                <option value="">All Lists</option>
            </select>
        </div>
        <div>
            <label for="filter_state" class="form-label-modern"><i class="bi bi-geo-alt"></i> State</label>
            <select id="filter_state" class="form-control-modern"><option value="">All States</option></select>
        </div>
        <div>
            <label for="filter_city" class="form-label-modern"><i class="bi bi-buildings"></i> City</label>
            <select id="filter_city" class="form-control-modern"><option value="">All Cities</option></select>
        </div>
        <div>
            <label class="form-label-modern" style="visibility: hidden;">Reset</label>
            <button id="resetFilters" class="filter-reset-btn w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="filter_name" placeholder="Search by name, contact, or company..." />
    </div>

    <!-- Selection Command Center (Robust Pattern) -->
    <div id="selectionCommandCenter" class="selection-command-bar shadow-sm animate__animated animate__fadeInDown" style="display: none;">
        <div class="d-flex align-items-center gap-3">
            <div class="selection-badge">
                <span id="selectionCountText">0</span> SELECTED
            </div>
            <div id="globalSelectionStatus" class="badge bg-soft-primary text-primary border border-primary px-3 py-2" style="display: none; border-radius: 20px; font-weight: 600;">
                <i class="bi bi-globe me-1"></i> Global Selection
            </div>
        </div>
        <div class="d-flex gap-2">
            <button id="selectAllMatchingBtn" class="selection-action-btn primary">
                <i class="bi bi-lightning-fill me-1"></i> Select All Matching
            </button>
            <button id="clearSelectionBtn" class="selection-action-btn ghost">
                <i class="bi bi-x-lg me-1"></i> Clear
            </button>
        </div>
    </div>

    <div class="data-table-card">
        <div class="table-scroll">
            <table class="table custom-table" id="callingTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                        </th>
                        <th>Lead Name</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Legal Status</th>
                        <th>GST No</th>
                        <th>Turnover</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="11" class="text-center p-5 text-muted">Awaiting data stream...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
        <div class="d-flex gap-2">
            <button id="selectAllBtn" class="chip-btn ghost">
                <i class="bi bi-check2-square"></i> Select All
            </button>
            <button id="openCampaignModalBtn" class="chip-btn primary" disabled>
                <i class="bi bi-plus-circle"></i> Create Campaign (<span id="selectedCount">0</span>)
            </button>
        </div>
        <div class="d-flex flex-column align-items-end">
            <div class="table-range-meta" id="callingRangeInfo">Showing 0-0 from 0 data</div>
            <ul class="pagination mb-0" id="paginationFilterLinks"></ul>
        </div>
    </div>
</div>

<!-- Campaign Creation Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <h5 class="modal-title fw-bold" style="font-family: Montserrat;"><i class="bi bi-megaphone-fill me-2"></i> New Campaign</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label for="campaignName" class="form-label fw-bold small text-muted text-uppercase" style="font-family: Montserrat;">Define Campaign Identity</label>
                    <input type="text" id="campaignName" class="form-control form-control-lg border-2" style="border-radius: 8px; font-family: Montserrat;" placeholder="e.g. Summer Outreach 2026">
                </div>
                <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                    <div class="fs-3 text-primary"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <span class="d-block fw-bold text-dark" style="font-family: Montserrat;">Assignment Scope</span>
                        <span class="text-muted small">You are grouping <span id="modalSelectedCount" class="fw-bold text-primary">0</span> contacts.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmCampaignBtn" class="btn px-4 py-2 fw-bold text-white shadow-sm" style="background: #434AFA; border-radius: 8px; font-family: Montserrat;">
                    Launch Campaign
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const $tbody = $('#callingTable tbody');
        
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        function loadFilterData() {
            $.get('{{ route("calling.campaigns") }}', function(campaigns) {
                var $camp = $('#filter_campaign'); $camp.empty().append('<option value="">Master Lead Pool</option>');
                (campaigns || []).forEach(function(c){ $camp.append('<option value="'+c.id+'">'+(c.name || 'Unnamed')+'</option>'); });
            });
            $.get('{{ route("calling.filter-options") }}', function(resp) {
                var $state = $('#filter_state'); $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){ $state.append('<option value="'+s.id+'">'+s.name+'</option>'); });

                var $list = $('#filter_list'); $list.empty().append('<option value="">All Lists</option>');
                (resp.lists || []).forEach(function(l){ $list.append('<option value="'+l.id+'">'+l.name+'</option>'); });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            $.get('{{ route("calling.cities", ["stateId" => ":id"]) }}'.replace(':id', stateId), function(cities){
                var $city = $('#filter_city'); $city.empty().append('<option value="">All Cities</option>');
                (cities || []).forEach(function(c){ $city.append('<option value="'+c.id+'">'+c.name+'</option>'); });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    let isChecked = r.is_selected ? 'checked' : '';
                    html += `<tr>
                        <td><input type="checkbox" class="form-check-input row-checkbox" value="${r.id}" ${isChecked}></td>
                        <td>${r.name || '-'}</td>
                        <td>${r.company_name || '-'}</td>
                        <td>${r.contact_person || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.legal_status || '-'}</td>
                        <td>${r.gst_number || '-'}</td>
                        <td>${r.turnover || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.phone || '-'}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="11" class="text-center p-5 text-muted">No records matching your search scope.</td></tr>';
            }
            $tbody.html(html);
            syncHeaderCheckbox();
        }

        function loadData(page = 1) {
            var campaignId = $('#filter_campaign').val();
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            var listId = $('#filter_list').val();
            const appliedCount = [campaignId, name, stateId, cityId, listId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('{{ route("calling.filter") }}?page=' + page, { campaign_id: campaignId, name, state_id: stateId, city_id: cityId, list_id: listId })
            .done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalCallings').text((data.total || 0).toLocaleString('en-IN'));
                updateSelectionUI();
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationFilterLinks'); $container.empty();
            if (data.last_page <= 1) return;
            $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="bi bi-chevron-left"></i> Previous</a></li>`);
            $container.append(`<li class="page-item active"><span class="page-link">${data.current_page} / ${data.last_page}</span></li>`);
            $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next <i class="bi bi-chevron-right"></i></a></li>`);
            $('#callingRangeInfo').text(`Showing ${data.from || 0}-${data.to || 0} from ${data.total || 0} data`);
        }

        function updateSelectionUI() {
            $.get('{{ route("calling.selection.status") }}', function(resp) {
                $('#selectedCount, #heroSelected, #modalSelectedCount').text(resp.count);
                $('#openCampaignModalBtn').prop('disabled', resp.count === 0);
                
                if (resp.count > 0) {
                    $('#selectionCommandCenter').fadeIn(200).css('display', 'flex');
                    $('#selectionCountText').text(resp.count);
                    
                    if (resp.all_matching) {
                        $('#globalSelectionStatus').show().html(`<i class="bi bi-globe me-1"></i> Global Selection Active`);
                        $('#selectAllMatchingBtn').hide();
                    } else {
                        $('#globalSelectionStatus').hide();
                        $('#selectAllMatchingBtn').show();
                    }
                } else {
                    $('#selectionCommandCenter').fadeOut(200);
                }
                syncHeaderCheckbox();
            });
        }

        function syncHeaderCheckbox() {
            let allChecked = $('.row-checkbox').length > 0 && $('.row-checkbox:not(:checked)').length === 0;
            $('#selectAllCheckbox').prop('checked', allChecked);
        }

        loadFilterData(); loadData(1);

        $('#filter_campaign, #filter_city, #filter_list').on('change', () => loadData(1));
        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); loadData(1); });
        $('#filter_name').on('input', function() { loadData(1); });
        $('#resetFilters').on('click', function() {
            $('#filter_name, #filter_campaign, #filter_state, #filter_list').val(''); $('#filter_city').html('<option value="">All Cities</option>');
            $.post('{{ route("calling.selection.clear") }}', () => loadData(1));
        });

        $('#selectAllCheckbox').on('change', function() { 
            let checked = $(this).is(':checked');
            if (checked) {
                $('.row-checkbox').prop('checked', true).each(function() {
                    $.post('{{ route("calling.selection.toggle") }}', { id: $(this).val(), checked: true });
                });
            } else {
                $.post('{{ route("calling.selection.clear") }}', () => {
                    $('.row-checkbox').prop('checked', false);
                });
            }
            setTimeout(updateSelectionUI, 400);
        });

        $(document).on('change', '.row-checkbox', function() { 
            let id = $(this).val();
            let checked = $(this).is(':checked');
            $.post('{{ route("calling.selection.toggle") }}', { id, checked }, () => updateSelectionUI());
        });

        $('#selectAllMatchingBtn, #selectAllBtn').on('click', function() {
            var filters = {
                campaign_id: $('#filter_campaign').val(),
                name: ($('#filter_name').val() || '').trim(),
                state_id: $('#filter_state').val(),
                city_id: $('#filter_city').val(),
                list_id: $('#filter_list').val()
            };
            $.post('{{ route("calling.selection.all-matching") }}', { filters }, function() {
                $('.row-checkbox').prop('checked', true);
                $('#selectAllCheckbox').prop('checked', true);
                updateSelectionUI();
            });
        });

        $('#clearSelectionBtn').on('click', function() {
            $.post('{{ route("calling.selection.clear") }}', () => {
                $('.row-checkbox, #selectAllCheckbox').prop('checked', false);
                updateSelectionUI();
            });
        });

        $('#openCampaignModalBtn').on('click', () => $('#campaignModal').modal('show'));

        $('#confirmCampaignBtn').on('click', function() {
            var name = ($('#campaignName').val() || '').trim();
            if (!name) { Swal.fire('Wait!', 'Please provide a campaign name.', 'warning'); return; }

            var $btn = $(this); $btn.prop('disabled', true).text('Launching...');
            $.post('{{ route("calling.create-campaign") }}', { campaign_name: name, use_session_selection: true })
            .done(resp => {
                if (resp.success) {
                    $('#campaignModal').modal('hide'); $('#campaignName').val('');
                    Swal.fire('Identity Established', resp.message, 'success');
                    loadData(1); loadFilterData();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            }).always(() => { $btn.prop('disabled', false).text('Launch Campaign'); });
        });

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); loadData($(this).data('page')); });
    });
</script>
@endpush
