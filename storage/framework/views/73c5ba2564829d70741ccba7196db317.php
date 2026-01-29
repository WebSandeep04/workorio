

<?php $__env->startSection('title', 'Leave Management'); ?>
<?php $__env->startSection('page_title', 'Leave Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
  }

  .data-table-card .custom-table thead th {
    
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
   
  }

  /* Table Search & Add Button (Matches alldata) */
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
  
  .table-search-field i {
    color: #9ca3af;
    font-size: 0.85rem;
  }

  .table-search-field input {
    border: none;
    background: transparent;
    font-size: 0.85rem;
    width: 100%;
    outline: none;
    color: #111827;
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
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
    color: white;
    text-decoration: none;
  }
  
  /* Table Styles */
  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
    overflow: hidden;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-scroll {
    width: 100%;
    overflow-x: auto;
    padding: 0.5rem 0.75rem 1rem;
    background: transparent;
  }
  
  .data-table-card .table-scroll::-webkit-scrollbar { height: 8px; }
  .data-table-card .table-scroll::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
  .data-table-card .table-scroll::-webkit-scrollbar-thumb { background: #434aFA; border-radius: 999px; }

  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 800px;
    background: transparent;
    font-size: 0.85rem;
    table-layout: auto;
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
    border-right: 1px solid #f1f3f5;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }
  .data-table-card .custom-table thead th:last-child { border-right: none; }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #0f172a;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    font-family: Montserrat;
    vertical-align: middle;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    transform: translateY(-1px);
    box-shadow: 0px 2px 5px rgba(0,0,0,0.02);
  }
  
  /* Pagination */
  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
    cursor: pointer;
  }

  .pagination .page-item.active .page-link {
    background: #434afa;
    border-color: #434afa;
    color: white;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }

  .pagination .page-link:hover {
    background: rgba(67, 74, 250, 0.15);
    border-color: #434afa;
    transform: translateY(-1px);
  }
  
  .table-range-meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0.35rem 0 0.75rem;
  }
  
  /* Modals */
  .modal-content { border-radius: 0; border: none; }
  .modal-header { border-radius: 0; background-color: #434afa; color: white; }
  .modal-title { font-weight: 600; font-size: 1rem; }
  .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
  .form-control, .form-select { border-radius: 0; }
  .btn { border-radius: 0; }
  
  @media (max-width: 767px){
      .table-search { flex-direction: row; gap: 0.5rem; }
      .table-search-btn { width: auto; padding: 0.35rem 0.75rem; }
      .table-search-field { width: 100%; }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 mt-2">
    <div id="alertBox"></div>
    
    <!-- Search and Add -->
    <div class="table-search mb-2">
        <div class="table-search-field">
          <i class="bi bi-search"></i>
          <input type="text" id="leaveSearch" placeholder="Search leaves..." />
        </div>
        <button type="button" class="table-search-btn" onclick="openCreateModal()">
          <i class="bi bi-plus me-1"></i>Apply
        </button>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-scroll">
                <table class="table custom-table" id="leavesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Leave Type</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="leavesTableBody">
                        <tr><td colspan="4" class="text-center py-4 text-muted">Loading leaves...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="table-range-meta" id="leaveRangeInfo">
        Showing 0-0 of 0 entries
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-2">
        <ul class="pagination" id="pagination"></ul>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Apply Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="leaveForm">
                <div class="modal-body">
                    <input type="hidden" id="leaveId" name="id">
                    <div class="mb-3">
                        <label for="date" class="form-label small fw-bold">Leave Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" required>
                        <div class="invalid-feedback" id="dateError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="leave_type_id" class="form-label small fw-bold">Leave Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                            <option value="">Select Leave Type</option>
                        </select>
                        <div class="invalid-feedback" id="leave_type_idError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label small fw-bold">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Optional reason..."></textarea>
                        <div class="invalid-feedback" id="reasonError"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="submitBtn" style="background:#434afa; border-color:#434afa;">Apply Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this leave application?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn" style="background:#434afa; border-color:#434afa;">Delete</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let allLeaves = [];
let filteredLeaves = [];
let currentPage = 1;
let itemsPerPage = 10;
let currentLeaveId = null;
let deleteLeaveId = null;

$(document).ready(function() {
    loadLeaveTypes();
    loadLeaves();
    
    // Search Handler
    $('#leaveSearch').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        filteredLeaves = allLeaves.filter(leave => {
            return (leave.leave_type?.name || '').toLowerCase().includes(query) ||
                   (leave.reason || '').toLowerCase().includes(query) ||
                   (leave.date || '').includes(query);
        });
        currentPage = 1;
        renderTable();
    });
    
    // Modal & Form Handlers
    $('#leaveForm').on('submit', function(e) { e.preventDefault(); submitForm(); });
    $('#confirmDeleteBtn').on('click', function() { deleteLeave(); });
    
    $(document).on('click', '.edit-btn', function() {
        openEditModal($(this).data('id'), $(this).data('date'), $(this).data('type-id'), $(this).data('reason'));
    });
    
    $(document).on('click', '.delete-btn', function() {
        openDeleteModal($(this).data('id'));
    });
});

function showAlert(type, message) {
    let color = type === 'success' ? 'alert-success' : 'alert-danger';
    $('#alertBox').html(`<div class="alert ${color} alert-dismissible fade show border-0 shadow-sm" style="border-radius:0;">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
    setTimeout(() => $('.alert').fadeOut(500, function(){$(this).remove()}), 3000);
}

function loadLeaves() {
    $.get('<?php echo e(route("leave.fetch")); ?>', function(response) {
        if (response.data) {
            allLeaves = response.data;
            filteredLeaves = [...allLeaves];
            currentPage = 1;
            renderTable();
        }
    }).fail(() => showAlert('error', 'Failed to load leaves.'));
}

function loadLeaveTypes() {
    $.get('<?php echo e(route("leave.types")); ?>', function(response) {
        if (response.data) {
            let opts = '<option value="">Select Leave Type</option>';
            response.data.forEach(t => opts += `<option value="${t.id}">${t.name}</option>`);
            $('#leave_type_id').html(opts);
        }
    });
}

function renderTable() {
    const total = filteredLeaves.length;
    const start = (currentPage - 1) * itemsPerPage;
    const end = Math.min(start + itemsPerPage, total);
    const pageData = filteredLeaves.slice(start, end);
    
    let html = '';
    if (pageData.length === 0) {
        html = '<tr><td colspan="4" class="text-center py-4 text-muted">No leaves found</td></tr>';
    } else {
        pageData.forEach(leave => {
            html += `<tr>
                <td>${new Date(leave.date).toLocaleDateString()}</td>
                <td>${leave.leave_type ? leave.leave_type.name : 'Unknown'}</td>
                <td>${leave.reason || '-'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-link text-primary edit-btn p-0 me-2" data-id="${leave.id}" data-date="${leave.date}" data-type-id="${leave.leave_type_id}" data-reason="${leave.reason || ''}"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-link delete-btn p-0" data-id="${leave.id}" style="color:#434afa;"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
        });
    }
    
    $('#leavesTableBody').html(html);
    $('#leaveRangeInfo').text(`Showing ${total > 0 ? start + 1 : 0}-${end} of ${total} entries`);
    
    // Pagination
    const totalPages = Math.ceil(total / itemsPerPage);
    let pagHtml = '';
    if (totalPages > 1) {
        // Prev
        if (currentPage > 1) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage-1})">Previous</a></li>`;
        
        // Numbered
         if (totalPages <= 10) {
             for(let i=1; i<=totalPages; i++) {
                 pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
             }
         } else {
             pagHtml += `<li class="page-item ${1===currentPage?'active':''}"><a class="page-link" onclick="changePage(1)">1</a></li>`;
             if(currentPage > 3) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             let s = Math.max(2, currentPage - 1), e = Math.min(totalPages - 1, currentPage + 1);
             for(let i=s; i<=e; i++) {
                  pagHtml += `<li class="page-item ${i===currentPage?'active':''}"><a class="page-link" onclick="changePage(${i})">${i}</a></li>`;
             }
             if(currentPage < totalPages - 2) pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
             pagHtml += `<li class="page-item ${totalPages===currentPage?'active':''}"><a class="page-link" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
         }

        // Next
        if (currentPage < totalPages) pagHtml += `<li class="page-item"><a class="page-link" onclick="changePage(${currentPage+1})">Next</a></li>`;
    }
    $('#pagination').html(pagHtml);
}

function changePage(p) {
    currentPage = p;
    renderTable();
}

function openCreateModal() {
    currentLeaveId = null;
    $('#leaveModalLabel').text('Apply Leave');
    $('#submitBtn').text('Apply Leave');
    $('#leaveForm')[0].reset();
    $('#date').val(new Date().toISOString().split('T')[0]);
    $('#leaveModal').modal('show');
}

function openEditModal(id, date, typeId, reason) {
    currentLeaveId = id;
    $('#leaveModalLabel').text('Edit Leave');
    $('#submitBtn').text('Update Leave');
    $('#leaveId').val(id);
    $('#date').val(date);
    $('#leave_type_id').val(typeId);
    $('#reason').val(reason);
    $('#leaveModal').modal('show');
}

function openDeleteModal(id) {
    deleteLeaveId = id;
    $('#deleteModal').modal('show');
}

function submitForm() {
    const data = {
        _token: '<?php echo e(csrf_token()); ?>',
        date: $('#date').val(),
        leave_type_id: $('#leave_type_id').val(),
        reason: $('#reason').val()
    };
    if (currentLeaveId) data._method = 'PUT';
    
    const url = currentLeaveId ? `/leave/${currentLeaveId}` : '<?php echo e(route("leave.store")); ?>';
    const method = currentLeaveId ? 'POST' : 'POST'; 
    
    $.ajax({
        url: url, type: method, data: data,
        success: function(res) {
            if (res.success) {
                $('#leaveModal').modal('hide');
                showAlert('success', res.message);
                loadLeaves();
            }
        },
        error: function(xhr) {
             let msg = 'Error saving leave.';
             if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
             showAlert('error', msg);
        }
    });
}

function deleteLeave() {
    $.ajax({
        url: `/leave/${deleteLeaveId}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function(res) {
             if (res.success) {
                 $('#deleteModal').modal('hide');
                 showAlert('success', res.message);
                 loadLeaves();
             }
        },
        error: function() { showAlert('error', 'Error deleting leave.'); }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/leave/index.blade.php ENDPATH**/ ?>