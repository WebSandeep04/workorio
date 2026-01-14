@extends('layouts.app')

@section('title', 'Countries')
@section('page_title', 'Countries')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
            <h6 class="mb-0"><i class="bi bi-globe me-2"></i>Country Master</h6>
            <button class="btn btn-sm btn-light" id="openCountryModal"><i class="bi bi-plus-circle me-1"></i>Add Country</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered align-middle text-center">
                    <thead class="table-secondary">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="countryTableBody"><tr><td colspan="4">Loading...</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');
    const listUrl = "{{ route('countries.list') }}";
    const storeUrl = "{{ route('countries.store') }}";

    function escapeHtml(text = '') {
        return (text || '').toString()
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    function loadCountries() {
        $('#countryTableBody').html('<tr><td colspan="4">Loading...</td></tr>');
        $.get(listUrl).done(function(rows){
            if (!rows.length) {
                $('#countryTableBody').html('<tr><td colspan="4" class="text-center">No records found</td></tr>');
                return;
            }
            let html = '';
            rows.forEach(function(row){
                html += `<tr>
                    <td>${escapeHtml(row.code || '')}</td>
                    <td>${escapeHtml(row.name)}</td>
                    <td><span class="badge ${row.status === 'active' ? 'bg-success' : 'bg-secondary'}">${escapeHtml(row.status || '')}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-primary edit-country" data-country='${JSON.stringify(row)}'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-danger delete-country" data-id="${row.id}"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            $('#countryTableBody').html(html);
        }).fail(function(){
            $('#countryTableBody').html('<tr><td colspan="4" class="text-center text-danger">Failed to load</td></tr>');
        });
    }

    function openModal(data) {
        $('#countryForm')[0].reset();
        $('#country_id').val(data && data.id ? data.id : '');
        $('#countryModalLabel').text(data ? 'Edit Country' : 'Add Country');
        $('#countryError').addClass('d-none').text('');
        if (data) {
            $('#country_code').val(data.code || '');
            $('#country_name').val(data.name || '');
            $('#country_status').val(data.status || 'active');
            $('#country_notes').val(data.notes || '');
        } else {
            $('#country_status').val('active');
        }
        new bootstrap.Modal('#countryModal').show();
    }

    function saveCountry() {
        const id = $('#country_id').val();
        const payload = {
            _token: csrf,
            code: $('#country_code').val().trim(),
            name: $('#country_name').val().trim(),
            status: $('#country_status').val(),
            notes: $('#country_notes').val().trim(),
        };
        const method = id ? 'PUT' : 'POST';
        const url = id ? `{{ url('/countries') }}/${id}` : storeUrl;

        $('#saveCountry').prop('disabled', true).text('Saving...');
        $.ajax({ url, method, data: payload }).done(function(){
            bootstrap.Modal.getInstance(document.getElementById('countryModal')).hide();
            loadCountries();
        }).fail(function(xhr){
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to save.';
            $('#countryError').removeClass('d-none').text(msg);
        }).always(function(){
            $('#saveCountry').prop('disabled', false).text('Save');
        });
    }

    function deleteCountry(id) {
        if (!confirm('Delete this country?')) return;
        $.ajax({
            url: `{{ url('/countries') }}/${id}`,
            method: 'DELETE',
            data: { _token: csrf },
        }).done(loadCountries)
          .fail(()=>alert('Delete failed'));
    }

    $('#openCountryModal').on('click', function(){ openModal(null); });
    $('#saveCountry').on('click', saveCountry);
    $(document).on('click', '.edit-country', function(){ openModal($(this).data('country')); });
    $(document).on('click', '.delete-country', function(){ deleteCountry($(this).data('id')); });
    $(document).ready(loadCountries);
})();
</script>
@endpush

<div class="modal fade" id="countryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title mb-0" id="countryModalLabel">Add Country</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="countryForm">
                    <input type="hidden" id="country_id">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Code</label>
                            <input type="text" id="country_code" class="form-control form-control-sm" placeholder="Auto-generated" readonly>
                            <small class="text-muted">Auto-generated after save.</small>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" id="country_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select id="country_status" class="form-select form-select-sm">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea id="country_notes" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </form>
                <div class="alert alert-danger d-none mt-3" id="countryError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="saveCountry">Save</button>
            </div>
        </div>
    </div>
</div>

