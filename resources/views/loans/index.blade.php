@extends('layouts.app')

@section('title', 'My Financial Requests')
@section('page_title', 'My Financial Requests')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
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
  }
  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
  }
  .table-search-btn:hover { background: #3538d4; color: white; text-decoration: none;}
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
  }
  .table-responsive { padding: 0.5rem 0.75rem 1rem; overflow-x: auto; }
  .custom-table { border-spacing: 0; width: 100%; font-size: 0.85rem; }
  .custom-table thead th { background: #fff; font-size: 0.8rem; font-weight: 600; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; text-align: left; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-rejected { background: #fee2e2; color: #991b1b; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem;}

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      overflow: hidden;
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 0.6rem 1rem;
  }
  
  .modal-footer {
      border-top: 1px solid #f0f0f0;
      padding: 0.6rem 1rem;
      background: #fff;
  }

  .form-label-modern {
      color: #434AFA;
      font-weight: 600;
      margin-bottom: 0.2rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
      font-size: 0.75rem;
  }
  
  .form-control-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.8rem;
      width: 100%;
  }
  
  .form-control-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
      outline: none;
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
  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
  }
  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); color: white; }
  .icon-green { background: linear-gradient(135deg, #34d399, #10b981); color: white; }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); color: white; }
  .icon-orange { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }
  .icon-red { background: linear-gradient(135deg, #fb7185, #f43f5e); color: white; }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }
  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    line-height: 1.2;
  }
  .summary-card-value {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
    color: #101828;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search loans..." />
    </div>
    <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#formModal" id="addBtn">
        <i class="bi bi-plus me-1"></i>Request
    </button>
  </div>

  @if(session('success'))
      <div class="alert alert-success mt-2">{{ session('success') }}</div>
  @endif

  <div id="financialSummary" class="summary-cards d-none mt-3"></div>

  <div class="data-table-card mt-3">
    <div class="table-responsive">
      <table class="table custom-table" id="mainTable">
        <thead>
          <tr>
            <th>Type</th>
            <th>Employee</th>
            <th>Principal / Amt</th>
            <th>Interest</th>
            <th>Total Payable</th>
            <th>Details</th>
            <th>Remaining Balance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="loansTableBody">
          <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Request Loan Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="modalTitle">
          <i class="bi bi-plus text-white"></i> Request
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('loans.store') }}" method="POST" id="mainForm">
        <div class="modal-body bg-white pt-4 pb-4">
          @csrf
          <input type="hidden" name="_method" id="formMethod" value="POST">
          <div id="formErrors" class="alert alert-danger d-none"></div>
          
          <div class="row g-3 mb-3">
              <div class="col-md-12">
                  <label class="form-label-modern">Request Type <span class="text-danger">*</span></label>
                  <select id="requestType" class="form-control-modern" style="font-size: 0.85rem;" required>
                      <option value="loan">Loan</option>
                      <option value="advance">Salary Advance</option>
                  </select>
              </div>
          </div>

          <div class="row g-3 mb-3">
              <div class="col-md-6">
                  <label class="form-label-modern">Employee <span class="text-danger">*</span></label>
                  <input type="text" class="form-control-modern bg-light" value="{{ session('user_name') }}" readonly>
                  <input type="hidden" name="employee_id" value="{{ \App\Models\User::find(session('user_id'))?->employee_id }}">
              </div>
              <div class="col-md-6">
                  <label class="form-label-modern"><span id="amountLabel">Loan</span> Amount <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" name="amount" class="form-control-modern" required value="{{ old('amount') }}">
              </div>
          </div>

          <div class="row g-3 mb-3" id="loanFields">
              <div class="col-md-6">
                  <label class="form-label-modern">EMI Amount <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" name="emi_amount" id="emiInput" class="form-control-modern" required value="{{ old('emi_amount') }}">
              </div>
              <div class="col-md-6">
                  <label class="form-label-modern">Start Month <span class="text-danger">*</span></label>
                  <input type="month" name="start_month" id="loanStartInput" class="form-control-modern" required value="{{ old('start_month') }}" min="{{ \Carbon\Carbon::now()->format('Y-m') }}">
              </div>
          </div>

          <div class="row g-3 mb-3 d-none" id="advanceFields">
              <div class="col-md-6">
                  <label class="form-label-modern">Deduction Start Month <span class="text-danger">*</span></label>
                  <input type="month" name="deduction_start_month" id="advanceStartInput" class="form-control-modern bg-light" value="{{ \Carbon\Carbon::now()->format('Y-m') }}" readonly disabled>
              </div>
          </div>

          <div class="row g-3">
              <div class="col-md-12">
                  <label class="form-label-modern">Reason (Optional)</label>
                  <textarea name="reason" class="form-control-modern" rows="3">{{ old('reason') }}</textarea>
              </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn table-search-btn w-100 justify-content-center" id="saveBtn">
            <i class="bi bi-check-circle"></i> Submit Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
          <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            ${message}
          </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => $('.alert.alert-dismissible').fadeOut('slow', function() { $(this).remove(); }), 3000);
    }

    let defaultAction = "{{ route('loans.store') }}";

    function fetchLoans() {
        $.ajax({
            url: "{{ route('loans.fetch') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(response) {
                let html = '';
                let totalLoan = 0;
                let paidLoan = 0;
                let remLoan = 0;
                
                let totalAdv = 0;
                let paidAdv = 0;
                let remAdv = 0;

                if(response.data.length === 0) {
                    html = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4"></i><br>No requests found</td></tr>';
                } else {
                    response.data.forEach(function(req) {
                        let amt = parseFloat(req.amount) || 0;
                        let rem = parseFloat(req.remaining_balance) || 0;
                        let paid = amt - rem;
                        if(paid < 0) paid = 0;

                        if (req.request_type === 'loan') {
                            totalLoan += amt;
                            remLoan += rem;
                            paidLoan += paid;
                        } else {
                            totalAdv += amt;
                            remAdv += rem;
                            paidAdv += paid;
                        }

                        let empName = req.employee ? req.employee.name : 'N/A';
                        let amount = amt.toFixed(2);
                        let remStr = rem.toFixed(2);
                        
                        let totalInterestStr = 'N/A';
                        let totalPayableStr = 'N/A';
                        if (req.request_type === 'loan') {
                            totalInterestStr = (parseFloat(req.total_interest) || 0).toFixed(2) + ' (' + (parseFloat(req.applied_interest_rate)||0) + '%)';
                            totalPayableStr = (parseFloat(req.total_payable) || amt).toFixed(2);
                        }
                        
                        let typeBadge = req.request_type === 'loan' ? '<span class="badge bg-primary" style="font-size: 0.75rem;">Loan</span>' : '<span class="badge bg-info text-dark" style="font-size: 0.75rem;">Advance</span>';
                        
                        let details = '';
                        if (req.request_type === 'loan') {
                            details = req.total_installments + ' EMIs';
                        } else {
                            let formattedMonth = req.deduction_start_month;
                            if(formattedMonth && formattedMonth.includes('-')) {
                                let parts = formattedMonth.split('-');
                                let months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
                                if(parts.length >= 2) formattedMonth = months[parseInt(parts[1], 10) - 1] + '-' + parts[0];
                            }
                            details = 'Start: ' + (formattedMonth || 'N/A');
                        }

                        let statusBadge = '';
                        if(req.status == 'active' || req.status == 'completed') statusBadge = `<span class="badge-active">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span>`;
                        else if(req.status == 'rejected') statusBadge = `<span class="badge-rejected">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span>`;
                        else statusBadge = `<span class="badge-pending">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span>`;

                        let actions = '';
                        if(req.status === 'pending') {
                            actions = `
                                <button class="btn btn-sm btn-danger py-1 px-2 delete-btn" style="font-size: 11px;" data-id="${req.id}" data-type="${req.request_type}"><i class="bi bi-trash"></i></button>
                            `;
                        } else {
                            actions = `<span class="text-muted" style="font-size: 11px;">N/A</span>`;
                        }
                        
                        html += `
                            <tr>
                                <td>${typeBadge}</td>
                                <td><strong>${empName}</strong></td>
                                <td>${amount}</td>
                                <td>${totalInterestStr}</td>
                                <td>${totalPayableStr}</td>
                                <td>${details}</td>
                                <td>${remStr}</td>
                                <td>${statusBadge}</td>
                                <td>${actions}</td>
                            </tr>
                        `;
                    });
                }
                $('#loansTableBody').html(html);

                $('#financialSummary').html(`
                    <div class="summary-card">
                        <div class="summary-card-icon icon-blue">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Loan Amount</div>
                            <div class="summary-card-value">${totalLoan.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-icon icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Paid Loan</div>
                            <div class="summary-card-value">${paidLoan.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-icon icon-orange">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Rem. Loan</div>
                            <div class="summary-card-value">${remLoan.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-icon icon-purple">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Adv. Amount</div>
                            <div class="summary-card-value">${totalAdv.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-icon icon-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Paid Adv.</div>
                            <div class="summary-card-value">${paidAdv.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-card-icon icon-red">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="summary-card-content">
                            <div class="summary-card-label">Rem. Adv.</div>
                            <div class="summary-card-value">${remAdv.toFixed(2)}</div>
                        </div>
                    </div>
                `).removeClass('d-none');
            }
        });
    }

    $(document).ready(function() {
        // Initial Fetch
        fetchLoans();

        $('#requestType').on('change', function() {
            if($(this).val() === 'loan') {
                $('#loanFields').removeClass('d-none');
                $('#emiInput, #loanStartInput').prop('disabled', false);
                $('#advanceFields').addClass('d-none');
                $('#advanceStartInput').prop('disabled', true);
                $('#amountLabel').text('Loan');
                $('#mainForm').attr('action', "{{ route('loans.store') }}");
            } else {
                $('#loanFields').addClass('d-none');
                $('#emiInput, #loanStartInput').prop('disabled', true);
                $('#advanceFields').removeClass('d-none');
                $('#advanceStartInput').prop('disabled', false);
                $('#amountLabel').text('Advance');
                $('#mainForm').attr('action', "{{ route('salary_advances.store') }}");
            }
        });

        $('#addBtn').on('click', function() {
            $('#requestType').val('loan').trigger('change');
            $('#formMethod').val('POST');
            $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Request');
            $('#mainForm').find('input[type="number"], input[type="month"]:not([readonly]), textarea').val('');
            $('#formErrors').addClass('d-none').html('');
        });



        // Delete
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let deleteUrl = type === 'loan' ? `/loans/${id}` : `/salary-advances/${id}`;
            if(confirm(`Are you sure you want to delete this pending ${type}?`)) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if(response.success) {
                            showAlert('success', response.message);
                            fetchLoans();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showAlert('error', 'Error deleting loan.');
                    }
                });
            }
        });

        $('#mainForm').on('submit', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = $('#saveBtn');
            let formErrors = $('#formErrors');
            
            // Clear previous errors
            formErrors.addClass('d-none').html('');
            submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Submitting...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        $('#formModal').modal('hide');
                        showAlert('success', response.message);
                        fetchLoans();
                        submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
                    
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(key, value) {
                            errorHtml += '<li>' + value[0] + '</li>';
                        });
                        errorHtml += '</ul>';
                        formErrors.removeClass('d-none').html(errorHtml);
                    } else if(xhr.status === 403) {
                        showAlert('error', xhr.responseJSON.message || 'Action forbidden.');
                    } else {
                        showAlert('error', 'An unexpected error occurred. Please try again.');
                    }
                }
            });
        });
        
        // Reset form and errors when modal is hidden
        $('#formModal').on('hidden.bs.modal', function () {
            $('#requestType').val('loan').trigger('change');
            $('#formMethod').val('POST');
            $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Request');
            $('#mainForm').find('input[type="number"], input[type="month"]:not([readonly]), textarea').val('');
            $('#formErrors').addClass('d-none').html('');
            $('#saveBtn').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Submit Request');
        });
    });
</script>
@endpush
