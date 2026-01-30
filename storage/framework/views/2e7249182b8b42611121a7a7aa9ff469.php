

<?php $__env->startSection('title', 'Client-Event Links'); ?>
<?php $__env->startSection('page_title', 'Client-Event Links'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header" style="background: #434AFA; color: white;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h6 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-link-45deg"></i> Link Events for <span id="clientName" class="fw-semibold"></span></h6>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-primary" id="saveLinks"><i class="bi bi-save"></i> Save Links</button>
                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('calendar-client-event.links')); ?>"><i class="bi bi-arrow-left"></i> Back</a>
                </div>
                <span class="badge bg-primary" id="selectedCount">0 selected</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered align-middle mb-0">
                    <thead class="table-secondary" style="position: sticky; top: 0; z-index: 1;">
                        <tr>
                            <th style="width: 60px;"><input type="checkbox" id="checkAll"></th>
                            <th style="width: 140px;">Date</th>
                            <th>Event</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTbody"><tr><td colspan="3">Loading...</td></tr></tbody>
                </table>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-12">
                    <div class="card border-0" style="background:#f8fafc;">
                        <div class="card-body">
                            <h6 class="mb-2"><i class="bi bi-collection me-2"></i>Common Events</h6>
                            <div id="commonEventsContainer" class="row g-2"></div>
                            <div class="mt-2 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" id="saveCommon"><i class="bi bi-save"></i> Save Common Events</button>
                                <small class="text-muted">Tip: check an item to add one or more dates.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toasts -->
<div class="position-fixed" style="right: 12px; bottom: 12px; z-index: 1080;">
  <div id="actionToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">Saved</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){
    const clientId = <?php echo e((int)($clientId ?? 0)); ?>;
    let linked = [];
    let commonEvents = [];
    let existingCommon = {};

    function load(){
        $.when(
            $.get(`/calendar/client-event-links/${clientId}/events`),
            $.get(`/calendar/client-event-links/${clientId}/common-events`)
        ).done(function(evRes, comRes){
            const resp = evRes[0];
            $('#clientName').text(resp.client?.name || '');
            linked = resp.linked_event_ids || [];
            const rows = resp.events || [];
            let html = '';
            if (rows.length === 0) html = '<tr><td colspan="3" class="text-muted">No events found</td></tr>';
            else {
                html = rows.map(function(r){
                    const checked = linked.includes(r.id) ? 'checked' : '';
                    return `<tr>
                        <td class="text-center"><input type="checkbox" class="evchk" value="${r.id}" ${checked}></td>
                        <td class="text-nowrap">${r.event_date || ''}</td>
                        <td>${r.name || ''}</td>
                    </tr>`;
                }).join('');
            }
            $('#eventsTbody').html(html);
            // Common events render
            const com = comRes[0] || {};
            commonEvents = com.common_events || [];
            existingCommon = com.existing || {};
            renderCommonEvents();
        }).fail(function(){ $('#eventsTbody').html('<tr><td colspan="3" class="text-danger">Failed to load</td></tr>'); });
    }

    function renderCommonEvents(){
        let html = '';
        if (!commonEvents || commonEvents.length === 0){ html = '<div class="text-muted">No common events</div>'; }
        else {
            html = commonEvents.map(function(ce){
                const dates = existingCommon[ce.id] || [];
                const checked = dates.length > 0 ? 'checked' : '';
                const inputs = (dates.length ? dates : ['']).map(function(d, idx){
                    return `<div class=\"input-group input-group-sm mb-1\" data-ce-id=\"${ce.id}\">\
                                <span class=\"input-group-text\">Date</span>\
                                <input type=\"date\" class=\"form-control ce-date\" value=\"${d || ''}\">\
                                <button class=\"btn btn-outline-secondary add-date\" type=\"button\"><i class=\"bi bi-plus\"></i></button>\
                                <button class=\"btn btn-outline-danger remove-date\" type=\"button\"><i class=\"bi bi-x\"></i></button>\
                            </div>`;
                }).join('');
                return `<div class=\"col-md-6\">\
                            <div class=\"border rounded p-2 bg-white\">\
                                <div class=\"form-check mb-2\">\
                                    <input class=\"form-check-input ce-check\" type=\"checkbox\" id=\"ce_${ce.id}\" data-id=\"${ce.id}\" ${checked}>\
                                    <label class=\"form-check-label\" for=\"ce_${ce.id}\">${ce.name}</label>\
                                </div>\
                                <div class=\"ce-dates\" data-id=\"${ce.id}\" style=\"display:${checked? 'block':'none'};\">${inputs}</div>\
                            </div>\
                        </div>`;
            }).join('');
        }
        $('#commonEventsContainer').html(html);
    }

    // filters removed
    $(document).on('change', '#checkAll', function(){
        const checked = $(this).is(':checked');
        $('.evchk').prop('checked', checked);
        updateSelectedCount();
    });
    $(document).on('change', '.evchk', updateSelectedCount);
    function updateSelectedCount(){
        const n = $('.evchk:checked').length;
        $('#selectedCount').text(`${n} selected`);
    }
    $('#saveLinks').on('click', function(){
        const selected = $('.evchk:checked').map(function(){ return $(this).val(); }).get();
        $.ajax({
            url: `/calendar/client-event-links/${clientId}/save`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                event_ids: selected
            }
        }).done(function(resp){
            if (resp && resp.success) {
                showToast('Event links saved');
                load();
            } else {
                alert(resp?.message || 'Failed to save');
            }
        }).fail(function(){ alert('Failed to save'); });
    });

    // Common events interactions
    $(document).on('change', '.ce-check', function(){
        const id = $(this).data('id');
        const show = $(this).is(':checked');
        $(`.ce-dates[data-id=${id}]`).toggle(show);
    });
    $(document).on('click', '.add-date', function(){
        const group = $(this).closest('.input-group');
        const ceId = group.data('ce-id');
        const clone = group.clone();
        clone.find('input.ce-date').val('');
        group.after(clone);
    });
    $(document).on('click', '.remove-date', function(){
        const container = $(this).closest('.ce-dates');
        const groups = container.find('.input-group');
        if (groups.length > 1) {
            $(this).closest('.input-group').remove();
        } else {
            $(this).closest('.input-group').find('input.ce-date').val('');
        }
    });
    $('#saveCommon').on('click', function(){
        const items = [];
        $('.ce-check:checked').each(function(){
            const id = $(this).data('id');
            const dates = $(`.ce-dates[data-id=${id}] input.ce-date`).map(function(){ return $(this).val(); }).get().filter(Boolean);
            if (dates.length > 0) items.push({ common_event_id: id, dates });
        });
        $.ajax({
            url: `/calendar/client-event-links/${clientId}/common-events/save`,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                items: items
            }
        }).done(function(resp){
            if (resp && resp.success) { showToast('Common events saved'); load(); }
            else { alert(resp?.message || 'Failed to save'); }
        }).fail(function(){ alert('Failed to save'); });
    });

    function showToast(msg){
        $('#toastMsg').text(msg || 'Saved');
        try {
            new bootstrap.Toast(document.getElementById('actionToast')).show();
        } catch(e) { /* no-op */ }
    }

    $(document).ready(load);
})();
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Don't Delete\laravel\leadmanagement (akrati ui work)\resources\views/calendar/client-event-links/events.blade.php ENDPATH**/ ?>