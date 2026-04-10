<?php $__env->startSection('title', 'Lead Lists'); ?>
<?php $__env->startSection('page_title', 'List Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .calling-page {
        padding: 0.5rem;
        background: #f7f8fc;
        min-height: calc(100vh - 110px);
        display: flex;
        flex-direction: column;
    }

    /* Hero Metrics */
    .hero-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .hero-metric-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef3;
        padding: 0.75rem 1rem;
        width: 100%;
        box-shadow: 0px 4px 4px 0px #0000000A;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: all 0.3s ease;
    }

    .hero-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0px 8px 8px 0px #0000000A;
    }

    .hero-metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-sky { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .icon-indigo { background: linear-gradient(135deg, #434AFA, #667eea); }
    .icon-teal { background: linear-gradient(135deg, #0ea5e9, #2dd4bf); }

    .hero-metric-icon i {
        color: #fff;
        font-size: 1.2rem;
    }

    .hero-metric-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex-grow: 1;
        min-width: 0;
    }

    .metric-label {
        display: block;
        font-size: 0.65rem;
        color: #000;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.2rem;
        font-weight: 600;
        font-family: Montserrat;
    }

    .metric-value {
        font-size: 1.2rem;
        font-weight: 700;
        line-height: 1.2;
        display: block;
        color: #101828;
        font-family: Montserrat;
    }

    /* Table System */
    .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; margin-bottom: 1rem; flex-grow: 1; display: flex; flex-direction: column; }
    .table-scroll { width: 100%; overflow-x: auto; padding: 0.5rem 0.75rem 1rem; flex-grow: 1; }
    .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-family: Montserrat; }
    .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important; }
    .custom-table tbody td { font-size: 0.85rem; padding: 0.6rem 0.75rem; color: #1f2937; border-bottom: 1px solid #f4f4f6; white-space: nowrap; }
    .custom-table tbody tr:hover { background: #f8f9ff; transform: translateY(-1px); }

    .btn-create-list { 
        background: #434AFA; 
        color: #fff !important; 
        border: none; 
        padding: 0.5rem 1rem; 
        border-radius: 6px; 
        font-weight: 700; 
        font-size: 0.75rem; 
        display: inline-flex; 
        align-items: center; 
        gap: 0.5rem; 
        transition: all 0.2s; 
        text-decoration: none; 
        font-family: Montserrat;
    }
    .btn-create-list:hover { background: #3339d6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2); }

    .badge-records { 
        background: #f1f5ff; 
        color: #434afa; 
        padding: 0.25rem 0.6rem; 
        border-radius: 4px; 
        font-weight: 700; 
        font-size: 0.7rem; 
    }
    
    .list-name { font-weight: 700; color: #101828; }
    .list-date { font-size: 0.75rem; color: #667085; }

    .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; font-family: Montserrat; }
    .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }

    .table-range-meta { font-size: 0.75rem; color: #6b7280; margin: 0.35rem 0 0.75rem; font-family: Montserrat; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2 calling-page">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0" style="font-family: Montserrat; font-size: 1.1rem; color: #101828;">Lead Segments</h5>
            <p class="text-muted small mb-0" style="font-size: 0.75rem;">Manage and monitor your imported data lists</p>
        </div>
        <a href="<?php echo e(route('calling.list.create')); ?>" class="btn-create-list">
            <i class="bi bi-cloud-arrow-up-fill"></i> Import New List
        </a>
    </div>

    <div class="hero-metrics">
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-sky">
                <img src="<?php echo e(asset('img/icons/all.png')); ?>" alt="Total Segments">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Segments</span>
                <span class="metric-value text-primary" id="totalLists">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-amber">
                <img src="<?php echo e(asset('img/icons/pending.png')); ?>" alt="Total Leads">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Total Leads</span>
                <span class="metric-value text-warning" id="totalLeads">0</span>
            </div>
        </div>
        <div class="hero-metric-card">
            <div class="hero-metric-icon icon-emerald">
                <img src="<?php echo e(asset('img/icons/tick.png')); ?>" alt="Active Status">
            </div>
            <div class="hero-metric-content">
                <span class="metric-label">Latest refresh</span>
                <span class="metric-value text-success" id="lastRefreshed">--:--</span>
            </div>
        </div>
    </div>

    <div class="data-table-card">
        <div class="table-scroll">
            <table class="table custom-table" id="listsTable">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Segment name</th>
                        <th>Records volume</th>
                        <th>Created on</th>
                        <th class="text-end" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center p-5 text-muted">Indexing lead segments...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
        <div class="table-range-meta" id="rangeInfo">
            Showing 0-0 from 0 segments
        </div>
        <ul class="pagination mb-0" id="paginationLinks"></ul>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        function loadData(page = 1) {
            $.get('<?php echo e(route("calling.list.data")); ?>?page=' + page, function(data) {
                let listObj = data.lists || {};
                renderRows(listObj.data || []);
                buildPagination(listObj);
                $('#totalLists').text((listObj.total || 0).toLocaleString('en-IN'));
                $('#totalLeads').text((data.total_leads || 0).toLocaleString('en-IN'));
                $('#lastRefreshed').text(new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            });
        }

        function renderRows(rows) {
            let html = '';
            if (rows && rows.length) {
                rows.forEach(function(r) {
                    let date = new Date(r.created_at).toLocaleString('en-IN', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit', hour12: true
                    });
                    html += `
                        <tr id="row-${r.id}">
                            <td class="fw-bold text-muted">#${r.id}</td>
                            <td><div class="list-name">${r.name}</div></td>
                            <td><span class="badge-records">${parseInt(r.total_records).toLocaleString()} Contacts</span></td>
                            <td>
                                <div class="list-date">
                                    <i class="bi bi-clock-history me-1"></i> ${date}
                                </div>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-link text-danger p-0" onclick="deleteList(${r.id})" title="Remove List">
                                    <i class="bi bi-trash-fill fs-5"></i>
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center p-5 text-muted"><i class="bi bi-cloud-slash d-block fs-1 mb-3"></i>No segments found.</td></tr>';
            }
            $('#listsTable tbody').html(html);
        }

        function buildPagination(data) {
            const $container = $('#paginationLinks'); $container.empty();
            if (data.last_page <= 1) return;
            $container.append(`<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="bi bi-chevron-left"></i> Previous</a></li>`);
            $container.append(`<li class="page-item active"><span class="page-link">${data.current_page} / ${data.last_page}</span></li>`);
            $container.append(`<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${data.current_page + 1}">Next <i class="bi bi-chevron-right"></i></a></li>`);
            $('#rangeInfo').text(`Showing ${data.from || 0}-${data.to || 0} from ${data.total || 0} segments`);
        }

        window.deleteList = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Removing this list will also delete all associated contacts! This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#434AFA',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/calling/list/' + id,
                        type: 'DELETE',
                        success: function(resp) {
                            if (resp.success) {
                                $(`#row-${id}`).fadeOut(300);
                                Swal.fire('Deleted!', resp.message, 'success');
                                loadData(1);
                            } else {
                                Swal.fire('Error', resp.message, 'error');
                            }
                        }
                    });
                }
            })
        };

        $(document).on('click', '.page-link', function(e) { e.preventDefault(); loadData($(this).data('page')); });
        
        loadData(1);
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/calling/list/index.blade.php ENDPATH**/ ?>