@extends('layouts.app')

@section('title', 'My Gen Leads')
@section('page_title', 'My Gen Leads')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards,
  .status-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .metric-arrow {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    color: #000;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    position: absolute;
    right: 8px;
    bottom: 8px;
    font-size: 0.9rem;
  }

  .metric-arrow:hover { background: #5b59f7; color: #fff; }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img { width: 24px; height: 24px; object-fit: contain; }

  .icon-sunrise { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-emerald { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content { display: flex; flex-direction: column; justify-content: space-between; flex-grow: 1; min-width: 0; }
  .summary-card:hover { transform: translateY(-2px); box-shadow: 0px 8px 8px 0px #0000000A; }
  .summary-card-label { font-size: 9px; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; color: #000; line-height: 1.2; font-family: Montserrat; }
  .summary-card-value { font-size: 1.2rem; font-weight: 700; margin: 0; display: flex; align-items: center; line-height: 1; color: #101828; font-family: Montserrat; }

  .status-card {
    border-radius: 8px;
    padding: 0.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .status-card:nth-child(6n+1) { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
  .status-card:nth-child(6n+2) { background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%); }
  .status-card:nth-child(6n+3) { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
  .status-card:nth-child(6n+4) { background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%); }
  .status-card:nth-child(6n+5) { background: linear-gradient(135deg, #16a085 0%, #27ae60 100%); }
  .status-card:nth-child(6n+6) { background: linear-gradient(135deg, #d35400 0%, #e67e22 100%); }
  .status-card-label { font-size: 9px; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; }
  .status-card-value { font-size: 1.2rem; font-weight: 700; display: flex; align-items: center; line-height: 1; }

  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: white;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }
  .filterBox .form-label-modern { color: white; font-weight: 600; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.25rem; font-size: 10px; }
  .filterBox .form-control-modern { border: 2px solid rgba(255, 255, 255, 0.4); border-radius: 6px; padding: 0.35rem 0.5rem; background: rgba(255, 255, 255, 0.98); color: #000; font-size: 10px; }

  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-btn { padding: 0.35rem 1rem; background: #434AFA; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
  .table-search-btn:hover { background: #3538d4; color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); }

  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; font-family: Montserrat; text-align: left; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; font-family: Montserrat; white-space: nowrap; text-align: left; }

  .status-badge { color: #000; font-size: 0.85rem; font-family: Montserrat; }
  .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; font-size: 10px; }
  .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; }
  .remark-link { color: #667eea; text-decoration: none; font-weight: 500; }
  .loading-state { text-align: center; padding: 1rem; color: #667eea; }
  .assign-select { font-size: 9px; padding: 2px 4px; border: 1px solid #e0e0e0; border-radius: 4px; width: 100%; max-width: 120px; }

  /* Modal Specific Styles from My Leads */
  .modal-content { border-radius: 0px !important; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
  .modal-header { background: #434AFA !important; color: white; padding: 0.6rem 1rem; border-radius: 0; }
  .form-label-modern { color: #434AFA; font-weight: 600; font-size: 0.75rem; font-family: Montserrat; display: block; margin-bottom: 2px; }
  .form-control-modern, .form-select-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.4rem 0.6rem; font-size: 0.8rem; font-family: Montserrat; width: 100%; }
  .btn-modern-primary { background: #434AFA; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
  .btn-modern-primary:hover { background: #3538d4; }
  .blur-active { filter: blur(2px); pointer-events: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card card-1">
      <div class="summary-card-icon icon-sunrise"><img src="{{ asset('img/icons/call.png') }}" alt="Calls"></div>
      <div class="summary-card-content"><div class="summary-card-label">Today's Follow Ups</div><div class="summary-card-value" id="todayFollowups">0</div></div>
      <a href="{{ route('todayfollowupstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-2">
      <div class="summary-card-icon icon-amber"><img src="{{ asset('img/icons/underprocess.png') }}" alt="Under Process"></div>
      <div class="summary-card-content"><div class="summary-card-label">Under Process</div><div class="summary-card-value" id="underProcess">0</div></div>
      <a href="{{ route('underprocesstable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-3">
      <div class="summary-card-icon icon-emerald"><img src="{{ asset('img/icons/tick.png') }}" alt="Completed"></div>
      <div class="summary-card-content"><div class="summary-card-label">Today Completed</div><div class="summary-card-value" id="todayCompleted">0</div></div>
      <a href="{{ route('todaycompletedtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-4">
      <div class="summary-card-icon icon-rose"><img src="{{ asset('img/icons/pending.png') }}" alt="Pending"></div>
      <div class="summary-card-content"><div class="summary-card-label">Today Pending</div><div class="summary-card-value" id="todayPending">0</div></div>
      <a href="{{ route('todaypendingtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="summary-card card-5">
      <div class="summary-card-icon icon-sky"><img src="{{ asset('img/icons/new.png') }}" alt="New"></div>
      <div class="summary-card-content"><div class="summary-card-label">Today New</div><div class="summary-card-value" id="todayNew">0</div></div>
      <a href="{{ route('todaynewtable') }}" class="metric-arrow"><i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <!-- Status Cards -->
  <div class="status-cards" id="statusCardsContainer"></div>

  <!-- Filters -->
  <div class="filterBox mb-2">
    <div><label class="form-label-modern">Status</label><select id="sales_status" class="form-control-modern"><option value="">Select</option></select></div>
    <div><label class="form-label-modern">State</label><select id="state" class="form-control-modern"><option value="">Select</option></select></div>
    <div><label class="form-label-modern">City</label><select id="city" class="form-control-modern"><option value="">Select</option></select></div>
    <div><label class="form-label-modern">Business Type</label><select id="business_type" class="form-control-modern"><option value="">Select</option></select></div>
    <div><label class="form-label-modern">Lead Source</label><select id="lead_source" class="form-control-modern"><option value="">Select</option></select></div>
    <div><label class="form-label-modern">Product</label><select id="product_type" class="form-control-modern"><option value="">Select</option></select></div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field"><i class="bi bi-search"></i><input type="text" id="search" placeholder="Search generated leads..." /></div>
    <button type="button" class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#addLeadModal">
      <i class="bi bi-plus me-1"></i>Add Gen Lead
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="sales_table">
          <thead>
            <tr>
              <th>Status</th><th>Assigned To</th><th>Prospect</th><th>Lead</th><th>Contact Person</th><th>Contact No.</th><th>Remark</th><th>Next Follow</th><th>Address</th><th>State</th><th>City</th><th>Email</th><th>Business</th><th>Source</th><th>Product</th><th>Ticket</th>
            </tr>
          </thead>
          <tbody><tr><td colspan="16" class="loading-state"><i class="bi bi-arrow-repeat"></i> Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta" id="leadgenRangeInfo"></div>
</div>

<div class="mt-2 d-flex justify-content-center"><ul class="pagination" id="paginationLinks"></ul></div>

@include('partials.remarks-modal')

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content blur-target">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2 text-white"></i>Add New Generated Lead</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-2">
        <form id="addLeadForm">
           <div class="row g-2">
              <div class="col-md-6">
                 <label class="form-label-modern">Prospectus <span style="color:red;">*</span></label>
                 <div class="input-group input-group-sm">
                   <select id="add_lead_prospectus" class="form-select-modern" required><option value="">Select Prospectus</option></select>
                   <button type="button" class="btn btn-outline-primary btn-sm" onclick="openProspectusModal()"><i class="bi bi-plus-lg"></i></button>
                 </div>
                 
                 <label class="form-label-modern mt-2">Lead Name</label><input type="text" id="add_lead_leadsName" class="form-control-modern">
                 <label class="form-label-modern mt-2">Contact Person</label><input type="text" id="add_lead_contactPerson" class="form-control-modern">
                 <label class="form-label-modern mt-2">Contact Number</label><input type="text" id="add_lead_contactNumber" class="form-control-modern">
                 <label class="form-label-modern mt-2">Status <span style="color:red;">*</span></label><select id="add_lead_sales_status" class="form-select-modern" required><option value="">Loading...</option></select>
                 
                 <label class="form-label-modern mt-2">Address</label><input type="text" id="add_lead_address" class="form-control-modern">
                 <label class="form-label-modern mt-2">State</label><select id="add_lead_state" class="form-select-modern"><option value="">Select State</option></select>
                 <label class="form-label-modern mt-2">City</label><select id="add_lead_city" class="form-select-modern"><option value="">Select City</option></select>
              </div>
              <div class="col-md-6">
                 <label class="form-label-modern">Email ID</label><input type="email" id="add_lead_email" class="form-control-modern">
                 <label class="form-label-modern mt-2">Website</label><input type="url" id="add_lead_website_link" class="form-control-modern">
                 <label class="form-label-modern mt-2">Next Follow-up Date <span style="color:red;">*</span></label><input type="date" id="add_lead_next_follow_up" class="form-control-modern" required>
                 
                 <label class="form-label-modern mt-2">Business Type</label><select id="add_lead_business_type" class="form-select-modern"><option value="">Loading...</option></select>
                 <label class="form-label-modern mt-2">Lead Sources</label><select id="add_lead_lead_source" class="form-select-modern"><option value="">Loading...</option></select>
                 <label class="form-label-modern mt-2">Product Type</label><select id="add_lead_product_type" class="form-select-modern"><option value="">Loading...</option></select>
                 
                 <label class="form-label-modern mt-2">Remark <span style="color:red;">*</span></label><textarea id="add_lead_remark" class="form-control-modern" rows="2" placeholder="Enter Remark" required></textarea>

                 <label class="form-label-modern mt-2">Assign to <span style="color:red;">*</span></label><select id="add_lead_assign_to" class="form-select-modern" required><option value="">Select User</option></select>
              </div>
           </div>
        </form>
      </div>
      <div class="modal-footer"><button type="button" onclick="submitLead(event)" class="btn-modern-primary w-100">Submit Lead</button></div>
    </div>
  </div>
</div>

<!-- Add Prospectus Modal -->
<div class="modal fade" id="addProspectusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5><i class="bi bi-building-add text-white me-2"></i>Add New Prospectus</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body py-2">
        <form id="addProspectusForm">
           <div class="row g-2">
             <div class="col-md-6"><label class="form-label-modern">Prospect Name <span style="color:red;">*</span></label><input type="text" id="modalnewProspectusName" class="form-control-modern" required></div>
             <div class="col-md-6"><label class="form-label-modern">Contact Person</label><input type="text" id="modal_contact_person" class="form-control-modern"></div>
             <div class="col-md-6"><label class="form-label-modern">Contact Number</label><input type="text" id="modal_contact_number" class="form-control-modern"></div>
             <div class="col-md-6"><label class="form-label-modern">Address</label><input type="text" id="modal_address" class="form-control-modern"></div>
             <div class="col-md-6"><label class="form-label-modern">State</label><select id="modal_state" class="form-select-modern"><option value="">Select State</option></select></div>
             <div class="col-md-6"><label class="form-label-modern">City</label><select id="modal_city" class="form-select-modern"><option value="">Select City</option></select></div>
             <div class="col-md-6"><label class="form-label-modern">Email</label><input type="email" id="modal_email" class="form-control-modern"></div>
             <div class="col-md-6"><label class="form-label-modern">Website</label><input type="url" id="modal_website_link" class="form-control-modern"></div>
             <div class="col-md-6"><label class="form-label-modern">Business Type</label><select id="modal_business_type" class="form-select-modern"><option value="">Loading...</option></select></div>
           </div>
        </form>
      </div>
      <div class="modal-footer"><button type="button" onclick="submitProspect(event)" class="btn-modern-primary w-100">Save Prospect</button></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
let currentPage = 1;

function formatDateOnly(value) {
    if (!value) return 'N/A';
    const d = new Date(value);
    return isNaN(d.getTime()) ? value : d.toISOString().split('T')[0];
}

function buildSimplePagination($container, current, last) {
    $container.empty();
    $container.append(`<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${Math.max(1, current - 1)}">Prev</a></li>`);
    $container.append(`<li class="page-item active"><span class="page-link">${current} / ${last}</span></li>`);
    $container.append(`<li class="page-item ${current === last ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${Math.min(last, current + 1)}">Next</a></li>`);
}

function loadSummaryStats() {
    $.get('{{ route("leadgen.my.summary-stats") }}', d => {
        $('#todayFollowups').text(d.today_followups); $('#underProcess').text(d.under_process); $('#todayCompleted').text(d.today_completed); $('#todayPending').text(d.today_pending); $('#todayNew').text(d.today_new);
    });
}

function loadStatusCounts() {
    $.get('{{ route("leadgen.my.status-counts") }}', data => {
        const $c = $('#statusCardsContainer').empty();
        data.forEach(s => { if(s.count > 0) $c.append(`<div class="status-card"><div class="status-card-label">${s.status_name}</div><div class="status-card-value">${s.count}</div></div>`); });
    });
}

function loadGenLeads(page = 1) {
    const filters = {
        _token: '{{ csrf_token() }}', status_id: $('#sales_status').val(), city_id: $('#city').val(), state_id: $('#state').val(), business_type_id: $('#business_type').val(), lead_source_id: $('#lead_source').val(), products_id: $('#product_type').val(), search: $('#search').val(), per_page: 10
    };
    $.post('{{ route("leadgen.my.filter") }}?page=' + page, filters, d => {
        let h = '';
        if (d.data.length === 0) h = '<tr><td colspan="16" class="text-center">No leads found.</td></tr>';
        else d.data.forEach(r => {
            let rem = '-'; 
            if (r.latest_remark) {
                const fullRemark = r.latest_remark.remark || '';
                const shortRemark = fullRemark.length > 15 ? fullRemark.substring(0, 15) + '...' : fullRemark;
                rem = `<a href="javascript:void(0)" class="remark-link" onclick="showRemarksModal(${r.id})" title="${fullRemark.replace(/"/g, '&quot;')}">${shortRemark}</a>`;
            }
            h += `<tr><td>${r.status?.status_name ?? ''}</td><td>${r.user?.name ?? 'Not Assigned'}</td><td>${r.prospectus?.prospectus_name ?? ''}</td><td>${r.leads_name ?? ''}</td><td>${r.contact_person ?? ''}</td><td>${r.contact_number ?? ''}</td><td>${rem}</td><td>${formatDateOnly(r.next_follow_up_date)}</td><td>${r.address ?? ''}</td><td>${r.state?.state_name ?? ''}</td><td>${r.city?.city_name ?? ''}</td><td>${r.email ?? ''}</td><td>${r.business_type?.business_name ?? ''}</td><td>${r.lead_source?.source_name ?? ''}</td><td>${r.product?.product_name ?? ''}</td><td>${r.ticket_value ?? 0}</td></tr>`;
        });
        $('#sales_table tbody').html(h);
        buildSimplePagination($('#paginationLinks'), d.current_page, d.last_page);
        $('#leadgenRangeInfo').text(`Showing ${d.from || 0}-${d.to || 0} from ${d.total} data`);
    });
}

function submitLead(e) {
    e.preventDefault();
    if(!$('#add_lead_assign_to').val()) { alert('Please select a user to assign the lead to.'); return; }
    const data = { _token: '{{ csrf_token() }}', prospectus_id: $('#add_lead_prospectus').val(), leads_name: $('#add_lead_leadsName').val(), contact_person: $('#add_lead_contactPerson').val(), contact_number: $('#add_lead_contactNumber').val(), status_id: $('#add_lead_sales_status').val(), address: $('#add_lead_address').val(), state_id: $('#add_lead_state').val(), city_id: $('#add_lead_city').val(), email: $('#add_lead_email').val(), website_link: $('#add_lead_website_link').val(), next_follow_up_date: $('#add_lead_next_follow_up').val(), business_type_id: $('#add_lead_business_type').val(), remark: $('#add_lead_remark').val(), lead_source_id: $('#add_lead_lead_source').val(), products_id: $('#add_lead_product_type').val(), user_id: $('#add_lead_assign_to').val() };
    $.post('/savelead', data).done(() => { $('#addLeadModal').modal('hide'); $('#addLeadForm')[0].reset(); loadGenLeads(); loadSummaryStats(); loadStatusCounts(); }).fail(() => alert('Failed to save lead'));
}

function openProspectusModal() { $('.blur-target').addClass('blur-active'); $('#addProspectusModal').modal('show'); }
$('#addProspectusModal').on('hidden.bs.modal', () => $('.blur-target').removeClass('blur-active'));

function submitProspect(e) {
    e.preventDefault();
    const data = { _token: '{{ csrf_token() }}', prospectus_name: $('#modalnewProspectusName').val(), contact_person: $('#modal_contact_person').val(), contact_number: $('#modal_contact_number').val(), address: $('#modal_address').val(), state_id: $('#modal_state').val(), city_id: $('#modal_city').val(), email: $('#modal_email').val(), website_link: $('#modal_website_link').val(), business_type_id: $('#modal_business_type').val() };
    $.post('/prospectus', data).done(() => { $('#addProspectusModal').modal('hide'); $('#addProspectusForm')[0].reset(); fetchProspectuses(); }).fail(() => alert('Failed to save prospectus'));
}

function fetchProspectuses() {
    $.get('{{ route("getProspectus") }}', data => {
        let h = '<option value="">Select Prospectus</option>';
        data.forEach(p => h += `<option value="${p.id}">${p.prospectus_name}</option>`);
        $('#add_lead_prospectus').html(h);
    });
}

$(document).ready(() => {
    loadSummaryStats(); loadStatusCounts();
    loadGenLeads();
    
    $.get('{{ route("leadgen.my.team-members") }}', d => { 
        let uo = '<option value="">Select User</option>';
        d.forEach(u => uo += `<option value="${u.id}">${u.name}</option>`);
        $('#add_lead_assign_to').html(uo);
    });
    
    $(document).on('change', '#sales_status, #city, #state, #business_type, #lead_source, #product_type', () => loadGenLeads(1));
    $('#search').on('keyup', () => loadGenLeads(1));
    $(document).on('click', '#paginationLinks .page-link', function(e) { e.preventDefault(); loadGenLeads($(this).data('page')); });

    // Prospectus Auto-fill
    $(document).on('change', '#add_lead_prospectus', function() {
        let id = $(this).val();
        if(id) {
            $.get('/fillprospectus/' + id, function(data) {
                $('#add_lead_contactPerson').val(data.contact_person);
                $('#add_lead_contactNumber').val(data.contact_number);
                $('#add_lead_address').val(data.address);
                $('#add_lead_leadsName').val(data.prospectus_name);
                $('#add_lead_email').val(data.email);
                $('#add_lead_website_link').val(data.website_link);
                $('#add_lead_business_type').val(data.business_type_id);
                $('#add_lead_state').val(data.state_id).trigger('change');
                setTimeout(() => $('#add_lead_city').val(data.city_id), 500); 
            });
        }
    });

    // Handle state change for cities in Add Lead modal
    $(document).on('change', '#add_lead_state', function() {
        $.get(`/city/${$(this).val()}`, d => {
            let h = '<option value="">Select City</option>';
            Object.entries(d).forEach(([id, n]) => h += `<option value="${id}">${n}</option>`);
            $('#add_lead_city').html(h);
        });
    });

    // Populate selects
    $.get("{{ route('getStatuses') }}", d => { 
        let h = '<option value="">Select Status</option>'; d.forEach(s => h += `<option value="${s.id}">${s.status_name}</option>`);
        $('#sales_status, #add_lead_sales_status').html(h);
    });
    $.get("{{ route('state') }}", d => {
        let h = '<option value="">Select State</option>'; Object.entries(d).forEach(([id, n]) => h += `<option value="${id}">${n}</option>`);
        $('#state, #modal_state, #add_lead_state').html(h);
    });
    $.get("{{ route('getbusiness') }}", d => {
        let h = '<option value="">Select Business</option>'; d.forEach(b => h += `<option value="${b.id}">${b.business_name}</option>`);
        $('#business_type, #add_lead_business_type, #modal_business_type').html(h);
    });
    $.get("{{ route('getsource') }}", d => {
        let h = '<option value="">Select Source</option>'; d.forEach(s => h += `<option value="${s.id}">${s.source_name}</option>`);
        $('#lead_source, #add_lead_lead_source').html(h);
    });
    $.get("{{ route('getproduct') }}", d => {
        let h = '<option value="">Select Product</option>'; d.forEach(p => h += `<option value="${p.id}">${p.product_name}</option>`);
        $('#product_type, #add_lead_product_type').html(h);
    });
    fetchProspectuses();

    $('#state').on('change', function() {
        $.get(`/city/${$(this).val()}`, d => {
            let h = '<option value="">Select City</option>'; Object.entries(d).forEach(([id, n]) => h += `<option value="${id}">${n}</option>`);
            $('#city').html(h);
        });
    });
    
    $('#modal_state').on('change', function() {
        $.get(`/city/${$(this).val()}`, d => {
            let h = '<option value="">Select City</option>'; Object.entries(d).forEach(([id, n]) => h += `<option value="${id}">${n}</option>`);
            $('#modal_city').html(h);
        });
    });
});
</script>
@endpush
