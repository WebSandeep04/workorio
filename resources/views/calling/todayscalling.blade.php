@extends('layouts.app')

@section('title', "Today's Calling")
@section('page_title', "Today's Calling")

@push('styles')
<style>
    .calling-page { padding: 0.5rem; background: #f7f8fc; }
    .hero-metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; width: 100%; margin-bottom: 1rem; }
    .hero-metric-card { background: #fff; border-radius: 10px; border: 1px solid #eceef3; padding: 0.75rem 1rem; width: 100%; box-shadow: 0px 4px 4px 0px #0000000A; display: flex; align-items: center; gap: 0.75rem; }
    .hero-metric-icon { width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .hero-metric-icon img { width: 24px; height: 24px; object-fit: contain; }
    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
    .metric-label { font-size: 0.65rem; color: #000; text-transform: uppercase; font-weight: 600; font-family: Montserrat; }
    .metric-value { font-size: 1.2rem; font-weight: 700; color: #101828; font-family: Montserrat; }
    
    .filterBox { 
        display: grid; 
        grid-template-columns: 1fr 1fr 150px; 
        gap: 0.75rem; 
        background: #434AFA; 
        padding: 1rem; 
        border-radius: 8px; 
        color: #fff; 
        margin-bottom: 1rem; 
        align-items: end;
    }
    .form-label-modern { color: #fff; font-size: 10px; font-weight: 600; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.25rem; text-transform: uppercase; font-family: Montserrat; }
    .form-control-modern { border: 1px solid rgba(255, 255, 255, 0.4); border-radius: 6px; padding: 0.45rem 0.6rem; background: #fff; color: #000; font-size: 11px; font-family: Montserrat; width: 100%; }
    
    .filter-reset-btn { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 6px; background: rgba(255, 255, 255, 0.18); color: white; padding: 0.45rem; font-size: 10px; font-weight: 600; height: 35px; display: flex; align-items: center; justify-content: center; }
    
    .modern-card { border-radius: 8px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05); overflow: hidden; }
    .modern-card-header { padding: 1rem; border-bottom: 1px solid #eef0f6; display: flex; align-items: center; justify-content: space-between; background: white; }
    .section-eyebrow { font-size: 0.55rem; letter-spacing: 0.1em; text-transform: uppercase; color: #9ca3af; font-weight: 700; }
    .card-title-modern { margin: 0; font-size: 0.9rem; font-weight: 700; color: #101828; }
    
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .custom-table thead th { background: #fafbfc; color: #475467; font-size: 0.7rem; text-transform: uppercase; font-weight: 700; padding: 0.9rem 1rem; border-bottom: 1px solid #eaecf0; font-family: Montserrat; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.9rem 1rem; color: #344054; border-bottom: 1px solid #f2f4f7; font-family: Montserrat; }
    
    .pagination-wrapper { margin-top: 1.5rem; display: flex; justify-content: center; }
    .pagination .page-link { color: #434afa; border: 1px solid #dee2e6; padding: 0.25rem 0.75rem; margin: 0 2px; border-radius: 4px; font-size: 0.75rem; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 calling-page">
    <div id="alertContainer"></div>

    <!-- Metrics -->
    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="{{ asset('img/icons/call.png') }}" alt="Due Today">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Due today</span>
                <span class="metric-value" id="totalDue">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="{{ asset('img/icons/underprocess.png') }}" alt="Active Filters">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Filters</span>
                <span class="metric-value" id="activeFilters">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="{{ asset('img/icons/tick.png') }}" alt="Last Updated">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Last updated</span>
                <span class="metric-value" id="lastUpdated" style="font-size: 0.9rem;">--</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filterBox">
        <div>
            <label for="filter_state" class="form-label-modern"><i class="bi bi-geo-alt"></i> State</label>
            <select id="filter_state" class="form-control-modern">
                <option value="">All States</option>
            </select>
        </div>
        <div>
            <label for="filter_city" class="form-label-modern"><i class="bi bi-buildings"></i> City</label>
            <select id="filter_city" class="form-control-modern">
                <option value="">All Cities</option>
            </select>
        </div>
        <div>
            <button id="resetFilters" class="filter-reset-btn w-100">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-3" style="max-width: 400px;">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="filter_name" class="form-control border-start-0" placeholder="Search by name or phone...">
        </div>
    </div>

    <!-- Table -->
    <div class="modern-card">
        <div class="modern-card-header">
            <div>
                <p class="section-eyebrow mb-1">Today's queue</p>
                <h4 class="card-title-modern mb-0">Follow-up board</h4>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table custom-table" id="callingTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th style="width: 150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7" class="text-center p-5 text-muted">Loading today's follow-ups...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small" id="rangeInfo">Showing 0-0 from 0 data</div>
        <div class="pagination-wrapper mb-0">
            <ul class="pagination" id="paginationLinks"></ul>
        </div>
    </div>
</div>

@include('partials.remarks-modal')
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var $tbody = $('#callingTable tbody');
        let currentFilterPage = 1;

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        function loadFilterData() {
            $.get('{{ route("calling.todays.filter-options") }}', function(resp) {
                var $state = $('#filter_state');
                $state.empty().append('<option value="">All States</option>');
                (resp.states || []).forEach(function(s){
                    $state.append('<option value="'+s.id+'">'+s.name+'</option>');
                });
            });
        }

        function loadCitiesByState(stateId) {
            if (!stateId) { $('#filter_city').html('<option value="">All Cities</option>'); return; }
            var url = '{{ route("calling.todays.cities", ["stateId" => 0]) }}'.replace(/0$/, String(stateId));
            $.get(url, function(cities){
                var $city = $('#filter_city');
                $city.empty().append('<option value="">All Cities</option>');
                (cities || []).forEach(function(c){
                    $city.append('<option value="'+c.id+'">'+c.name+'</option>');
                });
            });
        }

        function renderRows(rows) {
            var html = '';
            if (rows && rows.length) {
                rows.forEach(function(r){
                    html += `<tr>
                        <td>${r.name || '-'}</td>
                        <td>${r.email || '-'}</td>
                        <td>${r.state || '-'}</td>
                        <td>${r.city || '-'}</td>
                        <td>${r.address || '-'}</td>
                        <td>${r.phone || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary show-remarks" data-id="${r.id}">
                                <i class="bi bi-chat-text"></i> Remarks
                            </button>
                        </td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="7" class="text-center p-4">No follow-ups due today.</td></tr>';
            }
            $tbody.html(html);
        }

        function loadData(page = 1) {
            currentFilterPage = page;
            var name = ($('#filter_name').val() || '').trim();
            var stateId = $('#filter_state').val();
            var cityId = $('#filter_city').val();
            
            const appliedCount = [name, stateId, cityId].filter(Boolean).length;
            $('#activeFilters').text(appliedCount);

            $.post('{{ route("calling.todays.filter") }}?page=' + page, {
                name: name,
                state_id: stateId,
                city_id: cityId,
                _token: '{{ csrf_token() }}'
            }).done(function(data){
                renderRows(data.data || []);
                buildPagination(data);
                $('#totalDue').text(data.total || 0);
                $('#lastUpdated').text(new Date().toLocaleTimeString());
            });
        }

        function buildPagination(data) {
            const $container = $('#paginationLinks');
            $container.empty();
            if (data.last_page <= 1) return;
            
            $container.append('<li class="page-item ' + (data.current_page === 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page - 1) + '">Previous</a></li>');
            $container.append('<li class="page-item active"><span class="page-link">' + data.current_page + ' / ' + data.last_page + '</span></li>');
            $container.append('<li class="page-item ' + (data.current_page === data.last_page ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (data.current_page + 1) + '">Next</a></li>');
            
            $('#rangeInfo').text('Showing ' + (data.from || 0) + '-' + (data.to || 0) + ' from ' + (data.total || 0) + ' data');
        }

        loadFilterData();
        loadData(1);

        $('#filter_state').on('change', function(){ loadCitiesByState($(this).val()); loadData(1); });
        $('#filter_city').on('change', () => loadData(1));
        $('#filter_name').on('input', debounce(() => loadData(1), 300));
        $('#resetFilters').on('click', function() {
            $('#filter_name').val('');
            $('#filter_state').val('');
            $('#filter_city').html('<option value="">All Cities</option>');
            loadData(1);
        });

        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            loadData($(this).data('page'));
        });

        $(document).on('click', '.show-remarks', function() {
            const id = $(this).data('id');
            if(window.showRemarksModal) window.showRemarksModal(id);
        });

        function debounce(fn, delay) {
            let t; return function() {
                clearTimeout(t); const args = arguments; const ctx = this;
                t = setTimeout(() => { fn.apply(ctx, args); }, delay);
            };
        }
    });
</script>
@endpush
