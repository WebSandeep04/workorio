@extends('layouts.app')
@section('title', 'Payroll Exemptions')
@section('page_title', 'Payroll Exemptions')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExemptionModal">
                <i class="bi bi-plus-circle me-1"></i> Add Exemption
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="exemptionsTable" style="width: 100%;">
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
        processing: true,
        serverSide: false,
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
