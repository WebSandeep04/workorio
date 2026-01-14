

<?php $__env->startSection('title', 'Form Builder'); ?>
<?php $__env->startSection('page_title', 'Form Builder - Lead Forms'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  /* Import fonts */
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

  /* Global font family */
  body {
    font-family: 'Montserrat', sans-serif !important;
    background-color: #f4f5f7;
  }

  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }

  /* Summary Card CSS matching todayfollowups */
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
    padding: 0.5rem;
    box-shadow: 0px 4px 4px 0px #0000000A;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    width: 100%;
    min-height: 70px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0px 8px 8px 0px #0000000A;
  }

  .summary-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .summary-card-icon img {
    width: 24px;
    height: 24px;
    object-fit: contain;
  }
  
  .icon-violet { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }

  .summary-card-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
    min-width: 0;
  }

  .summary-card-label {
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
    color: #000;
    flex-shrink: 0;
    line-height: 1.2;
    font-family: Montserrat;
  }

  .summary-card-value {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
    display: flex;
    align-items: center;
    line-height: 1;
    color: #101828;
    font-family: Montserrat;
  }

  /* Table Search */
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
    padding: 0.35rem 1.25rem;
    background: #434AFA;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .table-search-btn:hover {
    background: #3538d4;
    color: white;
    box-shadow: 0 2px 5px rgba(67, 74, 250, 0.3);
  }

  /* Table CSS */
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
    font-family: Montserrat;
  }

  .data-table-card .modern-card-body {
    padding: 0;
  }

  .data-table-card .table-responsive {
    border-radius: 5px;
    border: none;
    box-shadow: none;
    padding: 0.5rem 0.75rem 1rem;
    overflow-x: auto;
    background: transparent;
  }
  
  .data-table-card .table-responsive::-webkit-scrollbar {
    height: 8px;
    background-color: #f1f5f9;
    border-radius: 4px;
  }

  .data-table-card .table-responsive::-webkit-scrollbar-thumb {
    background: #434AFA;
    border-radius: 4px;
  }
  
  .data-table-card .custom-table {
    border-collapse: separate;
    border-spacing: 0 0;
    width: 100%;
    font-size: 0.85rem;
    background: transparent;
    table-layout: auto;
    border-spacing: 3px 0;
  }

  .data-table-card .custom-table thead th {
    background: #ffffff;
    color: #000;
    font-size: 0.65rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-weight: 700;
    padding: 0.75rem 0.5rem;
    text-align: left;
    border: none;
    position: sticky;
    top: 0;
    z-index: 5;
    white-space: nowrap;
    font-family: Montserrat, sans-serif;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border-radius: 3px;
    margin-bottom: 5px;
    border-right: 1px solid #f1f3f5;
  }
  
  .data-table-card .custom-table thead th:last-child {
    border-right: none;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.85rem;
    padding: 0.75rem 0.5rem;
    color: #333;
    background: #ffffff;
    white-space: nowrap;
    vertical-align: middle;
    font-family: Montserrat, sans-serif;
    border-bottom: 1px solid #f1f5f9;
  }
  
  .data-table-card .custom-table tbody tr:hover td {
    background-color: #f8faff;
  }
  
  /* Action Buttons (User Specific) */
  .action-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: #434AFA;
    border: 1px solid transparent;
    color: #ffffff;
    font-size: 0.9rem;
    margin-right: 4px;
    transition: all 0.2s ease;
  }
  .action-btn:hover {
    background-color: #3538d4;
    color: white;
    transform: translateY(-1px);
  }
  
  .badge-field-count {
        background-color: #e0e7ff; 
        color: #434afa;
        font-weight: 500;
        padding: 0.35em 0.65em;
        border-radius: 4px;
        font-size: 0.75rem;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
  <?php if(!empty($needs_migration)): ?>
  <div class="alert alert-warning d-flex align-items-center mb-2" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <div>
          Form Builder table is not created yet. Please run <code>php artisan migrate</code> and reload this page.
      </div>
  </div>
  <?php endif; ?>

  <!-- Summary Cards -->
  <div class="summary-cards mb-3">
    <div class="summary-card card-1" style="max-width: 250px;">
      <div class="summary-card-icon icon-violet">
         <!-- Using static image as requested -->
        <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Total Forms" onerror="this.onerror=null;this.src='<?php echo e(asset('img/icons/file.png')); ?>';">
      </div>
      <div class="summary-card-content">
        <div class="summary-card-label">TOTAL FORMS</div>
        <div class="summary-card-value"><?php echo e($forms->total()); ?></div>
      </div>
    </div>
  </div>

  <!-- Top Action Bar -->
  <div class="table-search mb-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="search" placeholder="Search forms..." onkeyup="filterForms()" />
    </div>
    <a href="<?php echo e(route('formbuilder.create')); ?>" class="table-search-btn">
      <i class="bi bi-plus me-1"></i>Add
    </a>
  </div>

  <!-- Data Table -->
  <div class="modern-card data-table-card">
    <div class="modern-card-body">
      <?php if($forms->count() > 0): ?>
      <div class="table-responsive">
        <table class="table custom-table" id="formsTable">
          <thead>
            <tr>
              <th class="text-start ps-3">Form Name</th>
              <th>Fields Count</th>
              <th>Created At</th>
              <th>Last Updated</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td class="text-start ps-3">
                <div class="fw-semibold text-dark"><?php echo e($form->name); ?></div>
              </td>
              <td>
                <span class="badge badge-field-count"><?php echo e(count($form->fields ?? [])); ?> fields</span>
              </td>
              <td><?php echo e($form->created_at->format('d M Y')); ?></td>
              <td>
                <?php echo e($form->updated_at->format('d M Y')); ?> <small class="text-muted"><?php echo e($form->updated_at->format('h:i A')); ?></small>
              </td>
              <td>
                <a href="<?php echo e(route('formbuilder.view', $form->id)); ?>" class="action-btn" title="View & Embed" data-bs-toggle="tooltip">
                    <i class="bi bi-code-slash"></i>
                </a>
                <a href="<?php echo e(route('formbuilder.config', $form->id)); ?>" class="action-btn" title="Database Config" data-bs-toggle="tooltip">
                    <i class="bi bi-database-gear"></i>
                </a>
                <a href="<?php echo e(route('formbuilder.edit', $form->id)); ?>" class="action-btn" title="Edit Form" data-bs-toggle="tooltip">
                    <i class="bi bi-pencil"></i>
                </a>
                <button type="button" class="action-btn btn-delete web-ripple" onclick="deleteForm(<?php echo e($form->id); ?>, '<?php echo e($form->name); ?>')" title="Delete" data-bs-toggle="tooltip">
                    <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-2 text-muted small ms-1">
       Page <?php echo e($forms->currentPage()); ?> of <?php echo e($forms->lastPage()); ?> • Showing <?php echo e($forms->firstItem()); ?>-<?php echo e($forms->lastItem()); ?> of <?php echo e($forms->total()); ?>

  </div>

  <div class="mt-2 d-flex justify-content-center">
    <nav>
        <ul class="pagination pagination-sm mb-0">
             <li class="page-item <?php echo e($forms->onFirstPage() ? 'disabled' : ''); ?>">
                 <a class="page-link" href="<?php echo e($forms->previousPageUrl()); ?>"><i class="bi bi-chevron-left"></i> Previous</a>
             </li>
             <li class="page-item active">
                 <span class="page-link"><?php echo e($forms->currentPage()); ?> / <?php echo e($forms->lastPage()); ?></span>
             </li>
             <li class="page-item <?php echo e($forms->hasMorePages() ? '' : 'disabled'); ?>">
                 <a class="page-link" href="<?php echo e($forms->nextPageUrl()); ?>">Next <i class="bi bi-chevron-right"></i></a>
             </li>
        </ul>
     </nav>
  </div>
      <?php else: ?>
      <div class="text-center py-5">
        <div class="mb-3">
            <i class="bi bi-clipboard-plus text-primary opacity-25" style="font-size: 4rem;"></i>
        </div>
        <h5 class="text-secondary fw-normal">No forms created yet</h5>
        <p class="text-muted small mb-4">Create your first lead form to get started.</p>
        <a href="<?php echo e(route('formbuilder.create')); ?>" class="table-search-btn">
            Create Form
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function filterForms() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("search");
    filter = input.value.toUpperCase();
    table = document.getElementById("formsTable");
    if (!table) return;
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        // Search in the Name column (index 0)
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }       
    }
}

async function deleteForm(id, name) {
    if (!confirm(`Are you sure you want to delete "${name}"?`)) {
        return;
    }
    
    try {
        const res = await fetch(`<?php echo e(url('/form-builder')); ?>/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': `<?php echo e(csrf_token()); ?>`
            }
        });
        
        const data = await res.json();
        if (res.ok && data.success) {
            // alert('Form deleted successfully'); // Optional: nicer UI notifications
            location.reload();
        } else {
            throw new Error(data.message || 'Failed to delete form');
        }
    } catch(e) {
        alert('Error: ' + e.message);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\leadmanagement (akrati ui work)\resources\views/formbuilder/index.blade.php ENDPATH**/ ?>