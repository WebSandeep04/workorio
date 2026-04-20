<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header lead-header">
                <h5 class="modal-title text-white fw-bold" id="remarksModalLabel">
                    Lead Details and Remarks
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
              <!-- Body -->
            <div class="modal-body">

                <!-- Lead Summary -->
                <div class="card lead-summary mb-4">
                    <div class="card-header bg-white fw-bold">
                        Lead Summary
                        <span class="float-end small">
                            Next Follow-up: <span id="modalNextFollowUp" class ="fw-bold">-</span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">

                            <!-- Left -->
                            <div>
                                <h5 class="fw-bold mb-1" id="modalLeadName">-</h5>
                                <div class="text" id="modalBusiness">-</div>
                                <div class="mt-2">
                                    <span id="modalProduct">-</span>  <span class="badge status-pill" id="modalStatus">-</span>
                                </div>
                                <div class="mt-2 small">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span id="modalContactNumber">-</span>
                                    &nbsp;&nbsp;
                                    <i class="bi bi-envelope-fill"></i>
                                    <span id="modalEmail">-</span>
                                </div>
                            </div>
                    
                    
                            <!-- Right -->
                            <div class=" text-left">
                                <div>
                                    <i class="bi bi-person-fill"></i>
                                    <span id="modalContactPerson">-</span>
                                </div>

                                <div>
                                    <i class="bi bi-person-badge-fill text-primary"></i>
                                    Owner: <span id="modalOwner">-</span>
                                </div>

                                <div>
                                    <i class="bi bi-person-plus-fill text-success"></i>
                                    Assigned By: <span id="modalCreatedBy">-</span>
                                </div>

                                <div>
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span id="modalCity">-</span>,
                                    <span id="modalState">-</span>
                                </div>

                                <div>
                                    <i class="bi bi-cash-stack text-primary"></i>
                                    Ticket Value: <span id="modalTicketValue">-</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                 <span class="texted fw-bold card2" >Follow-up Remarks:</span>
                <div class="card mt-1">
                    <div class="card-body">
                        <div id="remarksList" class="remarks-timeline">
                            <!-- Remarks injected here -->
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
.modal-xl {
    max-width: 70%;
}

.card{
    padding: 15px;
}

.card2 {
    margin-top: 10px;
}

.modal-body{
    padding: 40px;
    background: #f0f0f0; 
}

.lead-header {
    background: #434AFA;
    padding: 12px 20px;
}

.lead-summary {
    border-radius: 10px;
}

.status-pill {
    background: #d1f5ea;
    color: #0f766e;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.remarks-timeline {
    max-height: 420px;
    overflow-y: auto;
    padding-left: 10px;
    padding-top: 10px;
}

.remark-item {
    position: relative;
    padding-left: 30px;
    padding-bottom: 25px;
    border-left: 2px solid #e9ecef;
}

.remark-item:last-child {
    border-left-color: transparent;
    padding-bottom: 0;
}

.remark-item::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 5px;
    width: 10px;
    height: 10px;
    background: #434AFA;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #434AFA;
    z-index: 1;
}

.remark-date {
    position: relative;
    background: #434AFA;
    color: white;
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 8px;
}

/* Arrow for the date bubble */
.remark-date::after {
    content: '';
    position: absolute;
    left: -6px;
    top: 50%;
    transform: translateY(-50%);
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-right: 6px solid #434AFA;
}

.remark-text {
    font-size: 14px;
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid #f1f5f9;
}

.remarks-timeline::-webkit-scrollbar {
    width: 6px;
}

.remarks-timeline::-webkit-scrollbar-thumb {
    background: #c7c9ff;
    border-radius: 6px;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .modal-body {
        padding: 20px;
    }

    .lead-summary .card-body .d-flex {
        flex-direction: column;
        gap: 15px;
    }

    /* Right side info becomes full width and normal text align */
    .lead-summary .card-body .text-left {
        width: 100%;
        text-align: left !important;
        margin-top: 10px;
        border-top: 1px solid #eee;
        padding-top: 10px;
    }
    
    /* Ensure icons align nicely in stacked mode */
    .lead-summary .card-body .text-left > div {
        margin-bottom: 5px;
    }
}

@media (max-width: 576px) {
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 15px;
    }

    .card {
        padding: 10px;
    }
    
    .remark-item {
        margin-bottom: 15px;
        padding-left: 20px;
    }
    
    .remark-date {
        font-size: 11px;
        padding: 2px 8px;
    }
    
    .lead-header h5 {
        font-size: 1.1rem;
    }
}
</style>

<script>
window.showRemarksModal = function (salesRecordId) {

    $('#remarksList').html('<div class="text-center">Loading...</div>');

    $.ajax({
        url: '{{ route("team-analytics.remarks") }}',
        type: 'GET',
        data: { sales_record_id: salesRecordId },

        success: function (response) {

            $('#modalLeadName').text(response.sales_record.leads_name || '-');
            $('#modalContactPerson').text(response.sales_record.contact_person || '-');
            $('#modalContactNumber').text(response.sales_record.contact_number || '-');
            $('#modalEmail').text(response.sales_record.email || '-');
            $('#modalState').text(response.sales_record.state_name || '-');
            $('#modalCity').text(response.sales_record.city_name || '-');
            $('#modalProduct').text(response.sales_record.product_name || '-');
            $('#modalBusiness').text(response.sales_record.business_name || '-');
            $('#modalStatus').text(response.sales_record.status_name || '-');
            $('#modalTicketValue').text(response.sales_record.ticket_value || '-');
            $('#modalNextFollowUp').text(response.sales_record.next_follow_up_date || '-');
            $('#modalOwner').text(response.sales_record.owner_name || '-');
            $('#modalCreatedBy').text(response.sales_record.created_by_name || '-');

            let remarksHtml = '';

            if (response.remarks && response.remarks.length > 0) {
                response.remarks.forEach(function (remark) {
                    remarksHtml += `
                        <div class="remark-item">
                            <div class="remark-date">${remark.date}</div>
                            <div class="remark-text">${remark.remark}</div>
                        </div>
                    `;
                });
            } else {
                remarksHtml = '<div class="text-center text-muted">No remarks found</div>';
            }

            $('#remarksList').html(remarksHtml);
            $('#remarksModal').modal('show');
        },

        error: function () {
            $('#remarksList').html('<div class="text-danger text-center">Failed to load remarks</div>');
            $('#remarksModal').modal('show');
        }
    });
};
</script>

