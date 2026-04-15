<!-- Calling Details and Remarks Modal -->
<div class="modal fade" id="callingDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">
            <div class="modal-header" style="background: #434AFA; padding: 1rem 1.5rem;">
                <h5 class="modal-title text-white fw-bold" style="font-family: Montserrat; font-size: 1.1rem;">
                    <i class="bi bi-person-badge-fill me-2"></i> Lead Profile & History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body" style="background: #f8f9fa; padding: 1.5rem;">
                <!-- Lead Summary Card -->
                <div class="card mb-4" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark" style="font-family: Montserrat;">LEAD SUMMARY</h6>
                            <span class="badge status-pill" id="cdModalStatus" style="background: #e0e7ff; color: #434AFA; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">-</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-1" id="cdModalName" style="color: #101828; font-family: Montserrat;">-</h5>
                                <div class="text-muted small mb-3" style="font-family: Montserrat;">
                                    <i class="bi bi-megaphone me-1"></i> Campaign: <span id="cdModalCampaign" class="fw-bold">-</span>
                                </div>
                                <div class="d-flex flex-column gap-2 small">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 24px; color: #434AFA;"><i class="bi bi-telephone-fill"></i></div>
                                        <span id="cdModalPhone">-</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 24px; color: #434AFA;"><i class="bi bi-envelope-fill"></i></div>
                                        <span id="cdModalEmail">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 border-start ps-4">
                                <div class="d-flex flex-column gap-3 small">
                                    <div class="d-flex align-items-start gap-2">
                                        <div style="width: 24px; color: #434AFA;"><i class="bi bi-geo-alt-fill"></i></div>
                                        <div>
                                            <div id="cdModalCityState" class="fw-semibold">-</div>
                                            <div id="cdModalAddress" class="text-muted" style="font-size: 0.7rem;">-</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-auto">
                                        <div style="width: 24px; color: #434AFA;"><i class="bi bi-calendar-check-fill"></i></div>
                                        <div>Created On: <span class="fw-semibold" id="cdModalCreated">-</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks History Timeline -->
                <div class="card" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-bold text-dark" style="font-family: Montserrat;">INTERACTION HISTORY</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div id="cdRemarksList" class="remarks-timeline" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                            <!-- Remarks injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.remarks-timeline {
    position: relative;
    padding-left: 20px;
}
.remark-item {
    position: relative;
    padding-bottom: 1.5rem;
    border-left: 2px solid #e5e7eb;
    padding-left: 25px;
}
.remark-item:last-child {
    border-left: 2px solid transparent;
    padding-bottom: 0;
}
.remark-item::before {
    content: '';
    position: absolute;
    left: -7px;
    top: 0;
    width: 12px;
    height: 12px;
    background: #434AFA;
    border: 2px solid #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 1px #434AFA;
}
.remark-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}
.remark-date {
    font-size: 0.7rem;
    font-weight: 700;
    color: #434AFA;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.remark-user {
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 8px;
    border-radius: 4px;
}
.remark-content {
    background: #fff;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #334155;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
</style>

<script>
window.showCallingDetails = function(id) {
    const list = $('#cdRemarksList');
    list.html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2 text-primary"></div> Fetching lead dossier...</div>');
    $('#callingDetailsModal').modal('show');

    $.get('<?php echo e(route("calling.assigned.lead-details", ["id" => ":id"])); ?>'.replace(':id', id))
    .done(function(resp) {
        const lead = resp.lead;
        $('#cdModalName').text(lead.name || '-');
        $('#cdModalCampaign').text(lead.campaign_name || 'Individual Upload');
        $('#cdModalStatus').text(lead.status_name || 'Fresh');
        $('#cdModalPhone').text(lead.phone || '-');
        $('#cdModalEmail').text(lead.email || '-');
        $('#cdModalCityState').text(`${lead.city || '-'}, ${lead.state || '-'}`);
        $('#cdModalAddress').text(lead.address || '-');
        $('#cdModalCreated').text(lead.created_at ? new Date(lead.created_at).toLocaleDateString() : '-');

        let html = '';
        if (resp.remarks && resp.remarks.length) {
            resp.remarks.forEach(r => {
                html += `
                    <div class="remark-item">
                        <div class="remark-header">
                            <span class="remark-date">${r.date}</span>
                            <span class="remark-user"><i class="bi bi-person me-1"></i> ${r.user}</span>
                        </div>
                        <div class="remark-content">${r.remark}</div>
                    </div>
                `;
            });
        } else {
            html = '<div class="text-center py-5 text-muted small"><i class="bi bi-chat-square-dots d-block fs-3 mb-2 opacity-50"></i> No interaction history recorded yet.</div>';
        }
        list.html(html);
    })
    .fail(function() {
        list.html('<div class="text-center py-4 text-danger small"><i class="bi bi-exclamation-triangle-fill"></i> System failure: Unable to retrieve remarks.</div>');
    });
};
</script>
<?php /**PATH D:\DontDelete\laravel\leadmanagement (akrati ui work)\resources\views/partials/calling-details-modal.blade.php ENDPATH**/ ?>