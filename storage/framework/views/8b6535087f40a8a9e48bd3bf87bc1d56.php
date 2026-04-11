

<?php $__env->startSection('title', 'Subscription History'); ?>
<?php $__env->startSection('page_title', 'Subscription History'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
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
    margin-bottom: 0;
    background: transparent;
  }

  .data-table-card .table-scroll::-webkit-scrollbar {
    height: 8px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-track {
    background: #e4e7ec;
    border-radius: 999px;
  }

  .data-table-card .table-scroll::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 999px;
  }

  /* Matching Followup Table Styling */
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
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.65rem 0.75rem;
    color: #000;
    border-bottom: 1px solid #f4f4f6;
    text-align: left;
    background: transparent;
    white-space: nowrap;
    font-family: Montserrat;
  }

  .data-table-card .custom-table tbody tr {
    transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .data-table-card .custom-table tbody tr:hover {
    background: #f8f9ff;
    box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08);
    transform: translateY(-1px);
  }

  .data-table-card .custom-table tbody tr:last-child td {
    border-bottom: none;
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #434AFA;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    transition: all 0.3s ease;
  }

  .back-btn:hover {
    color: #2e35d2;
    transform: translateX(-3px);
    text-decoration: none;
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
  /* Badge styles to match index */
  .status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    font-family: Montserrat, sans-serif;
  }
  .status-badge.active { background: #d1fae5; color: #065f46; }
  .status-badge.expired { background: #fee2e2; color: #991b1b; }
  .status-badge.cancelled { background: #f3f4f6; color: #374151; }
  .status-badge.pending { background: #fef3c7; color: #92400e; }
  .status-badge.suspended { background: #fef3c7; color: #92400e; }
  .status-badge.n-a { background: #f3f4f6; color: #374151; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <a href="<?php echo e(route('subscriptions.index')); ?>" class="back-btn">
      <i class="bi bi-arrow-left"></i> Back to Subscriptions
    </a>
  </div>

  <div class="card mb-3" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 8px;">
    <div class="card-body p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0" style="font-weight: 700; color: #111827; font-family: Montserrat;">Billing History</h5>
        <?php if($subscription->notes): ?>
            <button class="btn btn-sm btn-light text-primary border-0" type="button" data-bs-toggle="modal" data-bs-target="#notesModal" title="View Notes">
                <i class="bi bi-info-circle fs-6"></i>
            </button>
        <?php endif; ?>
      </div>

      
      
      <div class="row g-3" style="font-size: 0.9rem;">
          <div class="col-md">
              <div class="text-muted small text-uppercase fw-bold mb-1">Customer</div>
              <div class="fw-semibold text-dark"><?php echo e($subscription->customer->name ?? $subscription->subscription_name); ?></div>
          </div>
          <div class="col-md">
              <div class="text-muted small text-uppercase fw-bold mb-1">Product</div>
              <div class="fw-semibold text-dark"><?php echo e($subscription->product->product_name ?? 'N/A'); ?></div>
          </div>
          <div class="col-md">
               <div class="text-muted small text-uppercase fw-bold mb-1">Recurrence</div>
               <div class="fw-semibold text-dark text-capitalize">
                   <?php if($subscription->is_recurring): ?>
                       <?php echo e($subscription->recurrence_type ?? 'Recursive'); ?>

                   <?php else: ?>
                       One Time
                   <?php endif; ?>
               </div>
          </div>
          <div class="col-md">
              <div class="text-muted small text-uppercase fw-bold mb-1">Billing Type</div>
              <div class="fw-semibold text-dark"><?php echo e($subscription->billing_type ?? 'N/A'); ?></div>
          </div>
          <div class="col-md">
               <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
               <div>
                   <?php if($subscription->is_active): ?>
                       <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Active</span>
                   <?php else: ?>
                       <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill">Inactive</span>
                   <?php endif; ?>
               </div>
          </div>
      </div>
    </div>
  </div>

  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <div class="table-scroll">
        <table class="table custom-table" id="history_table">
          <thead>
            <tr>
              <th>Period Start</th>
              <th>Period End</th>
              <th>Due Date</th>
              <th>Amount</th>

              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e(\Carbon\Carbon::parse($history->period_start)->format('d/m/Y')); ?></td>
              <td><?php echo e($history->period_end ? \Carbon\Carbon::parse($history->period_end)->format('d/m/Y') : 'N/A'); ?></td>
              <td><?php echo e($history->due_date ? \Carbon\Carbon::parse($history->due_date)->format('d/m/Y') : 'N/A'); ?></td>
              <td>₹<?php echo e(number_format($history->amount, 2)); ?></td>

              <td>
                <?php
                    $hStatus = $history->status ?? '';
                ?>
                <select class="form-control form-control-sm status-select" data-history-id="<?php echo e($history->id); ?>" data-current-status="<?php echo e($hStatus); ?>" style="width: auto; min-width: 140px; font-size: 0.8rem;">
                    <option value="<?php echo e($hStatus); ?>" selected><?php echo e($hStatus ?: 'Select Status'); ?></option>
                </select>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="5" class="text-center py-4">
                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                No billing history found for this subscription.
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="table-range-meta mt-2" id="historyRangeInfo">
    Showing 0-0 from 0 data
  </div>
</div>

<div class="mt-2 d-flex justify-content-center">
  <ul class="pagination" id="paginationLinks"></ul>
</div>

<!-- Notes Modal -->
<?php if($subscription->notes): ?>
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #434afa; color: white;">
        <h5 class="modal-title" id="notesModalLabel">Subscription Notes</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0 text-muted"><?php echo e($subscription->notes); ?></p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentPage = 1;
let globalStatuses = [];

function buildPagination($container, links) {
    $container.empty();
    if (!links || links.length === 0) return;

    let html = '';
    links.forEach(link => {
        if (link.url !== null) {
            let pageParam = '1';
            try {
                 let parts = link.url.split('page=');
                 if(parts.length > 1) pageParam = parts[1].split('&')[0];
            } catch(e) { }

            html += `<li class="page-item ${link.active ? 'active' : ''}">
                <a href="#" class="page-link" data-page="${pageParam}">${link.label}</a>
            </li>`;
        } else {
            html += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
        }
    });
    $container.html(html);
}

function updateRangeInfo(from, to, total) {
    const $info = $('#historyRangeInfo');
    if (!$info.length) return;

    const safeTotal = Number(total) || 0;
    const safeFrom = safeTotal === 0 ? 0 : (Number(from) || 0);
    const safeTo = safeTotal === 0 ? 0 : (Number(to) || 0);

    $info.text(`Showing ${safeFrom.toLocaleString('en-IN')}-${safeTo.toLocaleString('en-IN')} from ${safeTotal.toLocaleString('en-IN')} data`);
}

function formatDate(value) {
    if (!value) return 'N/A';
    const d = new Date(value);
    if (isNaN(d.getTime())) return value;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatDateTime(value) {
    if (!value) return 'N/A';
    const d = new Date(value);
    if (isNaN(d.getTime())) return value;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    let hours = d.getHours();
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    return `${day}/${month}/${year} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
}

function getStatusOptionsHtml(currentStatus) {
    if (!globalStatuses.length) return `<option value="${currentStatus}" selected>${currentStatus || 'Loading...'}</option>`;
    
    let options = '';
    globalStatuses.forEach(s => {
        // Handle potentially different property names or raw strings
        const name = s.status_name || s.name || s; 
        const isSelected = name === currentStatus ? 'selected' : '';
        options += `<option value="${name}" ${isSelected}>${name}</option>`;
    });
    return options;
}

function loadHistory(page = 1) {
    $.ajax({
        url: '<?php echo e(route("subscriptions.history", $subscription->id)); ?>?page=' + page,
        type: 'GET',
        success: function (data) {
            let html = '';
            if (data.data.length === 0) {
                html = '<tr><td colspan="4" class="text-center py-4"><i class="bi bi-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>No billing history found.</td></tr>';
            } else {
                data.data.forEach(function (history) {
                    const start = formatDate(history.period_start);
                    const periodEnd = formatDate(history.period_end);
                    const dueDate = formatDate(history.due_date);
                    const processed = formatDateTime(history.created_at);
                    const amount = parseFloat(history.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });
                    const status = history.status || '';
                    
                    const statusDropdown = `
                        <select class="form-control form-control-sm status-select" data-history-id="${history.id}" data-current-status="${status}" style="width: auto; min-width: 140px; font-size: 0.8rem;">
                            ${getStatusOptionsHtml(status)}
                        </select>
                    `;

                    html += `
                        <tr>
                            <td>${start}</td>
                            <td>${periodEnd}</td>
                            <td>${dueDate}</td>
                            <td>₹${amount}</td>
                            <td>${statusDropdown}</td>
                        </tr>
                    `;
                });
            }

            $('#history_table tbody').html(html);
            buildPagination($('#paginationLinks'), data.links);
            updateRangeInfo(data.from, data.to, data.total);
            currentPage = data.current_page;
        },
        error: function (xhr) {
            console.error("Error loading history:", xhr.responseText);
        }
    });
}

function populateAllDropdowns() {
    $('.status-select').each(function() {
        const $select = $(this);
        const current = $select.data('current-status');
        $select.html(getStatusOptionsHtml(current));
    });
}

$(document).on('click', '#paginationLinks .page-link', function (e) {
    e.preventDefault();
    const page = $(this).data('page');
    if (page && page !== currentPage) {
        loadHistory(page);
    }
});

$(document).ready(function() {
    // Initial pagination setup
    updateRangeInfo(<?php echo e($histories->firstItem() ?? 0); ?>, <?php echo e($histories->lastItem() ?? 0); ?>, <?php echo e($histories->total()); ?>);
    buildPagination($('#paginationLinks'), <?php echo json_encode($histories->toArray()['links'], 15, 512) ?>);

    // Load statuses and then populate dropdowns
    $.ajax({
        url: '<?php echo e(route("subscription-status.list")); ?>',
        type: 'GET',
        success: function(data) {
            if(Array.isArray(data)) {
                globalStatuses = data;
                populateAllDropdowns();
            } else if (data && data.data && Array.isArray(data.data)) {
                 // Handle paginated response if applicable, though unlikely for a list endpoint
                 globalStatuses = data.data;
                 populateAllDropdowns();
            }
        },
        error: function(err) {
            console.error('Failed to load statuses', err);
        }
    });

    // Handle Status Change on specific row
    $(document).on('change', '.status-select', function() {
        const $select = $(this);
        const newStatus = $select.val();
        const historyId = $select.data('history-id');
        
        $select.prop('disabled', true); // Prevent double submission

        $.ajax({
            url: '<?php echo e(route("subscriptions.update-status", $subscription->id)); ?>',
            type: 'PATCH',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                status: newStatus,
                history_id: historyId
            },
            success: function(response) {
                // Update dates in the row if they changed (unlikely unless it's the latest row and 'Last Payment Received')
                // For simplicity and correctness, refreshing the list is often best, 
                // but let's try to be smart.
                // If end_date returned and valid, update UI?
                // Actually user said "update same row".
                // Let's just re-enable.
                $select.prop('disabled', false);
                $select.data('current-status', newStatus);
                
                // Optional: Flash success
                // If end_date changed? The response sends it.
                // It's hard to target the specific TD for end date without row context.
                // Since this is rare, we can just reload the history table to be safe
                loadHistory(currentPage);
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('Error updating status');
                $select.prop('disabled', false);
                $select.val($select.data('current-status')); // Revert
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/subscription/history.blade.php ENDPATH**/ ?>