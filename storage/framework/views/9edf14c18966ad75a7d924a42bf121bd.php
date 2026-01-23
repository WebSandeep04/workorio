

<?php $__env->startSection('title', 'Petty Cash Approvals'); ?>
<?php $__env->startSection('page_title', 'Petty Cash Approvals'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Summary Cards - Same styling as alldata/pettycash index */
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

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
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
    font-size: 1.25rem;
  }

  .icon-amber { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
  .icon-rose { background: linear-gradient(135deg, #fb7185, #f43f5e); }
  
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
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern {
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    padding: 0.35rem 0.5rem;
    background: rgba(255, 255, 255, 0.98);
    color: #000;
    transition: all 0.3s ease;
    font-size: 10px;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern option {
    color: #000;
    background: #fff;
    font-family: Montserrat, sans-serif;
  }

  .filterBox .form-control-modern:focus {
    outline: none;
    border-color: #fff;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
    color: #000;
  }

  .table-search {
    width: 100%;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: space-between;
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

  .btn-custom-primary {
    background-color: #434afa;
    color: white;
    border: none;
    box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3);
  }
  
  .btn-custom-primary:hover {
     background-color: #3538d4;
     color: white;
     box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4);
  }
  
  .btn-custom-primary:disabled {
      background-color: #a0a3f5;
      cursor: not-allowed;
  }

  .modern-card {
    padding: 0;
    margin-bottom: 0.5rem;
  }

  .modern-card-body {
    padding: 0.5rem;
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
  }

  .badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
  }

  .btn-action {
    background: transparent !important;
    border: none !important;
    padding: 0.25rem;
    color: #6c757d;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .btn-action:hover {
    color: #434afa;
    transform: scale(1.1);
  }

  .pagination .page-link {
    color: #434afa;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    margin: 0 2px;
    font-size: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
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
  
  .spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  /* Custom Confirmation Modal */
  .custom-modal-header {
    background-color: #434afa;
    color: white;
    padding: 0.75rem 1rem;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
  }
  
  .custom-modal-title {
    font-weight: 600;
    font-size: 1rem;
    margin: 0;
  }
  
  .custom-modal-body {
    padding: 1.5rem 1rem;
    font-size: 0.9rem;
    color: #333;
    text-align: center;
  }
  
  .custom-modal-footer {
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    border-top: 1px solid #eee;
  }
  
  .btn-custom-confirm {
    background-color: #434afa;
    color: white;
    border: none;
    border-radius: 0; /* Radius 0 as requested */
    padding: 0.35rem 1.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: background 0.2s;
  }
  
  .btn-custom-confirm:hover {
    background-color: #3538d4;
    color: white;
  }
  
  .btn-custom-cancel {
    background-color: #f1f3f5;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 0; /* Radius 0 as requested */
    padding: 0.35rem 1.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    transition: background 0.2s;
  }
  
  .btn-custom-cancel:hover {
    background-color: #e9ecef;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">

  <!-- Summary Cards -->
  <div class="summary-cards">
      <div class="summary-card card-4">
        <div class="summary-card-icon icon-amber">
          <i class="bi bi-hourglass-split fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Pending (Count)</div>
          <div class="summary-card-value text-dark" id="stat_pending_count">0</div>
        </div>
      </div>
      <div class="summary-card card-5">
        <div class="summary-card-icon icon-rose">
          <i class="bi bi-cash-stack fs-5 text-white"></i>
        </div>
        <div class="summary-card-content">
          <div class="summary-card-label">Total Pending Amount</div>
          <div class="summary-card-value text-danger">₹<span id="stat_pending_amount">0.00</span></div>
        </div>
      </div>
  </div>

  <!-- Filters -->
  <div class="filterBox mb-3">
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-tag-fill"></i> Expense Type</label>
        <select class="form-control-modern" id="filter_expense">
            <option value="">All Expenses</option>
            <!-- Populated by JS -->
        </select>
    </div>
    <!-- Status Filter Hidden or Removed as this page is for Pending only -->
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> From Date</label>
        <input type="date" class="form-control-modern" id="filter_from_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-event"></i> To Date</label>
        <input type="date" class="form-control-modern" id="filter_to_date">
    </div>
    <div class="d-flex flex-column">
        <label class="form-label-modern"><i class="bi bi-calendar-month"></i> Month</label>
        <select class="form-control-modern" id="filter_month">
            <option value="">All Months</option>
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
        </select>
    </div>
  </div>

  <!-- Actions & Search -->
  <div class="table-search mb-2">
    <div class="table-search-field" style="max-width: 400px;"> <!-- Search on Left -->
      <i class="bi bi-search"></i>
      <input type="text" id="filter_search" placeholder="Search expenses..." />
    </div>
    
    <div class="d-flex gap-2"> <!-- Buttons on Right -->
      <button class="btn btn-custom-primary btn-sm" id="btnApproveSelected" disabled>
        <i class="bi bi-check-circle me-1"></i> Approve Selected
      </button>
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-responsive">
        <table class="table custom-table" id="pettyCashTable">
          <thead>
            <tr>
              <th width="40"><input type="checkbox" id="checkAll"></th>
              <th>Date</th>
              <th>Expense Name</th>
              <th>Price (₹)</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="6" class="text-center py-4">
                <i class="bi bi-arrow-repeat spin"></i> Loading data...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 d-flex justify-content-center">
    <div id="paginationLinks"></div>
  </div>
</div>

<!-- Edit Entry Modal -->
<div class="modal fade" id="editEntryModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Entry</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editEntryForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" id="edit_entry_id">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Expense Type <span class="text-danger">*</span></label>
            <select class="form-select" name="expense_id" id="edit_expense_id" required>
              <option value="">Select Expense</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
          </div>
          <div class="form-check mb-3">
             <input class="form-check-input" type="checkbox" name="is_approved" id="edit_is_approved" value="1">
             <label class="form-check-label" for="edit_is_approved">Approve Immediately</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0">
      <div class="custom-modal-header">
        <h5 class="custom-modal-title">Confirm Action</h5>
      </div>
      <div class="custom-modal-body" id="confirmationMessage">
        Are you sure?
      </div>
      <div class="custom-modal-footer">
        <button type="button" class="btn btn-custom-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-custom-confirm" id="btnConfirmAction">Confirm</button>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {

  let pendingAction = null; // To store the function to execute on confirmation

  // FUNTIONS defined first
  window.toggleBulkButtons = function() {
      let count = $('.row-checkbox:checked').length;
      $('#btnApproveSelected').prop('disabled', count === 0);
  }

  window.loadExpenses = function() {
    $.get("<?php echo e(route('petty-cash.fetch-expenses')); ?>", function(data) {
      let options = '<option value="">Select Expense</option>';
      let filterOptions = '<option value="">All Expenses</option>';
      
      data.forEach(function(expense) {
        options += `<option value="${expense.id}">${expense.name}</option>`;
        filterOptions += `<option value="${expense.id}">${expense.name}</option>`;
      });
      
      $('#edit_expense_id').html(options);
      $('#filter_expense').html(filterOptions);
    });
  }

  window.loadStats = function() {
      $.get("<?php echo e(route('petty-cash.stats')); ?>", function(data) {
          $('#stat_pending_count').text(data.total_pending_count);
          $('#stat_pending_amount').text(parseFloat(data.total_pending_amount).toFixed(2));
      });
  }

  window.loadData = function(page = 1) {
    let search = $('#filter_search').val();
    let expense_id = $('#filter_expense').val();
    let from_date = $('#filter_from_date').val();
    let to_date = $('#filter_to_date').val();
    let month = $('#filter_month').val();
    let status = 0; // Always fetch pending

    $.ajax({
      url: "<?php echo e(route('petty-cash.fetch')); ?>",
      data: { page, search, expense_id, from_date, to_date, month, status },
      success: function(response) {
        let rows = '';
        if(response.data.length > 0) {
          response.data.forEach(entry => {
            let date = new Date(entry.created_at).toLocaleDateString('en-GB', {
               day: '2-digit', month: 'short', year: 'numeric'
            });

            rows += `
              <tr>
                <td><input type="checkbox" class="row-checkbox" value="${entry.id}"></td>
                <td>${date}</td>
                <td>${entry.expense ? entry.expense.name : 'N/A'}</td>
                <td class="fw-bold">₹${parseFloat(entry.price).toFixed(2)}</td>
                <td><span class="badge badge-pending">Pending</span></td>
                <td>
                  <button class="btn-action text-success" title="Approve" onclick="approveEntry(this, ${entry.id})">
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button class="btn-action text-primary" title="Edit" onclick="editEntry(${entry.id}, ${entry.expense_id}, ${entry.price})">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn-action text-danger" title="Delete" onclick="deleteEntry(${entry.id})">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            `;
          });
        } else {
          rows = `<tr><td colspan="6" class="text-center py-4 text-muted">No pending approvals found</td></tr>`;
        }
        $('#pettyCashTable tbody').html(rows);
        toggleBulkButtons();
        $('#checkAll').prop('checked', false);

        // Render Pagination
        let links = '';
        if (response.links) {
          response.links.forEach(link => {
            let activeClass = link.active ? 'active' : '';
            let disabledClass = link.url ? '' : 'disabled';
            let label = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
            if (link.url) {
                links += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadData(${link.url.split('page=')[1]})">${label}</a></li>`;
            } else {
                links += `<li class="page-item ${disabledClass}"><span class="page-link">${label}</span></li>`;
            }
          });
          $('#paginationLinks').html(`<ul class="pagination pagination-sm">${links}</ul>`);
        }
      }
    }); // end ajax
  }

  // Edit Entry
  window.editEntry = function(id, expense_id, price) {
      $('#edit_entry_id').val(id);
      $('#edit_expense_id').val(expense_id);
      $('#edit_price').val(price);
      $('#edit_is_approved').prop('checked', false); // Default unchecked as it is pending list
      $('#editEntryModal').modal('show');
  }

  $('#editEntryForm').submit(function(e) {
      e.preventDefault();
      let id = $('#edit_entry_id').val();
      $.ajax({
          url: `/petty-cash/${id}`,
          method: 'PUT',
          data: $(this).serialize(),
          success: function(response) {
              $('#editEntryModal').modal('hide');
              loadData(); // Reload table
              loadStats(); // Reload stats after update
          },
          error: function(err) {
              alert('Error updating entry');
          }
      });
  });

  // Approve Single
  window.approveEntry = function(btn, id) {
      let originalContent = $(btn).html();
      $(btn).html('<i class="bi bi-arrow-repeat spin"></i>').prop('disabled', true);

      $.post(`/petty-cash/${id}/toggle-approval`, {
          _token: '<?php echo e(csrf_token()); ?>'
      }, function(response) {
          loadData();
          loadStats();
      }).fail(function() {
          $(btn).html(originalContent).prop('disabled', false);
          alert('Failed to approve');
      });
  }
  
  // Delete
  window.deleteEntry = function(id) {
    if(confirm('Are you sure you want to delete this entry?')) {
      $.ajax({
        url: `/petty-cash/${id}`,
        type: 'DELETE',
        data: { _token: '<?php echo e(csrf_token()); ?>' },
        success: function(res) {
          loadData();
          loadStats();
        }
      });
    }
  }

  // Handle Confirm Click
  $('#btnConfirmAction').click(function() {
      if (pendingAction) {
          pendingAction();
      }
  });

  // Bulk Approve
  $('#btnApproveSelected').click(function() {
      let ids = [];
      $('.row-checkbox:checked').each(function() {
          ids.push($(this).val());
      });
      if(ids.length === 0) return;

      $('#confirmationMessage').text(`Approve ${ids.length} entries?`);
      pendingAction = function() {
          $.post("<?php echo e(route('petty-cash.approve-bulk')); ?>", {
              _token: '<?php echo e(csrf_token()); ?>',
              ids: ids
          }, function(res) {
              loadData();
              loadStats();
              $('#confirmationModal').modal('hide');
          });
      };
      
      $('#confirmationModal').modal('show');
  });



  // Initialization calls
  loadData();
  loadExpenses();
  loadStats();

  let searchTimer;
  $('#filter_search').on('input', function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
      loadData(1);
    }, 500);
  });

  $('#filter_expense, #filter_from_date, #filter_to_date, #filter_month').on('change', function() {
    loadData(1);
  });

  $('#checkAll').on('change', function() {
      $('.row-checkbox').prop('checked', $(this).prop('checked'));
      toggleBulkButtons();
  });

  $(document).on('change', '.row-checkbox', function() {
      toggleBulkButtons();
      let allChecked = $('.row-checkbox').length === $('.row-checkbox:checked').length;
      $('#checkAll').prop('checked', allChecked);
  });

});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/pettycash/approvals.blade.php ENDPATH**/ ?>