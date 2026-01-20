@extends('layouts.app')

@section('title', 'Contact Management')
@section('page_title', 'Contact Management')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .summary-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    padding: 0.4rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon i {
      font-size: 16px;
      color: white;
  }

  .icon-blue { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
  .icon-green { background: linear-gradient(135deg, #34d399, #10b981); }
  .icon-orange { background: linear-gradient(135deg, #f97316, #fb923c); }
  .icon-purple { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
  .icon-red { background: linear-gradient(135deg, #fb7185, #f43f5e); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.15rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.1;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
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
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
  }

  .table-search-btn {
    padding: 0.35rem 1rem;
    background: #434afa;
    color: white;
    border: none;
    border-radius: 2px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }
  
  .table-search-btn:hover {
      background: #3538d4;
      color: white;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
  }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .table-responsive {
    border-radius: 18px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    min-width: 100%;
  }

  .data-table-card .custom-table thead th {
    background: #fff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.6rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #f1f3f5;
    font-family: Montserrat;
  }
  
  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
  }
  
  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .pagination .page-link {
    color: #667eea;
    border: 1px solid #e0e0e0;
    margin: 0 2px;
    font-size: 12px;
  }
  
  .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-color: #667eea;
      color: white;
  }

  /* Modal Styles */
  .modal-content {
      border-radius: 0px !important;
      border: none;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
  }
  
  .modal-header {
      border-radius: 0px !important;
      background: #434AFA !important;
      color: white;
      border-bottom: none;
      padding: 0.6rem 1rem;
  }
  
  .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
  }
  
  .form-label-modern {
      color: #000;
      font-weight: 600;
      margin-bottom: 0.2rem;
      font-size: 0.75rem;
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern {
      border: 1px solid #e0e0e0;
      border-radius: 4px;
      padding: 0.4rem 0.6rem;
      font-size: 0.8rem;
      font-family: Montserrat, sans-serif;
  }
  
  .form-control-modern:focus {
      border-color: #434AFA;
      box-shadow: 0 0 0 2px rgba(67, 74, 250, 0.1);
      outline: none;
  }

  .no-data {
      text-align: center;
      padding: 2rem;
      color: #6c757d;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <!-- Summary Cards -->
  <div class="summary-cards">
    <div class="summary-card">
      <div class="summary-card-icon icon-blue">
        <i class="bi bi-people"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">Total Contacts</div>
        <div class="summary-card-value" id="totalContacts">0</div>
      </div>
    </div>
    <div class="summary-card">
      <div class="summary-card-icon icon-green">
        <i class="bi bi-person-plus"></i>
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">New Today</div>
        <div class="summary-card-value" id="newToday">0</div>
      </div>
    </div>
    <div class="summary-card">
        <div class="summary-card-icon icon-purple">
          <i class="bi bi-envelope"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">With Email</div>
          <div class="summary-card-value" id="withEmail">0</div>
        </div>
      </div>
      <div class="summary-card">
        <div class="summary-card-icon icon-orange">
          <i class="bi bi-telephone"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">With Phone</div>
          <div class="summary-card-value" id="withPhone">0</div>
        </div>
      </div>
  </div>

  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="searchInput" placeholder="Search contacts by name, email, phone..." />
    </div>
    
    <button type="button" class="table-search-btn" id="addBtn" data-bs-toggle="modal" data-bs-target="#contactModal">
      <i class="bi bi-plus me-1"></i>Add Contact
    </button>
  </div>
  
  @if(session('user_role') == 1)
  <div class="d-flex align-items-center mb-3 ms-1">
      <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" id="adminViewAllToggle">
          <label class="form-check-label ms-1" for="adminViewAllToggle" style="font-size: 0.8rem; font-weight: 600;">View All</label>
      </div>
  </div>
  @endif

  <div class="data-table-card">
    <div class="table-responsive">
      <table class="table custom-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Company</th>
            <th>Industry</th>
            <th>Designation</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Other Text</th>
            <th>Remark</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="contactsTableBody">
            <!-- Data will be loaded here -->
        </tbody>
      </table>
      <div id="loadingState" class="text-center py-3" style="display: none;">
        <i class="bi bi-arrow-repeat spin"></i> Loading...
      </div>
      <div id="emptyState" class="no-data" style="display: none;">
        <i class="bi bi-inbox fs-2"></i>
        <p>No contacts found.</p>
      </div>
    </div>
    <div class="p-2 d-flex justify-content-end" id="paginationContainer">
        <!-- Pagination -->
    </div>
  </div>
</div>

<!-- Add/Edit Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Contact</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="contactForm">
          @csrf
          <input type="hidden" id="contactId" name="id">
          
          <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label-modern">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="name" name="name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="designation" class="form-label-modern">Designation</label>
                <input type="text" class="form-control form-control-modern" id="designation" name="designation">
            </div>
          </div>

          <div class="row">
               <div class="col-md-6 mb-3">
                <label for="company_name" class="form-label-modern">Company Name</label>
                <input type="text" class="form-control form-control-modern" id="company_name" name="company_name">
              </div>
              <div class="col-md-6 mb-3">
                <label for="industry" class="form-label-modern">Industry</label>
                <input type="text" class="form-control form-control-modern" id="industry" name="industry">
              </div>
          </div>

          <div class="row">
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label-modern">Email</label>
                <input type="email" class="form-control form-control-modern" id="email" name="email">
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone_primary" class="form-label-modern">Phone (Primary)</label>
                <input type="text" class="form-control form-control-modern" id="phone_primary" name="phone_primary">
              </div>
          </div>
          
          <div class="row">
             <div class="col-md-6 mb-3">
                <label for="phone_secondary" class="form-label-modern">Phone (Secondary)</label>
                <input type="text" class="form-control form-control-modern" id="phone_secondary" name="phone_secondary">
              </div>
              <div class="col-md-6 mb-3">
                <label for="website" class="form-label-modern">Website</label>
                <input type="text" class="form-control form-control-modern" id="website" name="website">
              </div>
          </div>

          <div class="mb-3">
            <label for="address" class="form-label-modern">Address</label>
            <textarea class="form-control form-control-modern" id="address" name="address" rows="2"></textarea>
          </div>
          
          <div class="row">
              <div class="col-md-6 mb-3">
                  <label for="city" class="form-label-modern">City</label>
                  <input type="text" class="form-control form-control-modern" id="city" name="city">
              </div>
              <div class="col-md-6 mb-3">
                  <label for="state" class="form-label-modern">State</label>
                  <input type="text" class="form-control form-control-modern" id="state" name="state">
              </div>
          </div>
          
          <div class="row">
              <div class="col-md-6 mb-3">
                  <label for="country" class="form-label-modern">Country</label>
                  <input type="text" class="form-control form-control-modern" id="country" name="country">
              </div>
               <div class="col-md-6 mb-3">
                  <label for="pincode" class="form-label-modern">Pincode</label>
                  <input type="text" class="form-control form-control-modern" id="pincode" name="pincode">
              </div>
          </div>

          <div class="mb-3">
            <label for="raw_text" class="form-label-modern">Other Text</label>
            <textarea class="form-control form-control-modern" id="raw_text" name="raw_text" rows="2"></textarea>
          </div>

          <div class="mb-3">
            <label for="remark" class="form-label-modern">Remark</label>
            <textarea class="form-control form-control-modern" id="remark" name="remark" rows="2"></textarea>
          </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id="saveBtn" style="background: #434AFA; border-color: #434AFA;">Save</button>
      </div>
    </div>
  </div>
</div>

@endsection

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewDetailsModalTitle">Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
      </div>
      <div class="modal-body" id="viewDetailsModalBody" style="word-wrap: break-word; white-space: pre-wrap;">
      </div>
    </div>
  </div>
</div>

</div>

<!-- View Card Modal -->
<div class="modal fade" id="viewCardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 10px; overflow: hidden;">
      <div class="modal-header" style="background: #434AFA; color: white;">
        <h5 class="modal-title"><i class="bi bi-card-heading me-2"></i>Contact Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
      </div>
      <div class="modal-body p-4">
          <div class="text-center mb-4">
              <h3 id="card_name" style="font-weight: 700; color: #333; margin-bottom: 0.2rem;">-</h3>
              <div id="card_designation" style="font-size: 0.9rem; color: #666; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">-</div>
              <div id="card_company" style="font-size: 1.1rem; color: #434AFA; font-weight: 600; margin-top: 0.5rem;">-</div>
              <div id="card_industry" style="font-size: 0.85rem; color: #888; margin-top: 0.2rem;">-</div>
          </div>
          
          <div class="row g-3">
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">EMAIL</small>
                  <span id="card_email" style="font-weight: 500;">-</span>
              </div>
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">PHONE (PRIMARY)</small>
                  <span id="card_phone" style="font-weight: 500;">-</span>
              </div>
              
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">PHONE (SECONDARY)</small>
                  <span id="card_phone_sec" style="font-weight: 500;">-</span>
              </div>
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">WEBSITE</small>
                  <span id="card_website" style="font-weight: 500;">-</span>
              </div>
              
              <div class="col-12">
                  <hr style="border-color: #eee; margin: 0.5rem 0;">
              </div>

              <div class="col-12">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">ADDRESS</small>
                  <span id="card_address" style="font-weight: 500;">-</span>
              </div>
              
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">CITY</small>
                  <span id="card_city">-</span>
              </div>
              <div class="col-md-6">
                  <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">STATE</small>
                  <span id="card_state">-</span>
              </div>
              
              <div class="col-md-6">
                   <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">COUNTRY</small>
                  <span id="card_country">-</span>
              </div>
              <div class="col-md-6">
                   <small class="text-muted d-block uppercase" style="font-size: 0.7rem;">PINCODE</small>
                  <span id="card_pincode">-</span>
              </div>
              
          </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function showAlert(type, message) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const alertHtml = `
    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  $('body').append(alertHtml);
  setTimeout(() => $('.alert').fadeOut(), 3000);
}

$(document).ready(function() {
    let currentPage = 1;
    let search = '';
    let viewAll = false;
    
    // Initial Load
    fetchStats();
    fetchContacts();

    // Admin Toggle Listener
    $('#adminViewAllToggle').change(function() {
        viewAll = $(this).is(':checked');
        currentPage = 1; // Reset to first page
        fetchContacts();
        fetchStats();
    });

    // Debounce search
    let timeout = null;
    $('#searchInput').on('keyup', function() {
        clearTimeout(timeout);
        search = $(this).val();
        timeout = setTimeout(function() {
            currentPage = 1;
            fetchContacts();
        }, 500);
    });

    // Handle Pagination click
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        if(page) {
            currentPage = page;
            fetchContacts();
        }
    });

    // Open Modal for Add
    $('#addBtn').click(function() {
        $('#modalTitle').text('Add Contact');
        $('#contactForm')[0].reset();
        $('#contactId').val('');
    });

    // Save Contact
    $('#saveBtn').click(function() {
        let id = $('#contactId').val();
        let url = id ? "{{ route('contactmanagement.update', ':id') }}".replace(':id', id) : "{{ route('contactmanagement.store') }}";
        let method = id ? 'PUT' : 'POST';
        
        let formData = $('#contactForm').serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#contactModal').modal('hide');
                showAlert('success', response.message);
                fetchContacts();
                fetchStats();
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let msg = '';
                    $.each(errors, function(key, value) {
                        msg += value[0] + '<br>';
                    });
                    showAlert('danger', msg);
                } else {
                    showAlert('danger', 'Something went wrong');
                }
            }
        });
    });

    // Edit Contact
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.get("{{ route('contactmanagement.edit', ':id') }}".replace(':id', id), function(data) {
            $('#modalTitle').text('Edit Contact');
            $('#contactId').val(data.id);
            $('#name').val(data.name);
            $('#company_name').val(data.company_name);
            $('#industry').val(data.industry);
            $('#designation').val(data.designation);
            $('#email').val(data.email);
            $('#phone_primary').val(data.phone_primary);
            $('#phone_secondary').val(data.phone_secondary);
            $('#website').val(data.website);
            $('#address').val(data.address);
            $('#raw_text').val(data.raw_text);
            $('#remark').val(data.remark);
            $('#city').val(data.city);
            $('#state').val(data.state);
            $('#country').val(data.country);
            $('#pincode').val(data.pincode);
            $('#contactModal').modal('show');
        });
    });

    // View Card Logic
    $(document).on('click', '.view-card-btn', function() {
        let id = $(this).data('id');
        $.get("{{ route('contactmanagement.edit', ':id') }}".replace(':id', id), function(data) {
             const v = (val) => val ? val : '-';
             
             $('#card_name').text(v(data.name));
             $('#card_designation').text(v(data.designation));
             $('#card_company').text(v(data.company_name));
             $('#card_industry').text(v(data.industry));
             
             $('#card_email').text(v(data.email));
             $('#card_phone').text(v(data.phone_primary));
             $('#card_phone_sec').text(v(data.phone_secondary));
             $('#card_website').text(v(data.website));
             
             $('#card_address').text(v(data.address));
             $('#card_city').text(v(data.city));
             $('#card_state').text(v(data.state));
             $('#card_country').text(v(data.country));
             $('#card_pincode').text(v(data.pincode));
             
             $('#card_remark').text(v(data.raw_text));
             $('#card_new_remark').text(v(data.remark));
             
             $('#viewCardModal').modal('show');
        });
    });

    // Delete Contact
    $(document).on('click', '.delete-btn', function() {
        if(confirm('Are you sure you want to delete this contact?')) {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ route('contactmanagement.destroy', ':id') }}".replace(':id', id),
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    showAlert('success', response.message);
                    fetchContacts();
                    fetchStats();
                },
                error: function() {
                    showAlert('danger', 'Failed to delete contact');
                }
            });
        }
    });

    function fetchStats() {
        $.get("{{ route('contactmanagement.stats') }}", { view_all: viewAll }, function(data) {
            $('#totalContacts').text(data.total_contacts);
            $('#newToday').text(data.new_today);
            $('#withEmail').text(data.with_email);
            $('#withPhone').text(data.with_phone);
        });
    }

    function fetchContacts() {
        $('#loadingState').show();
        $('#contactsTableBody').hide();
        $('#emptyState').hide();

        $.ajax({
            url: "{{ route('contactmanagement.fetch') }}",
            data: {
                page: currentPage,
                search: search,
                view_all: viewAll
            },
            success: function(response) {
                $('#loadingState').hide();
                let rows = '';
                
                 const formatCell = (text, label) => {
                    if(!text) return '-';
                    let str = String(text);
                    if(str.length > 7) {
                        let safeContent = str.replace(/"/g, '&quot;');
                        return `<span class="view-details" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-title="${label}" data-content="${safeContent}" style="cursor:pointer;">${str.substring(0, 7)}...</span>`;
                    }
                    return str;
                };

                if (response.data.length > 0) {
                    $.each(response.data, function(index, contact) {

                        rows += `<tr>
                            <td>${formatCell(contact.name, 'Name')}</td>
                            <td>${formatCell(contact.company_name, 'Company Name')}</td>
                            <td>${formatCell(contact.industry, 'Industry')}</td>
                            <td>${formatCell(contact.designation, 'Designation')}</td>
                            <td>${formatCell(contact.email, 'Email')}</td>
                            <td>${formatCell(contact.phone_primary, 'Phone')}</td>
                            <td>${formatCell(contact.address, 'Address')}</td>
                            <td>${formatCell(contact.raw_text, 'Other Text')}</td>
                            <td>${formatCell(contact.remark, 'Remark')}</td>
                            <td>
                                <button class="btn btn-sm text-info view-card-btn" data-id="${contact.id}" title="View Card"><i class="bi bi-eye"></i></button>
                                <button class="btn btn-sm text-primary edit-btn" data-id="${contact.id}"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm text-danger delete-btn" data-id="${contact.id}"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                    $('#contactsTableBody').html(rows).show();
                    
                    // Render Pagination
                    renderPagination(response);
                } else {
                    $('#emptyState').show();
                    $('#contactsTableBody').empty();
                    $('#paginationContainer').empty();
                }
            }
        });
    }

    function renderPagination(response) {
        let paginationHtml = '<nav><ul class="pagination pagination-sm mb-0">';
        
        // Previous
        if (response.prev_page_url) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="${response.prev_page_url}">&laquo;</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">&laquo;</span></li>`;
        }

        // Numbers (Simplified)
        for (let i = 1; i <= response.last_page; i++) {
             // Show first, last, current, and surrounding pages
             if (i === 1 || i === response.last_page || (i >= response.current_page - 1 && i <= response.current_page + 1)) {
                 let active = i === response.current_page ? 'active' : '';
                 paginationHtml += `<li class="page-item ${active}"><a class="page-link" href="?page=${i}">${i}</a></li>`;
             } else if (i === response.current_page - 2 || i === response.current_page + 2) {
                 paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             }
        }

        // Next
        if (response.next_page_url) {
            paginationHtml += `<li class="page-item"><a class="page-link" href="${response.next_page_url}">&raquo;</a></li>`;
        } else {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">&raquo;</span></li>`;
        }

        paginationHtml += '</ul></nav>';
        $('#paginationContainer').html(paginationHtml);
    }
    // View Details Modal Handler
    $('#viewDetailsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var title = button.data('title');
        var content = button.data('content');
        var modal = $(this);
        modal.find('.modal-title').text(title);
        modal.find('.modal-body').text(content);
    });
});
</script>
@endpush
