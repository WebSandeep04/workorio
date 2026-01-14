<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(to right, #6a11cb, #2575fc); color: white;">
                <h5 class="modal-title" id="remarksModalLabel">Lead Details & Remarks</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex">
                    <!-- First Part: Lead Details (5 columns) -->
                    <div class="col-5 pe-3">
                        <div class="card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-header">
                                <h6 class="mb-0">Lead Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Lead Name:</strong> <span id="modalLeadName">-</span></p>
                                <p><strong>Contact Person:</strong> <span id="modalContactPerson">-</span></p>
                                <p><strong>Contact Number:</strong> <span id="modalContactNumber">-</span></p>
                                <p><strong>Email:</strong> <span id="modalEmail">-</span></p>
                                <p><strong>State:</strong> <span id="modalState">-</span></p>
                                <p><strong>City:</strong> <span id="modalCity">-</span></p>
                                <p><strong>Product:</strong> <span id="modalProduct">-</span></p>
                                <p><strong>Business Type:</strong> <span id="modalBusiness">-</span></p>
                                <p><strong>Status:</strong> <span id="modalStatus">-</span></p>
                                <p><strong>Ticket Value:</strong> <span id="modalTicketValue">-</span></p>
                                <p><strong>Next Follow-up:</strong> <span id="modalNextFollowUp">-</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Second Part: Remarks (7 columns) -->
                    <div class="col-7 ps-3">
                        <div class="card h-100">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                <h6 class="mb-0">Remarks History</h6>
                            </div>
                            <div class="card-body">
                                <div id="remarksList" style="height: 400px; overflow-y: auto;">
                                    <!-- Remarks will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-xl {
    max-width: 95%;
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    border-radius: 15px 15px 0 0;
    border-bottom: none;
}

.modal-footer {
    border-radius: 0 0 15px 15px;
    border-top: none;
}

/* Flex layout for side-by-side sections */
.modal-body .d-flex {
    gap: 0;
}

.modal-body .col-5 {
    flex: 0 0 41.666667%;
    max-width: 41.666667%;
}

.modal-body .col-7 {
    flex: 0 0 58.333333%;
    max-width: 58.333333%;
}

/* Card styling */
.modal-body .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-body .card.h-100 {
    height: 100% !important;
}

/* Scrollbar styling */
#remarksList {
    scrollbar-width: thin;
    scrollbar-color: #667eea #f8f9fa;
}

#remarksList::-webkit-scrollbar {
    width: 8px;
}

#remarksList::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

#remarksList::-webkit-scrollbar-thumb {
    background-color: #667eea;
    border-radius: 4px;
}

.card-body p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.card-body p strong {
    color: rgba(255, 255, 255, 0.9);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-body .d-flex {
        flex-direction: column;
    }
    
    .modal-body .col-5,
    .modal-body .col-7 {
        flex: 0 0 100%;
        max-width: 100%;
        margin-bottom: 1rem;
    }
    
    .modal-body .col-5 {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// Make sure function is globally accessible
window.showRemarksModal = function(salesRecordId) {
    console.log('Opening remarks modal for sales record ID:', salesRecordId);
    
    // Show loading state
    $('#remarksList').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading remarks...</div>');
    
    // Reset fields while loading
    $('#modalLeadName').text('Loading...');
    
    // Fetch remarks data
    $.ajax({
        url: '{{ route("team-analytics.remarks") }}',
        type: 'GET',
        data: {
            sales_record_id: salesRecordId
        },
        success: function(response) {
            // Update lead details
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
            
            // Update remarks list
            let remarksHtml = '';
            if (response.remarks && response.remarks.length > 0) {
                response.remarks.forEach(function(remark) {
                    remarksHtml += `
                        <div class="card mb-3" style="border-left: 4px solid #667eea;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <i class="fas fa-calendar-alt"></i> ${remark.date}
                                        </h6>
                                        <p class="card-text">${remark.remark}</p>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> ${remark.created_at}
                                    </small>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                remarksHtml = '<div class="text-center text-muted"><i class="fas fa-info-circle"></i> No remarks found for this lead.</div>';
            }
            
            $('#remarksList').html(remarksHtml);
            
            // Show the modal
            $('#remarksModal').modal('show');
        },
        error: function(xhr) {
            console.error('Error loading remarks:', xhr);
            console.error('Response Text:', xhr.responseText);
            console.error('Status:', xhr.status);
            
            let errorMsg = 'Error loading remarks. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            
            $('#remarksList').html('<div class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> ' + errorMsg + '</div>');
            
            // Reset lead details to show error state
            $('#modalLeadName').text('-');
            $('#modalContactPerson').text('-');
            $('#modalContactNumber').text('-');
            $('#modalEmail').text('-');
            $('#modalState').text('-');
            $('#modalCity').text('-');
            $('#modalProduct').text('-');
            $('#modalBusiness').text('-');
            $('#modalStatus').text('-');
            $('#modalTicketValue').text('-');
            $('#modalNextFollowUp').text('-');
            
            $('#remarksModal').modal('show');
        }
    });
};
</script>
