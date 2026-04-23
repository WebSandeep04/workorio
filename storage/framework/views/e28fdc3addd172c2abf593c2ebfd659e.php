<?php $__env->startSection('title', 'Shifts'); ?>
<?php $__env->startSection('page_title', 'Shifts'); ?>

<?php $__env->startPush('styles'); ?>
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
  }
  .table-search-btn:hover { background: #3538d4; }
  .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; }
  
  .data-table-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08);
  }
  .table-responsive { padding: 0.5rem 0.75rem 1rem; overflow-x: auto; }
  .custom-table { border-spacing: 0; width: 100%; font-size: 0.85rem; }
  .custom-table thead th { background: #fff; font-size: 0.65rem; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; }
  .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; border-bottom: 1px solid #f4f4f6; }
  .custom-table tbody tr:hover { background: #f8f9ff; }
  
  .btn-action { background: transparent; border: none; padding: 0.25rem 0.5rem; }
  .btn-action-edit { color: white; background: #343AFA; border-radius: 4px; }
  .btn-action-delete { color: white; background: #343AFA; border-radius: 4px; }
  
  .modal-content { border-radius: 0px !important; border: none; }
  .modal-header { background: #434AFA; color: white; border-bottom: none; }
  .modal-footer { border-top: 1px solid #f0f0f0; background: #fff; }
  .form-label-modern { color: #434AFA; font-weight: 600; font-size: 0.85rem; }
  .form-control-modern { border: 1px solid #e0e0e0; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.9rem; }
  
  .badge-active { background: #dcfce7; color: #166534; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem; }
  .badge-inactive { background: #fee2e2; color: #991b1b; padding:0.2rem 0.4rem; border-radius:4px; font-size:0.75rem;}

  .spin {
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
  <div class="table-search mb-2">
    <div class="table-search-field">
        <i class="bi bi-search"></i>
        <input type="text" id="search" placeholder="Search shifts..." />
    </div>
    <button class="table-search-btn" id="addBtn">
        <i class="bi bi-plus me-1"></i>Add
    </button>
  </div>

  <div class="data-table-card">
    <div class="table-responsive">
      <table class="table custom-table" id="mainTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Late (min)</th>
            <th>SL Start (H)</th>
            <th>SL End (H)</th>
            <th>Week Offs</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="9" class="text-center text-muted py-3">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Unified Form Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-size: 1.1rem; font-weight: 600;" id="modalTitle">
          Create Shift
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="mainForm">
        <div class="modal-body bg-white pt-4 pb-4">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_id" name="id">
          
          <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label-modern">Shift Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-modern" id="name" name="name" required placeholder="General, Night, etc.">
              </div>
              <div class="col-md-4">
                <label class="form-label-modern">Start Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-modern" id="start_time" name="start_time" required>
              </div>
              <div class="col-md-4">
                <label class="form-label-modern">End Time <span class="text-danger">*</span></label>
                <input type="time" class="form-control form-control-modern" id="end_time" name="end_time" required>
              </div>

              <div class="col-md-3">
                <label class="form-label-modern">Late Allowed (Min)</label>
                <input type="number" class="form-control form-control-modern" id="late_min" name="late_min" min="0" value="15">
              </div>
              <div class="col-md-3">
                <label class="form-label-modern">SL Start Limit (H)</label>
                <input type="number" class="form-control form-control-modern" id="sl_start_limit" name="sl_start_limit" min="0" value="0">
              </div>
              <div class="col-md-3">
                <label class="form-label-modern">SL End Limit (H)</label>
                <input type="number" class="form-control form-control-modern" id="sl_end_limit" name="sl_end_limit" min="0" value="0">
              </div>
              <div class="col-md-3">
                <label class="form-label-modern">Status</label>
                <select id="is_active" name="is_active" class="form-control form-control-modern">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
              </div>
          </div>

          <div class="row g-3 mb-2">
            <div class="col-12">
                <label class="form-label-modern">Weekly Offs</label>
                <div class="d-flex flex-wrap gap-3 mt-1">
                    <?php $__currentLoopData = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-check">
                        <input class="form-check-input week-off-checkbox" type="checkbox" name="week_offs[]" value="<?php echo e($index); ?>" id="day_<?php echo e($index); ?>">
                        <label class="form-check-label" for="day_<?php echo e($index); ?>" style="font-size: 0.85rem;">
                            <?php echo e($day); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn table-search-btn w-100 justify-content-center" id="saveBtn">
            <i class="bi bi-check-circle"></i> Save Shift Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function() {
    let rawData = [];
    
    function formatTime(val) {
        if(!val) return '-';
        return val.substring(0, 5);
    }

    function formatWeekOffs(offs) {
        if(!offs || !Array.isArray(offs) || offs.length === 0) return 'None';
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        return offs.map(i => days[i]).join(', ');
    }

    function loadData() {
        let search = $('#search').val().toLowerCase();
        $.get("<?php echo e(route('shifts.list')); ?>", function(res) {
            rawData = res;
            renderTable(search);
        });
    }

    function renderTable(search = '') {
        let tbody = $('#mainTable tbody');
        tbody.empty();

        let filtered = rawData.filter(item => {
            return (item.name || '').toLowerCase().includes(search);
        });

        if(filtered.length === 0) {
            tbody.html(`<tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox fs-4"></i><br>No matching records</td></tr>`);
            return;
        }

        $.each(filtered, function(i, row) {
            let statusBadge = row.is_active ? 'badge-active' : 'badge-inactive';
            let statusText = row.is_active ? 'Active' : 'Inactive';
            
            let html = `
                <tr>
                    <td><strong style="color: #434AFA;">${row.name}</strong></td>
                    <td>${formatTime(row.start_time)}</td>
                    <td>${formatTime(row.end_time)}</td>
                    <td>${row.late_min || '0'} Min</td>
                    <td>${row.sl_start_limit || '0'}h</td>
                    <td>${row.sl_end_limit || '0'}h</td>
                    <td>${formatWeekOffs(row.week_offs)}</td>
                    <td><span class="${statusBadge}">${statusText}</span></td>
                    <td class="text-center">
                        <button class="btn-action btn-action-edit editBtn" data-id="${row.id}" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn-action btn-action-delete deleteBtn" data-id="${row.id}" title="Delete"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    $('#search').on('keyup', function() {
        renderTable($(this).val().toLowerCase());
    });

    $('#addBtn').on('click', function() {
        $('#modalTitle').html('<i class="bi bi-plus text-white"></i> Create Shift');
        $('#mainForm')[0].reset();
        $('.week-off-checkbox').prop('checked', false);
        $('#edit_id').val('');
        $('#formModal').modal('show');
    });

    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        let row = rawData.find(r => r.id == id);
        
        $('#modalTitle').html('<i class="bi bi-pencil-square text-white"></i> Edit Shift');
        $('#mainForm')[0].reset();
        $('.week-off-checkbox').prop('checked', false);
        
        $('#edit_id').val(row.id);
        $('#name').val(row.name);
        $('#start_time').val(formatTime(row.start_time));
        $('#end_time').val(formatTime(row.end_time));
        $('#late_min').val(row.late_min);
        $('#sl_start_limit').val(row.sl_start_limit || 0);
        $('#sl_end_limit').val(row.sl_end_limit || 0);
        $('#is_active').val(row.is_active ? '1' : '0');

        if(row.week_offs && Array.isArray(row.week_offs)) {
            row.week_offs.forEach(i => {
                $(`#day_${i}`).prop('checked', true);
            });
        }

        $('#formModal').modal('show');
    });

    $('#mainForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let url = id ? `/shifts/${id}` : "<?php echo e(route('shifts.store')); ?>";
        let type = id ? 'PUT' : 'POST';
        
        let $btn = $('#saveBtn');
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Saving...');

        $.ajax({
            url: url,
            type: type,
            data: $(this).serialize(),
            success: function(res) {
                $('#formModal').modal('hide');
                loadData();
                alert(res.message);
            },
            error: function(xhr) {
                let msg = 'An error occurred';
                if(xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                alert(msg);
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Shift Settings');
            }
        });
    });

    $(document).on('click', '.deleteBtn', function() {
        if(confirm('Are you absolutely sure you want to delete this shift?')) {
            $.ajax({
                url: `/shifts/${$(this).data('id')}`,
                type: 'DELETE',
                data: { _token: '<?php echo e(csrf_token()); ?>' },
                success: function() {
                    loadData();
                }
            });
        }
    });

    loadData();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/master/shifts.blade.php ENDPATH**/ ?>