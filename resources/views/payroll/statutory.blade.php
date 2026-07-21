@extends('layouts.app')

@section('title', 'Statutory Rules')
@section('page_title', 'Statutory Rules')

@push('styles')
<style>
  .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .modern-card-body { padding: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .data-table-card .modern-card-body { padding: 0; }
  .data-table-card .table-responsive { border-radius: 5px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; scrollbar-color: #434AFA #e4e7ec; }
  .data-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-responsive::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-responsive::-webkit-scrollbar-thumb { background: #434AFA; border-radius: 999px; }
  .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
  .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
  .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
  .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
  .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }
  .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; }
  .btn-action { background: #263385 !important; border: none !important; padding: 0.25rem 0.5rem; color: #fff; border-radius: 4px; transition: all 0.2s ease; font-size: 0.75rem; line-height: 1.2; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); }
  .btn-action:hover { background: #1a235c !important; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); color: #fff; }
  .modal-content { border: none; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
  .modal-header { background: #434afa; color: white; border-radius: 8px 8px 0 0; padding: 1rem 1.5rem; border-bottom: none; }
  .modal-header .modal-title { font-weight: 600; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem; }
  .modal-header .btn-close { filter: brightness(0) invert(1); opacity: 0.8; }
  .modal-body { padding: 1.5rem; }
  .form-label { font-weight: 500; color: #434afa; font-size: 0.85rem; margin-bottom: 0.5rem; }
  .form-control, .form-select { border: 1px solid #e0e0e0; border-radius: 6px; padding: 0.5rem 0.75rem; font-size: 0.9rem; transition: all 0.3s ease; }
  .form-control:focus, .form-select:focus { border-color: #434afa; box-shadow: 0 0 0 0.2rem rgba(67, 74, 250, 0.25); }
  .btn-primary { background: #434afa; border-color: #434afa; font-weight: 500; padding: 0.5rem 1.5rem; border-radius: 6px; transition: all 0.3s ease; }
  .btn-primary:hover { background: #3238d9; border-color: #3238d9; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(67, 74, 250, 0.3); }
  .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 1055; }
  .loading-state { text-align: center; padding: 2rem; color: #667eea; }
  .loading-state i { font-size: 2rem; animation: spin 1s linear infinite; margin-bottom: 1rem; }
  @keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="mb-3 d-flex align-items-center justify-content-between">
    <div class="table-search mb-0 w-50">
      <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search rules..." />
      </div>
    </div>
    <button class="btn btn-primary btn-sm" id="addRuleBtn">
      <i class="bi bi-plus"></i> Add Rule
    </button>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="rulesTable">
          <thead>
            <tr>
              <th>Type</th>
              <th>Employee Rate (%)</th>
              <th>Employer Rate (%)</th>
              <th>Salary Limit (₹)</th>
              <th>Calculate On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="rulesTableBody">
            <tr>
              <td colspan="6" class="loading-state">
                <i class="bi bi-arrow-repeat spin"></i>
                <p>Loading rules...</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="table-range-meta" id="rulesRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle"><i class="bi bi-shield-check"></i> Add Rule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="ruleForm">
          @csrf
          <input type="hidden" id="rule_id" name="id">
          
          <div class="mb-3">
            <label class="form-label">Rule Type <span class="text-danger">*</span></label>
            <select class="form-select" id="type" name="type" required>
              <option value="PF">Provident Fund (PF)</option>
              <option value="ESI">Employee State Insurance (ESI)</option>
              <option value="PT">Professional Tax (PT)</option>
              <option value="TDS">Tax Deducted at Source (TDS)</option>
            </select>
          </div>
          
          <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Employee Rate (%)</label>
                <input type="number" step="0.01" class="form-control" id="employee_rate" name="employee_rate" placeholder="e.g. 12.00">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Employer Rate (%)</label>
                <input type="number" step="0.01" class="form-control" id="employer_rate" name="employer_rate" placeholder="e.g. 13.00">
              </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Salary Limit (₹)</label>
            <input type="number" step="0.01" class="form-control" id="salary_limit" name="salary_limit" placeholder="e.g. 15000">
            <small class="text-muted">Maximum salary amount this rule applies to (leave empty for no limit).</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Calculate On (Formula/Components)</label>
            <input type="text" class="form-control" id="calculate_on" name="calculate_on" placeholder="e.g. Basic + DA">
            <small class="text-muted">Specify which components this rule is calculated on.</small>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary" id="saveBtn">
              <i class="bi bi-check-circle me-2"></i> Save Rule
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="toast-container"></div>
@endsection

@push('scripts')
<script>
function showToast(title, message, type = 'success') {
    const icon = type === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger';
    const toastHtml = `
        <div class="toast align-items-center border-0 shadow-sm mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="toast-header bg-white border-bottom-0">
                <i class="bi ${icon} me-2 fs-5"></i>
                <strong class="me-auto text-dark">${title}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white text-muted pt-0">${message}</div>
        </div>
    `;
    const $toast = $(toastHtml);
    $('.toast-container').append($toast);
    const bsToast = new bootstrap.Toast($toast[0]);
    bsToast.show();
    $toast.on('hidden.bs.toast', () => $toast.remove());
}

function escapeHtml(text = '') {
  return (text || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/\'/g,'&#039;');
}

$(function () {
  const baseUrl = "{{ route('payroll.statutory.index') }}";
  let allRules = [];

  loadRules();

  function loadRules() {
    $.ajax({
      url: baseUrl,
      type: 'GET',
      success: function (data) {
        allRules = data;
        renderRules(data);
      }
    });
  }

  function renderRules(rules) {
    let rows = '';
    
    if (rules.length === 0) {
      rows = `<tr><td colspan="6" class="text-center text-muted py-4">No statutory rules found</td></tr>`;
    } else {
      $.each(rules, function (i, rule) {
        rows += `
          <tr>
            <td><strong>${escapeHtml(rule.type)}</strong></td>
            <td>${rule.employee_rate ? rule.employee_rate + '%' : 'N/A'}</td>
            <td>${rule.employer_rate ? rule.employer_rate + '%' : 'N/A'}</td>
            <td>${rule.salary_limit ? '₹' + rule.salary_limit : 'No Limit'}</td>
            <td>${escapeHtml(rule.calculate_on || 'N/A')}</td>
            <td>
              <button class="btn-action edit-rule" data-id="${rule.id}"><i class="bi bi-pencil"></i></button>
              <button class="btn-action delete-rule" data-id="${rule.id}"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
        `;
      });
    }
    
    $('#rulesTableBody').html(rows);
    $('#rulesRangeInfo').text(`Showing 1-${rules.length} from ${rules.length} data`);
  }

  $('#search').on('keyup', function() {
    const term = $(this).val().toLowerCase();
    const filtered = allRules.filter(r => r.type.toLowerCase().includes(term));
    renderRules(filtered);
  });

  $('#addRuleBtn').click(function() {
    $('#ruleForm')[0].reset();
    $('#rule_id').val('');
    $('#modalTitle').html('<i class="bi bi-shield-plus"></i> Add Rule');
    $('#ruleModal').modal('show');
  });

  $(document).on('click', '.edit-rule', function() {
    const id = $(this).data('id');
    $.get(`${baseUrl}/${id}`, function(data) {
      $('#rule_id').val(data.id);
      $('#type').val(data.type);
      $('#employee_rate').val(data.employee_rate);
      $('#employer_rate').val(data.employer_rate);
      $('#salary_limit').val(data.salary_limit);
      $('#calculate_on').val(data.calculate_on);
      
      $('#modalTitle').html('<i class="bi bi-pencil-square"></i> Edit Rule');
      $('#ruleModal').modal('show');
    });
  });

  $('#ruleForm').submit(function(e) {
    e.preventDefault();
    const id = $('#rule_id').val();
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    const type = id ? 'PUT' : 'POST';
    const data = $(this).serialize();

    $.ajax({
      url: url,
      type: type,
      data: data,
      success: function(res) {
        if(res.success) {
          $('#ruleModal').modal('hide');
          showToast('Success', res.message);
          loadRules();
        }
      },
      error: function(xhr) {
        showToast('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
      }
    });
  });

  $(document).on('click', '.delete-rule', function() {
    if(confirm('Are you sure you want to delete this rule?')) {
      const id = $(this).data('id');
      $.ajax({
        url: `${baseUrl}/${id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
          if(res.success) {
            showToast('Success', res.message);
            loadRules();
          }
        }
      });
    }
  });
});
</script>
@endpush
