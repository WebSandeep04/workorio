@extends('layouts.app')

@section('title', 'Manage Loans (Admin)')
@section('page_title', 'Manage Loans (Admin)')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body { font-family: 'Montserrat', sans-serif !important; background-color: #f4f5f7; }
  .container-fluid { padding: 0.5rem; }

  /* Filter Box */
  .filterBox {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 0.5rem;
    background: #434AFA;
    padding: 0.75rem;
    color: #fff;
    border-radius: 5px;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(67, 74, 250, 0.3);
    margin-bottom: 0.5rem;
    border: 1px solid #434AFA;
    font-family: Montserrat, sans-serif;
  }
  .filterBox .form-label-modern {
    color: #fff;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 10px;
  }
  .filterBox .form-control-modern, .filterBox .form-select-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    font-size: 10px;
    width: 100%;
  }

  /* Table Search */
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }

  /* Table Styles */
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
  .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; }
  
  .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; table-layout: auto; }
  .custom-table thead th { background: #fff; color: #000; font-size: 0.8rem; font-weight: 600; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; text-align: left; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-pending { background: #fef08a; color: #854d0e; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-skipped { background: #e0e7ff; color: #3730a3; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  
  .btn-skip { background: #fb923c; color: white; border: none; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;}
  .btn-skip:hover { background: #ea580c; }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Filter Box -->
    <div class="filterBox">
        <div>
            <label class="form-label-modern"><i class="bi bi-funnel"></i> Status</label>
            <select class="form-select-modern" id="filterStatus">
                <option value="approved">Approved</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="loanSearch" placeholder="Search by employee name..." />
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="manageTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Total EMIs</th>
                            <th>Remaining Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="manageTableBody">
                        <tr><td colspan="6" class="text-center py-4 text-muted">Loading loans...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let allLoans = [];

    function fetchLoans() {
        $('#manageTableBody').html('<tr><td colspan="6" class="text-center py-4 text-muted">Loading loans...</td></tr>');
        
        $.ajax({
            url: "{{ route('loans.admin.fetch') }}",
            type: "POST",
            success: function(res) {
                allLoans = res.data || [];
                renderTable();
            },
            error: function(err) {
                $('#manageTableBody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load data.</td></tr>');
            }
        });
    }

    function renderTable() {
        let searchTerm = $('#loanSearch').val().toLowerCase();
        let statusFilter = $('#filterStatus').val();

        let filtered = allLoans.filter(loan => {
            // Only show loans that are not pending/rejected (i.e. approved or completed)
            if (loan.status === 'pending' || loan.status === 'rejected') return false;

            let matchSearch = true;
            let matchStatus = true;

            if(searchTerm) {
                let empName = (loan.employee && loan.employee.name) ? loan.employee.name.toLowerCase() : '';
                matchSearch = empName.includes(searchTerm);
            }

            if(statusFilter) {
                matchStatus = loan.status === statusFilter;
            }

            return matchSearch && matchStatus;
        });

        let html = '';
        if(filtered.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-4 text-muted">No loans found.</td></tr>';
        } else {
            filtered.forEach(loan => {
                let empName = (loan.employee && loan.employee.name) ? loan.employee.name : 'N/A';
                
                let badgeClass = 'badge-pending';
                if(loan.status === 'approved') badgeClass = 'badge-active';
                if(loan.status === 'rejected') badgeClass = 'text-danger fw-bold';

                let rem = loan.remaining_balance !== undefined ? parseFloat(loan.remaining_balance).toFixed(2) : '0.00';
                
                html += `
                    <tr>
                        <td style="vertical-align: middle;"><strong>${empName}</strong></td>
                        <td style="vertical-align: middle;">${parseFloat(loan.amount).toFixed(2)}</td>
                        <td style="vertical-align: middle;">${loan.total_installments}</td>
                        <td style="vertical-align: middle;">${rem}</td>
                        <td style="vertical-align: middle;"><span class="${badgeClass}">${loan.status.charAt(0).toUpperCase() + loan.status.slice(1)}</span></td>
                        <td style="vertical-align: middle;">
                            <a href="/admin/loans/${loan.id}/installments" class="btn btn-sm" title="View Installments" style="background: #434AFA; color: white; border: none; font-size: 0.8rem; border-radius: 4px; text-decoration: none;">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        $('#manageTableBody').html(html);
    }

    fetchLoans();

    $('#loanSearch').on('keyup', renderTable);
    $('#filterStatus').on('change', renderTable);

    function showAlert(type, msg) {
        let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${msg}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        $('#alertBox').html(alertHtml);
        setTimeout(() => { $('#alertBox .alert').alert('close'); }, 5000);
    }
});
</script>
@endpush
