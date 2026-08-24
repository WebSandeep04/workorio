@extends('layouts.app')
@section('title', 'WhatsApp Inbox')
@section('page_title', 'WhatsApp Inbox')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-violet">
                <i class="bi bi-people text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Numbers</div>
                <div class="summary-card-value text-primary" id="total_numbers_count">0</div>
            </div>
        </div>
    </div>

    <!-- Search & Add (Separate Row) -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="search_inbox" placeholder="Search leads, contacts, emails..." />
        </div>
    </div>

    <!-- Data Table -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table mb-0">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Latest Message</th>
                            <th>Type</th>
                            <th>Last Received</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="messages_container">
                        <tr><td colspan="5" class="text-center py-4">Loading messages...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center" id="pagination_container" style="background: #fff; border-top: 1px solid #f1f3f5; font-family: Montserrat; display: none;">
            <div id="pagination_info" style="font-size: 10px; color: #6c757d; font-weight: 500;">Showing 0 to 0 of 0 entries</div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="pagination_controls"></ul>
            </nav>
        </div>
    </div>

</div>

<!-- Modal for Chat History -->
<div class="modal fade" id="chatHistoryModal" tabindex="-1" aria-labelledby="chatHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header" style="background-color: #434afa; color: white;">
        <h5 class="modal-title" id="chatHistoryModalLabel" style="font-size: 1rem;">Chat History: <span id="modalSenderNumber" class="fw-bold"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="chatHistoryBody" style="background-color: #f4f5f7; max-height: 65vh; overflow-y: auto;">
        <!-- Chat bubbles go here -->
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
    let allMessages = [];
    let uniqueMessages = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    $(document).ready(function() {
        loadMessages();
    });

    function loadMessages() {
        $('#messages_container').html('<tr><td colspan="5" class="text-center py-4">Loading messages...</td></tr>');
        $('#pagination_container').hide();
        
        $.ajax({
            url: `{{ route('whatsapp-inbox.fetch') }}`,
            type: 'GET',
            success: function(response) {
                allMessages = response.data;

                if (allMessages.length === 0) {
                    $('#messages_container').html('<tr><td colspan="5" class="text-center py-4 text-muted">No incoming messages found.</td></tr>');
                    $('#pagination_container').hide();
                } else {
                    // Group messages by sender_number
                    let groupedMessages = {};
                    allMessages.forEach(msg => {
                        if (!groupedMessages[msg.sender_number]) {
                            groupedMessages[msg.sender_number] = msg;
                        } else {
                            let existingDate = new Date(groupedMessages[msg.sender_number].received_at);
                            let newDate = new Date(msg.received_at);
                            if (newDate > existingDate) {
                                groupedMessages[msg.sender_number] = msg;
                            }
                        }
                    });

                    uniqueMessages = Object.values(groupedMessages);
                    // Sort descending by received_at
                    uniqueMessages.sort((a, b) => new Date(b.received_at) - new Date(a.received_at));
                    
                    // Update total numbers count
                    $('#total_numbers_count').text(uniqueMessages.length);
                    
                    currentPage = 1;
                    renderTable();
                }
            },
            error: function() {
                $('#messages_container').html('<tr><td colspan="5" class="text-center py-4 text-danger">Error loading messages.</td></tr>');
                $('#pagination_container').hide();
            }
        });
    }

    function renderTable() {
        let html = '';
        const totalItems = uniqueMessages.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        if (totalItems === 0) {
            $('#messages_container').html('<tr><td colspan="5" class="text-center py-4 text-muted">No messages found.</td></tr>');
            $('#pagination_container').hide();
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
        const paginatedItems = uniqueMessages.slice(startIndex, endIndex);

        paginatedItems.forEach(msg => {
            let senderDisplay = msg.sender_name 
                ? `${msg.sender_name} <br><small class="text-muted">${msg.sender_number}</small>` 
                : `${msg.sender_number}`;

            let text = msg.message_text ? msg.message_text : '<span class="text-muted fst-italic">(No text)</span>';
            let typeBadge = msg.message_type !== 'text' 
                ? `<span class="badge bg-secondary badge-type">${msg.message_type}</span>` 
                : `<span class="badge bg-light text-dark border badge-type">text</span>`;
            let time = new Date(msg.received_at).toLocaleString();
            
            html += `
                <tr>
                    <td class="fw-medium text-dark">${senderDisplay}</td>
                    <td>${text}</td>
                    <td>${typeBadge}</td>
                    <td>${time}</td>
                    <td class="text-end">
                        <button class="btn btn-sm text-white px-3 py-1" style="background-color: #434afa; border-radius: 4px; font-weight: 500;" onclick="viewChatHistory('${msg.sender_number}')">
                            <i class="bi bi-chat-dots"></i> View
                        </button>
                    </td>
                </tr>
            `;
        });
        
        $('#messages_container').html(html);
        
        if (totalPages > 0) {
            $('#pagination_container').css('display', 'flex');
            $('#pagination_info').text(`Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`);
            renderPaginationControls(totalPages);
        } else {
            $('#pagination_container').css('display', 'none');
        }
    }

    function renderPaginationControls(totalPages) {
        let paginationHtml = '';
        
        // Previous Button
        paginationHtml += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;" style="color: #434afa;">Previous</a>
            </li>
        `;
        
        // Page Numbers
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            let activeStyle = i === currentPage ? 'background-color: #434afa; border-color: #434afa; color: white;' : 'color: #434afa;';
            paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;" style="${activeStyle}">${i}</a>
                </li>
            `;
        }
        
        // Next Button
        paginationHtml += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;" style="color: #434afa;">Next</a>
            </li>
        `;
        
        $('#pagination_controls').html(paginationHtml);
    }

    function changePage(page) {
        const totalPages = Math.ceil(uniqueMessages.length / itemsPerPage);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            renderTable();
        }
    }

    function viewChatHistory(senderNumber) {
        $('#modalSenderNumber').text(senderNumber);
        
        let history = allMessages.filter(msg => msg.sender_number === senderNumber);
        // Sort chronologically for the chat (oldest first)
        history.sort((a, b) => new Date(a.received_at) - new Date(b.received_at));

        let chatHtml = '';
        history.forEach(msg => {
            let text = msg.message_text ? msg.message_text : '<span class="text-muted fst-italic">(No text)</span>';
            let time = new Date(msg.received_at).toLocaleString();
            
            chatHtml += `
                <div class="mb-3 d-flex justify-content-start">
                    <div class="p-2 bg-white shadow-sm" style="max-width: 85%; border: 1px solid #e2e5ec; border-radius: 12px 12px 12px 0px;">
                        <div class="mb-1 text-dark" style="font-size: 0.9rem;">${text}</div>
                        <div class="text-muted text-end" style="font-size: 0.65rem;">
                            ${msg.message_type !== 'text' ? `<span class="badge bg-secondary me-1">${msg.message_type}</span>` : ''}
                            ${time}
                        </div>
                    </div>
                </div>
            `;
        });

        if(chatHtml === '') chatHtml = '<div class="text-center text-muted">No messages found.</div>';

        $('#chatHistoryBody').html(chatHtml);
        
        var chatModal = new bootstrap.Modal(document.getElementById('chatHistoryModal'));
        chatModal.show();
    }
</script>
@endpush
