<!-- Remarks Modal -->
<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #434AFA; color: white; border-bottom: none;">
                <h5 class="modal-title" id="remarksModalLabel" style="font-size: 1rem; font-weight: 700;">Lead Details & Remarks</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="container-fluid p-0">
                    <div class="row g-0">
                        <!-- Left Part: Lead Details -->
                        <div class="col-12 col-lg-5 bg-primary-custom" style="background-color: white !important;">
                            <div class="p-3 h-100">
                                <h6 class="fw-bold mb-3 border-bottom pb-2" style="color: #434AFA; border-color: #dee2e6 !important;">Lead Information</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless lead-info-table mb-0">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Lead Name:</th>
                                                <td id="modalLeadName">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Contact Person:</th>
                                                <td id="modalContactPerson">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Contact Number:</th>
                                                <td id="modalContactNumber">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Email:</th>
                                                <td id="modalEmail">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">State:</th>
                                                <td id="modalState">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">City:</th>
                                                <td id="modalCity">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Product:</th>
                                                <td id="modalProduct">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Business Type:</th>
                                                <td id="modalBusiness">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Status:</th>
                                                <td id="modalStatus">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Ticket Value:</th>
                                                <td id="modalTicketValue">-</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Next Follow-up:</th>
                                                <td id="modalNextFollowUp">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Part: Remarks -->
                        <div class="col-12 col-lg-7 bg-light">
                            <div class="p-3 h-100">
                                <h6 class="fw-bold mb-3 text-primary-custom border-bottom pb-2">Remarks History</h6>
                                <div id="remarksList" style="height: 400px; overflow-y: auto; padding-right: 5px;">
                                    <!-- Remarks will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.table-responsive{
    padding: 15px !important;
    border-left: 4px solid #434AFA;
}
.modal-xl {
    max-width: 90%;
}

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.modal-header {
    border-radius: 0;
}

.bg-primary-custom {
    background-color: #f8f9fa !important;
}

.text-primary-custom {
    color: #434AFA !important;
}

/* Scrollbar styling */
#remarksList {
    scrollbar-width: thin;
    scrollbar-color: #434AFA #e9ecef;
}

#remarksList::-webkit-scrollbar {
    width: 6px;
}

#remarksList::-webkit-scrollbar-track {
    background: #e9ecef;
    border-radius: 3px;
}

#remarksList::-webkit-scrollbar-thumb {
    background-color: #434AFA;
    border-radius: 3px;
}

.lead-info-table th {
    font-weight: 600;
    font-size: 0.75rem;
    color: #495057;
    padding: 0.25rem 0.5rem 0.25rem 0;
    width: 35%;
    vertical-align: top;
}

.lead-info-table td {
    font-weight: 500;
    font-size: 0.85rem;
    color: #212529;
    padding: 0.25rem 0;
    vertical-align: top;
}

.border-white-50 {
    border-color: rgba(255, 255, 255, 0.2) !important;
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
                        <div class="card mb-3" style="border-left: 4px solid #434AFA;">
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
