<?php $__env->startSection('title', 'Leave Types'); ?>
<?php $__env->startSection('page_title', 'Leave Types'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
  .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; }
  .table-search-btn { padding: 0.35rem 1rem; background: #434AFA; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; }
  .table-search-btn:hover { background: #3538d4; color: white; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; }
  .modern-card { padding: 0; margin-bottom: 0.5rem; }
  .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; overflow: hidden; }
  .table thead th { background: #f8fafc; color: #475569; font-size: 0.75rem; font-weight: 600; padding: 0.5rem 0.75rem; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
  .table tbody td { padding: 0.65rem 0.75rem; vertical-align: middle; font-size: 0.82rem; color: #334155; border-bottom: 1px solid #e2e8f0; font-weight: normal; } /* Normal font weight as requested */
  .badge { padding: 0.25rem 0.5rem; border-radius: 12px; font-weight: 500; font-size: 0.75rem; }
  .badge-active { background: #dcfce7; color: #166534; }
  .badge-inactive { background: #fee2e2; color: #991b1b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Search & Actions -->
            <div class="table-search">
                <div class="table-search-field">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search leave types..." autocomplete="off">
                </div>
                <button type="button" class="table-search-btn" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-lg"></i> Add Leave Type
                </button>
            </div>

            <!-- Table Card -->
            <div class="modern-card data-table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Paid Status</th>
                                <th class="text-center">Color Code</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-2" id="paginationContainer"></div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Leave Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="mainForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color Code</label>
                        <input type="color" class="form-control form-control-color w-100" name="color_code" id="color_code" value="#434AFA" title="Choose your color">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type (Paid/Unpaid)</label>
                        <select class="form-select" name="is_paid" id="is_paid">
                            <option value="1">Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let currentPage = 1;

    function applyFilters() {
        currentPage = 1;
        fetchData();
    }

    $('#searchInput').on('keyup', function() {
        applyFilters();
    });

    function fetchData(page = 1) {
        currentPage = page;
        let search = $('#searchInput').val();
        
        $.ajax({
            url: "<?php echo e(route('leave-type.fetch')); ?>",
            type: "GET",
            data: { page: page, search: search },
            success: function(response) {
                let html = '';
                if(response.data.length > 0) {
                    $.each(response.data, function(index, item) {
                        html += `
                            <tr>
                                <td>${item.name}</td>
                                <td class="text-center">
                                    <span class="badge ${item.is_paid ? 'bg-success' : 'bg-danger'}">${item.is_paid ? 'Paid' : 'Unpaid'}</span>
                                </td>
                                <td class="text-center">
                                    ${item.color_code ? `<span style="display:inline-block; width:20px; height:20px; border-radius:50%; background-color:${item.color_code}"></span>` : '-'}
                                </td>
                                <td class="text-center">
                                    <span class="badge ${item.status ? 'badge-active' : 'badge-inactive'}">${item.status ? 'Active' : 'Inactive'}</span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light btn-edit" data-item='${JSON.stringify(item)}'><i class="bi bi-pencil"></i></button>
                                    <button class="btn btn-sm btn-light text-danger btn-delete" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                    renderPagination(response);
                } else {
                    html = '<tr><td colspan="5" class="text-center py-4 text-muted">No leave types found. Please run the migration first if this is a fresh database.</td></tr>';
                    $('#paginationContainer').html('');
                }
                $('#tableBody').html(html);
            },
            error: function() {
                $('#tableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data. Try again.</td></tr>');
            }
        });
    }

    function renderPagination(response) {
        let html = '<ul class="pagination pagination-sm justify-content-end">';
        if (response.prev_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page - 1}">Prev</a></li>`;
        }
        for (let i = 1; i <= response.last_page; i++) {
            html += `<li class="page-item ${response.current_page === i ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        if (response.next_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page + 1}">Next</a></li>`;
        }
        html += '</ul>';
        $('#paginationContainer').html(html);
    }

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        fetchData($(this).data('page'));
    });

    fetchData(); // Initial load

    // Setup Form
    $('#createModal').on('show.bs.modal', function() {
        if(!$('#edit_id').val()) {
            $('#mainForm')[0].reset();
            $('#modalTitle').text('Add Leave Type');
        }
    });

    $('#createModal').on('hidden.bs.modal', function() {
        $('#edit_id').val('');
    });

    $(document).on('click', '.btn-edit', function() {
        let item = $(this).data('item');
        $('#edit_id').val(item.id);
        $('#name').val(item.name);
        $('#is_paid').val(item.is_paid);
        $('#color_code').val(item.color_code);
        $('#status').val(item.status);
        $('#modalTitle').text('Edit Leave Type');
        $('#createModal').modal('show');
    });

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = id ? `/leave-type/${id}` : "<?php echo e(route('leave-type.store')); ?>";
        let type = id ? 'PUT' : 'POST';
        let data = $(this).serialize();

        let btn = $('#saveBtn');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function(response) {
                if(response.success) {
                    $('#createModal').modal('hide');
                    alert(id ? 'Leave type updated successfully' : 'Leave type created successfully');
                    fetchData(currentPage);
                } else {
                    alert(response.message || 'Error saving data.');
                }
            },
            error: function(xhr) {
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    alert(Object.values(xhr.responseJSON.errors).join('\n'));
                } else {
                    alert(xhr.responseJSON?.message || 'Something went wrong');
                }
            },
            complete: function() {
                btn.prop('disabled', false).text('Save changes');
            }
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        if(confirm('Are you sure you want to delete this leave type?')) {
            $.ajax({
                url: `/leave-type/${id}`,
                type: 'DELETE',
                data: { _token: "<?php echo e(csrf_token()); ?>" },
                success: function(response) {
                    fetchData(currentPage);
                }
            });
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/software-setup/leave-type/index.blade.php ENDPATH**/ ?>