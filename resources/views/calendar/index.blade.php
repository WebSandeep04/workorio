@extends('layouts.app')

@section('title', 'Calendar')
@section('page_title', 'Calendar')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center position-relative" style="background: #434AFA; color: white; min-height: 50px;">
            <h6 class="mb-0 position-absolute start-0 ms-3 d-none d-md-block"><i class="bi bi-calendar3 me-2"></i>Calendar</h6>
            <div class="d-flex gap-2 align-items-center mx-auto">
                <button id="prevMonth" class="btn btn-sm btn-light">◀</button>
                <span id="monthLabel" class="fw-semibold small bg-white text-dark px-2 py-1 rounded"></span>
                <button id="nextMonth" class="btn btn-sm btn-light">▶</button>
            </div>
        </div>
        <div class="card-body">
            <div id="calendarGrid" class="table-responsive"></div>
        </div>
    </div>
</div>
@endsection

<style>
/***** Calendar Modal Polishing *****/
#eventDetailsModal .modal-body { background: #f8fafc; }
.client-block { border: 1px solid #eef2f7; border-radius: 10px; padding: 10px; background: #fff; transition: box-shadow .2s ease; }
.client-block:hover { box-shadow: 0 2px 10px rgba(16,24,40,.08); }
.client-header { display:flex; align-items:center; justify-content:space-between; gap: 10px; }
.client-name { font-weight: 600; color: #0f172a; }
.status-select { min-width: 180px; }
.handle-item { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border:1px solid #e5e7eb; border-radius: 999px; background:#fff; margin: 2px 6px 6px 0; }
.handle-item .form-check-input { margin:0; }
.checklist-card { margin-top: 8px; }
.checklist-title { font-size: 12px; color:#6c757d; margin-bottom:6px; display:flex; align-items:center; gap:6px; }
.checklist-scroll { height: 120px; overflow-y: auto; border:1px solid #e5e7eb; border-radius:8px; padding:8px; background:#fff; }
.checklist-scroll .form-check { margin-bottom: 6px; }
</style>

@push('scripts')
<script>
(function(){
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    let today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let eventsByDate = {};

    function escapeHtml(text){
        return (text||'').toString()
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;')
            .replace(/'/g,'&#039;');
    }

    function ymd(date){
        const m = (date.getMonth()+1).toString().padStart(2,'0');
        const d = date.getDate().toString().padStart(2,'0');
        return `${date.getFullYear()}-${m}-${d}`;
    }

    function loadEvents(year, month){
        const first = new Date(year, month, 1);
        const last = new Date(year, month+1, 0);
        const from = ymd(first);
        const to = ymd(last);
        return $.get("{{ route('calendar.grid') }}", { from, to }).then(function(data){
            eventsByDate = {};
            (data||[]).forEach(function(e){
                if(e.event_date){
                    eventsByDate[e.event_date] = eventsByDate[e.event_date] || [];
                    eventsByDate[e.event_date].push(e);
                }
            });
        });
    }

    function renderCalendar(year, month){
        $('#monthLabel').text(`${monthNames[month]} ${year}`);
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month+1, 0);
        const startWeekDay = firstDay.getDay(); // 0 = Sun
        let html = '<table class="table table-sm table-bordered text-center" style="table-layout:fixed; font-size:12px">';
        html += '<thead class="table-light"><tr>'+
            '<th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>'+
            '</tr></thead><tbody>';
        let date = 1;
        for (let i=0; i<6; i++) {
            html += '<tr>';
            for (let j=0; j<7; j++) {
                if (i === 0 && j < startWeekDay) {
                    html += '<td class="bg-light" style="height:85px"></td>';
                } else if (date > lastDay.getDate()) {
                    html += '<td class="bg-light" style="height:85px"></td>';
                } else {
                    const cellDate = new Date(year, month, date);
                    const key = ymd(cellDate);
                    const isToday = key === ymd(new Date());
                    const events = eventsByDate[key] || [];
                    let eventsHtml = '';
                    if(events.length > 0){
                        const list = events.slice(0,3).map((e) => {
                            const safe = escapeHtml(e.title || '');
                            return `<div class="d-flex align-items-center" style="font-size:10px; margin-top:2px; text-align:left; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                                        <span title="${safe}">${safe.length>30? safe.slice(0,30)+'…': safe}</span>
                                    </div>`;
                        }).join('');
                        const extra = events.length > 3 ? `<div style="font-size:10px; color:#6c757d; margin-top:2px;">+${events.length-3} more</div>` : '';
                        eventsHtml = `<div style="margin-top:2px;">${list}${extra}</div>`;
                    }
                    html += `<td class="day-cell" data-date="${key}" style="vertical-align:top; height:85px; cursor:pointer; ${isToday? 'outline:2px solid #0d6efd; outline-offset:-2px; border-radius:6px;':''}">`+
                            `<div style="font-weight:600; font-size:11px; color:#6c757d; text-align:left">${date}</div>`+
                            eventsHtml+
                            `</td>`;
                    date++;
                }
            }
            html += '</tr>';
            if (date > lastDay.getDate()) break;
        }
        html += '</tbody></table>';
        $('#calendarGrid').html(html);
    }

    function refresh(){
        loadEvents(currentYear, currentMonth).then(function(){
            renderCalendar(currentYear, currentMonth);
        });
    }

    $('#prevMonth').on('click', function(){
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        refresh();
    });

    $('#nextMonth').on('click', function(){
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        refresh();
    });

    // Removed old modal/status logic

    $(document).ready(refresh);

    // Old interactions removed
    // Open modal on day click and show related social handles per client
    let currentModalDate = null;
    $(document).on('click', '.day-cell', function(){
        const date = $(this).data('date');
        if (!date) return;
        currentModalDate = date;
        $.get(`{{ url('/calendar/date') }}/${date}/handles`).done(function(resp){
            if (!resp || !resp.success){ alert('Failed to load'); return; }
            const clients = resp.clients || [];
            const clientHandles = resp.client_handles || {};
            const checkedHandles = resp.checked_handles || {};
            const statuses = resp.statuses || [];
            const clientStatuses = resp.client_statuses || {};
            const clientMissedReasons = resp.client_missed_reasons || {};
            const clientDescriptions = resp.client_descriptions || {};
            const checkedChecklistOptions = resp.checked_checklist_options || {};
            
            // Store for later use
            window.currentCheckedChecklistOptions = checkedChecklistOptions;
            window.currentClientMissedReasons = clientMissedReasons;
            window.currentClientDescriptions = clientDescriptions;
            
            let blocks = '';
            if (clients.length === 0){
                blocks = '<div class="text-muted">No clients for this date</div>';
            } else {
                clients.forEach(function(c, idx){
                    const checkedIds = checkedHandles[c.id] || [];
                    const currentStatusId = clientStatuses[c.id] || '';
                    const currentDescription = clientDescriptions[c.id] || '';
                    
                    // Build status dropdown
                    let statusOptions = '<option value="">Select Status</option>';
                    statuses.forEach(function(s){
                        const selected = (currentStatusId && (currentStatusId == s.id)) ? 'selected' : '';
                        statusOptions += `<option value="${s.id}" ${selected}>${escapeHtml(s.name)}</option>`;
                    });
                    
                    const list = (clientHandles[c.id] || []).map(function(h){
                        const id = `dh_${c.id}_${h.id}`;
                        const isChecked = checkedIds.indexOf(h.id) !== -1;
                        return `<div class=\"form-check form-check-inline me-2 mb-1\">\
                                  <input class=\"form-check-input date-handle-checkbox\" type=\"checkbox\" id=\"${id}\" data-client-id=\"${c.id}\" data-handle-id=\"${h.id}\" ${isChecked ? 'checked' : ''}>\
                                  <label class=\"form-check-label\" for=\"${id}\">${escapeHtml(h.name)}</label>\
                                </div>`;
                    }).join('');
                    blocks += `<div class=\"mb-3 client-status-block\" data-client-id=\"${c.id}\">\
                        <div class=\"d-flex justify-content-between align-items-center mb-2\">\
                            <div class=\"fw-semibold\">${escapeHtml(c.name)}</div>\
                            <select class=\"form-select form-select-sm date-client-status\" style=\"min-width: 180px;\" data-client-id=\"${c.id}\">\
                                ${statusOptions}\
                            </select>\
                        </div>\
                        <div class=\"checklist-container-${c.id}\" style=\"display: none;\"></div>\
                        <div class=\"missed-reason-container-${c.id}\" style=\"display: none;\"></div>\
                        <div class=\"mt-2\">${list || '<span class="text-muted">No social handles</span>'}</div>\
                        <div class=\"mt-2\">\
                            <label class=\"form-label small fw-semibold\">Description:</label>\
                            <textarea class=\"form-control form-control-sm date-client-description\" rows=\"2\" data-client-id=\"${c.id}\" placeholder=\"Enter description...\">${escapeHtml(currentDescription || '')}</textarea>\
                        </div>\
                    </div>`;
                    if (idx !== clients.length-1) blocks += '<hr class="my-2">';
                });
            }
            $('#dayModalBody').html(blocks);
            $('#dayModalDate').text(date);
            new bootstrap.Modal(document.getElementById('dayHandlesModal')).show();
        }).fail(function(){ alert('Failed to load'); });
    });

    // Handle checkbox click to save to database
    $(document).on('change', '.date-handle-checkbox', function(){
        const $checkbox = $(this);
        const clientId = $checkbox.data('client-id');
        const handleId = $checkbox.data('handle-id');
        const isChecked = $checkbox.is(':checked');
        
        if (!currentModalDate) {
            alert('Date not set');
            return;
        }

        $.ajax({
            url: '{{ route("calendar.date.handle.toggle") }}',
            method: 'POST',
            data: {
                date: currentModalDate,
                client_id: clientId,
                social_handle_id: handleId,
                is_checked: isChecked ? 1 : 0, // Send as 1 or 0 for better compatibility
                _token: '{{ csrf_token() }}'
            },
            success: function(resp){
                if (!resp || !resp.success){
                    // Revert checkbox if save failed
                    $checkbox.prop('checked', !isChecked);
                    alert(resp.message || 'Failed to save');
                }
            },
            error: function(xhr){
                // Revert checkbox on error
                $checkbox.prop('checked', !isChecked);
                let errorMsg = 'Failed to save';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                console.error('Error:', xhr.responseJSON);
                alert(errorMsg);
            }
        });
    });

    // Handle status dropdown change - load checklist options and missed reasons
    $(document).on('change', '.date-client-status', function(){
        const $select = $(this);
        const clientId = $select.data('client-id');
        const statusId = $select.val() || '';
        const statusName = $select.find('option:selected').text().toLowerCase().trim();
        const $container = $(`.checklist-container-${clientId}`);
        const $missedReasonContainer = $(`.missed-reason-container-${clientId}`);
        
        if (!statusId) {
            $container.hide().html('');
            $missedReasonContainer.hide().html('');
            // Clear status if no status selected
            saveClientStatus(clientId, '', [], null);
            return;
        }

        // Check if status is "missed" - show missed reasons
        if (statusName === 'missed') {
            // Fetch missed reasons
            $.get("{{ route('calendar-missed-reasons.fetch') }}").done(function(missedReasons){
                if (!missedReasons || missedReasons.length === 0) {
                    $missedReasonContainer.hide().html('');
                } else {
                    let missedReasonHtml = '<div class="checklist-card mt-2">';
                    missedReasonHtml += '<div class="checklist-title"><i class="bi bi-calendar-x"></i> Select Missed Reason</div>';
                    missedReasonHtml += '<div class="checklist-scroll">';
                    
                    const currentMissedReasonId = window.currentClientMissedReasons && window.currentClientMissedReasons[clientId] ? window.currentClientMissedReasons[clientId] : null;
                    
                    missedReasons.forEach(function(reason){
                        const reasonId = `missed_reason_${clientId}_${reason.id}`;
                        const checked = (currentMissedReasonId && currentMissedReasonId == reason.id) ? 'checked' : '';
                        missedReasonHtml += `<div class="form-check">\
                            <input class="form-check-input date-missed-reason" type="radio" name="missed_reason_${clientId}" id="${reasonId}" value="${reason.id}" data-client-id="${clientId}" data-reason-id="${reason.id}" ${checked}>\
                            <label class="form-check-label" for="${reasonId}">${escapeHtml(reason.name)}</label>\
                        </div>`;
                    });
                    
                    missedReasonHtml += '</div>';
                    missedReasonHtml += '</div>';
                    $missedReasonContainer.html(missedReasonHtml).show();
                }
            }).fail(function(){
                $missedReasonContainer.hide().html('');
            });
        } else {
            $missedReasonContainer.hide().html('');
        }

        // Fetch checklist options for this status
        $.get(`{{ url('/calendar/status') }}/${statusId}/checklists`).done(function(resp){
            if (!resp) return;
            
            const checklists = resp.checklists || [];
            const options = resp.options || [];
            
            if (checklists.length === 0) {
                $container.hide().html('');
                // Save status if no checklists required
                const missedReasonId = statusName === 'missed' ? $missedReasonContainer.find('.date-missed-reason:checked').data('reason-id') : null;
                if (statusName === 'missed' && !missedReasonId) {
                    alert('Please select a missed reason before saving.');
                    return;
                }
                saveClientStatus(clientId, statusId, [], missedReasonId);
                return;
            }

            // Group options by checklist
            const optionsByChecklist = {};
            options.forEach(function(opt){
                if (!optionsByChecklist[opt.checklist_id]) {
                    optionsByChecklist[opt.checklist_id] = [];
                }
                optionsByChecklist[opt.checklist_id].push(opt);
            });

            // Build checklist HTML
            let checklistHtml = '<div class="checklist-card mt-2">';
            checklistHtml += '<div class="checklist-title"><i class="bi bi-list-check"></i> Required Checklist Options</div>';
            checklistHtml += '<div class="checklist-scroll">';
            
            checklists.forEach(function(checklist){
                const checklistOptions = optionsByChecklist[checklist.id] || [];
                if (checklistOptions.length > 0) {
                    checklistHtml += `<div class="mb-2"><strong>${escapeHtml(checklist.name)}</strong></div>`;
                    checklistOptions.forEach(function(opt){
                        const optId = `checklist_${clientId}_${opt.id}`;
                        checklistHtml += `<div class="form-check">\
                            <input class="form-check-input date-checklist-option" type="checkbox" id="${optId}" data-client-id="${clientId}" data-option-id="${opt.id}">\
                            <label class="form-check-label" for="${optId}">${escapeHtml(opt.name)}</label>\
                        </div>`;
                    });
                }
            });
            
            checklistHtml += '</div>';
            checklistHtml += '<button type="button" class="btn btn-sm btn-primary mt-2 save-status-btn" data-client-id="' + clientId + '" style="width: 100%;">Save Status</button>';
            checklistHtml += '</div>';
            
            $container.html(checklistHtml).show();
            
            // Load previously checked options
            if (currentModalDate && window.currentCheckedChecklistOptions && window.currentCheckedChecklistOptions[clientId]) {
                window.currentCheckedChecklistOptions[clientId].forEach(function(optId){
                    $(`#checklist_${clientId}_${optId}`).prop('checked', true);
                });
            }
        }).fail(function(){
            $container.hide().html('');
            alert('Failed to load checklist options');
        });
    });

    // Handle save status button click
    $(document).on('click', '.save-status-btn', function(){
        const $btn = $(this);
        const clientId = $btn.data('client-id');
        const $block = $(`.client-status-block[data-client-id="${clientId}"]`);
        const $select = $block.find('.date-client-status');
        const statusId = $select.val() || '';
        
        if (!statusId) {
            alert('Please select a status');
            return;
        }

        // Get all checked checklist options
        const checkedOptions = [];
        $block.find('.date-checklist-option:checked').each(function(){
            checkedOptions.push($(this).data('option-id'));
        });

        // Validate all required options are checked by fetching status checklists again
        $.get(`{{ url('/calendar/status') }}/${statusId}/checklists`).done(function(resp){
            if (!resp) return;
            
            const options = resp.options || [];
            const requiredOptionIds = options.map(function(opt){ return opt.id; });
            
            // Check if all required options are checked
            const missing = requiredOptionIds.filter(function(id){
                return checkedOptions.indexOf(id) === -1;
            });
            
            if (missing.length > 0) {
                alert('Please check all required checklist options before saving the status.');
                return;
            }

            // Get selected missed reason if status is "missed"
            const statusName = $select.find('option:selected').text().toLowerCase().trim();
            const missedReasonId = statusName === 'missed' ? $block.find('.date-missed-reason:checked').data('reason-id') || null : null;
            if (statusName === 'missed' && !missedReasonId) {
                alert('Please select a missed reason before saving.');
                return;
            }
            
            // Save status with checklist options and missed reason
            saveClientStatus(clientId, statusId, checkedOptions, missedReasonId);
        });
    });

    // Function to save client status
    function saveClientStatus(clientId, statusId, checklistOptionIds, missedReasonId, descriptions){
        if (!currentModalDate) {
            alert('Date not set');
            return;
        }

        // Get description from textarea if not provided
        if (descriptions === undefined) {
            const $block = $(`.client-status-block[data-client-id="${clientId}"]`);
            const $descriptionField = $block.find('.date-client-description');
            descriptions = $descriptionField.val() || '';
        }

        $.ajax({
            url: '{{ route("calendar.date.client.status") }}',
            method: 'POST',
            data: {
                date: currentModalDate,
                client_id: clientId,
                status_id: statusId,
                checklist_option_ids: checklistOptionIds,
                missed_reason_id: missedReasonId || null,
                descriptions: descriptions,
                _token: '{{ csrf_token() }}'
            },
            success: function(resp){
                if (!resp || !resp.success){
                    alert(resp.message || 'Failed to save');
                } else {
                    const $block = $(`.client-status-block[data-client-id="${clientId}"]`);
                    const statusLabel = ($block.find('.date-client-status option:selected').text() || '').toLowerCase().trim();
                    window.currentClientMissedReasons = window.currentClientMissedReasons || {};
                    window.currentClientDescriptions = window.currentClientDescriptions || {};
                    if (statusLabel === 'missed') {
                        window.currentClientMissedReasons[clientId] = missedReasonId || null;
                    } else {
                        delete window.currentClientMissedReasons[clientId];
                    }
                    // Update stored description
                    if (descriptions) {
                        window.currentClientDescriptions[clientId] = descriptions;
                    } else {
                        delete window.currentClientDescriptions[clientId];
                    }

                    // Show success message briefly
                    const $btn = $(`.save-status-btn[data-client-id="${clientId}"]`);
                    const originalText = $btn.html();
                    $btn.html('Saved!').removeClass('btn-primary').addClass('btn-success');
                    setTimeout(function(){
                        $btn.html(originalText).removeClass('btn-success').addClass('btn-primary');
                    }, 2000);
                }
            },
            error: function(xhr){
                let errorMsg = 'Failed to save';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                console.error('Error:', xhr.responseJSON);
                alert(errorMsg);
            }
        });
    }

    $(document).on('change', '.date-missed-reason', function(){
        const $radio = $(this);
        const clientId = $radio.data('client-id');
        const reasonId = $radio.data('reason-id');
        const $block = $(`.client-status-block[data-client-id="${clientId}"]`);
        const $select = $block.find('.date-client-status');
        const statusId = $select.val() || '';
        const statusName = ($select.find('option:selected').text() || '').toLowerCase().trim();

        if (!statusId || statusName !== 'missed') {
            return;
        }

        const checkedOptions = [];
        $block.find('.date-checklist-option:checked').each(function(){
            checkedOptions.push($(this).data('option-id'));
        });

        saveClientStatus(clientId, statusId, checkedOptions, reasonId);
    });

    // Handle description textarea change - save description
    let descriptionTimeout = {};
    $(document).on('input', '.date-client-description', function(){
        const $textarea = $(this);
        const clientId = $textarea.data('client-id');
        
        // Clear existing timeout
        if (descriptionTimeout[clientId]) {
            clearTimeout(descriptionTimeout[clientId]);
        }
        
        // Debounce: save after 1 second of no typing
        descriptionTimeout[clientId] = setTimeout(function(){
            if (!currentModalDate) {
                return;
            }
            
            const descriptions = $textarea.val() || '';
            const $block = $(`.client-status-block[data-client-id="${clientId}"]`);
            const $select = $block.find('.date-client-status');
            const statusId = $select.val() || '';
            const checkedOptions = [];
            $block.find('.date-checklist-option:checked').each(function(){
                checkedOptions.push($(this).data('option-id'));
            });
            const statusName = ($select.find('option:selected').text() || '').toLowerCase().trim();
            const missedReasonId = statusName === 'missed' ? $block.find('.date-missed-reason:checked').data('reason-id') || null : null;
            
            // Save with current status (or empty if no status)
            saveClientStatus(clientId, statusId, checkedOptions, missedReasonId, descriptions);
        }, 1000);
    });

    // Load checklist options for existing statuses when modal opens
    $(document).on('shown.bs.modal', '#dayHandlesModal', function(){
        $('.date-client-status').each(function(){
            const statusId = $(this).val();
            if (statusId) {
                $(this).trigger('change');
            }
        });
    });
})();
</script>
@endpush

<!-- Day Handles Modal -->
<div class="modal fade" id="dayHandlesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0d6efd, #1e90ff); color: white;">
        <div class="d-flex align-items-center gap-2">
            <div class="fw-semibold">Social Handles</div>
            <span class="small" id="dayModalDate"></span>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="dayModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  </div>
