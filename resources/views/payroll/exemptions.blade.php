@extends('layouts.app')
@section('title', 'Payroll Exemptions')
@section('page_title', 'Payroll Exemptions')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
        .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
        .table-search-btn { padding: 0.35rem 1rem; background: #434afa; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); display: inline-flex; align-items: center; }
        .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; text-decoration: none; }
        .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
        .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
        .modern-card { padding: 0; margin-bottom: 0.5rem; }
        .modern-card-body { padding: 0.5rem; }
        .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; background: white; border-radius: 0px; overflow: hidden; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); }
        .custom-table th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; font-size: 9px; padding: 0; text-align: center; border: none; position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1); }
        .custom-table td { font-size: 9px; padding: 0; vertical-align: middle; text-align: center; border-bottom: 1px solid #e9ecef; transition: all 0.3s ease; }
        .custom-table tbody tr:hover { background: rgba(102, 126, 234, 0.08); box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15); }
        .custom-table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .data-table-card .modern-card-body { padding: 0; }
        .data-table-card .table-responsive { border-radius: 18px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; }
        .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
        .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; font-family: Montserrat; }
        .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; font-family: Montserrat; }
        .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
        
        .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
        .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
        .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 10px;
        }
    </style>
@endpush

<div class="container-fluid px-2">
    <!-- Search and Action -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="exemptionSearch" placeholder="Search exemptions...">
        </div>
        <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#addExemptionModal">
            <i class="bi bi-plus me-1"></i>Add
        </button>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table" id="exemptionsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Exemption Modal -->
<div class="modal fade" id="addExemptionModal" tabindex="-1" aria-labelledby="addExemptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addExemptionForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExemptionModalLabel">Add Exemption</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none"></div>

                    <div class="mb-3">
                        <label class="form-label required">Exemption Type</label>
                        <select class="form-select" id="exemptionType" name="type" required>
                            <option value="">Select Type</option>
                            <option value="branch">Branch</option>
                            <option value="department">Department</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="branchSelectGroup">
                        <label class="form-label required">Branch</label>
                        <select class="form-select" id="branch_id" name="branch_id">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="departmentSelectGroup">
                        <label class="form-label required">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="employeeSelectGroup">
                        <label class="form-label required">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">Select Employee</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required">Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" name="reason" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveExemptionBtn">Save Exemption</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
$(document).ready(function() {
    let table = $('#exemptionsTable').DataTable({
        processing: false,
        serverSide: false,
        dom: '<"top">rt<"bottom"p><"clear">',
        language: {
            loadingRecords: "",
            processing: ""
        },
        ajax: {
            url: "{{ route('payroll.exemptions.fetch') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" }
        },
        columns: [
            { data: 'date', name: 'date', render: function(data) {
                return moment(data).format('DD-MMM-YYYY');
            }},
            { data: 'type', name: 'type', render: function(data) {
                return data.charAt(0).toUpperCase() + data.slice(1);
            }},
            { data: 'target', name: 'target', orderable: false, searchable: false },
            { data: 'reason', name: 'reason', defaultContent: '-' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    $('#exemptionSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#exemptionType').on('change', function() {
        let type = $(this).val();
        $('#branchSelectGroup, #departmentSelectGroup, #employeeSelectGroup').addClass('d-none');
        $('#branch_id, #department_id, #employee_id').prop('required', false).val('');
        
        if (type === 'branch') {
            $('#branchSelectGroup').removeClass('d-none');
            $('#branch_id').prop('required', true);
        } else if (type === 'department') {
            $('#branchSelectGroup, #departmentSelectGroup').removeClass('d-none');
            $('#branch_id, #department_id').prop('required', true);
        } else if (type === 'employee') {
            $('#branchSelectGroup, #departmentSelectGroup, #employeeSelectGroup').removeClass('d-none');
            $('#branch_id, #department_id, #employee_id').prop('required', true);
        }
    });

    $('#branch_id').on('change', function() {
        let branch_id = $(this).val();
        let deptSelect = $('#department_id');
        deptSelect.empty().append('<option value="">Select Department</option>');
        $('#employee_id').empty().append('<option value="">Select Employee</option>');

        if (branch_id) {
            $.get("{{ route('payroll.exemptions.departments') }}?branch_id=" + branch_id, function(data) {
                data.forEach(function(d) {
                    deptSelect.append(new Option(d.name, d.id));
                });
            });
        }
    });

    $('#department_id').on('change', function() {
        let dept_id = $(this).val();
        let empSelect = $('#employee_id');
        empSelect.empty().append('<option value="">Select Employee</option>');

        if (dept_id) {
            $.get("{{ route('payroll.exemptions.employees') }}?department_id=" + dept_id, function(data) {
                data.forEach(function(e) {
                    empSelect.append(new Option(e.name + ' (' + e.employee_code + ')', e.id));
                });
            });
        }
    });

    $('#addExemptionForm').on('submit', function(e) {
        e.preventDefault();
        $('#formErrors').addClass('d-none').html('');
        let btn = $('#saveExemptionBtn');
        btn.prop('disabled', true).html('Saving...');

        $.ajax({
            url: "{{ route('payroll.exemptions.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    $('#addExemptionModal').modal('hide');
                    $('#addExemptionForm')[0].reset();
                    $('#exemptionType').trigger('change');
                    table.ajax.reload();
                    Toast.fire({
                        icon: 'success',
                        title: res.message
                    });
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors || {};
                let msg = '<ul>';
                if (xhr.responseJSON.message) {
                    msg += `<li>${xhr.responseJSON.message}</li>`;
                }
                for (let key in errors) {
                    msg += `<li>${errors[key][0]}</li>`;
                }
                msg += '</ul>';
                $('#formErrors').removeClass('d-none').html(msg);
            },
            complete: function() {
                btn.prop('disabled', false).html('Save Exemption');
            }
        });
    });

    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this exemption?')) {
            $.ajax({
                url: `/payroll/exemptions/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if(res.success) {
                        table.ajax.reload();
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                    }
                }
            });
        }
    });
});
</script>
@endpush
@endsection
