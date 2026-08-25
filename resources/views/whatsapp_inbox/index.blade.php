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
      <div id="replyExpiredAlert" class="alert alert-warning m-3" style="display: none; padding: 0.5rem 1rem; font-size: 0.85rem;">
          <i class="bi bi-info-circle"></i> The 24-hour window for replying has expired.
      </div>
      <div class="modal-footer flex-column align-items-start" id="replyFooter" style="display: none; background-color: #f4f5f7; border-top: 1px solid #e2e5ec; padding: 0.75rem;">
        <div id="filePreviewContainer" style="display: none; width: 100%; margin-bottom: 8px;">
            <div class="d-flex align-items-center bg-white p-2 border rounded shadow-sm" style="max-width: 300px;">
                <i class="bi bi-file-earmark-text text-primary fs-4 me-2" id="filePreviewIcon"></i>
                <div class="text-truncate flex-grow-1" id="filePreviewName" style="font-size: 0.85rem;">filename.pdf</div>
                <button type="button" class="btn-close ms-2" style="font-size: 0.7rem;" onclick="clearReplyFile()"></button>
            </div>
        </div>
        <div class="input-group w-100">
            <button class="btn btn-light border" type="button" onclick="document.getElementById('replyFile').click()" title="Attach File">
                <i class="bi bi-paperclip fs-5 text-secondary"></i>
            </button>
            <input type="file" id="replyFile" style="display: none;" onchange="handleReplyFileSelect(this)">
            <input type="text" id="replyMessage" class="form-control" placeholder="Type your reply...">
            <button class="btn" style="background-color: #434afa; color: white;" id="sendReplyBtn" onclick="sendReply()">Send <i class="bi bi-send"></i></button>
        </div>
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
                
                // Only show incoming messages in the main table
                let incomingMessages = allMessages.filter(msg => !['reply', 'image_reply', 'document_reply'].includes(msg.message_type));

                if (incomingMessages.length === 0) {
                    $('#messages_container').html('<tr><td colspan="5" class="text-center py-4 text-muted">No incoming messages found.</td></tr>');
                    $('#pagination_container').hide();
                } else {
                    // Group messages by sender_number
                    let groupedMessages = {};
                    incomingMessages.forEach(msg => {
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
        $('#replyMessage').val('');
        clearReplyFile();
        
        let history = allMessages.filter(msg => msg.sender_number === senderNumber || (['reply', 'image_reply', 'document_reply'].includes(msg.message_type) && msg.receiver_number === senderNumber));
        // Sort chronologically for the chat (oldest first)
        history.sort((a, b) => new Date(a.received_at) - new Date(b.received_at));

        let chatHtml = '';
        let lastReceivedDate = null;

        history.forEach(msg => {
            let time = new Date(msg.received_at).toLocaleString();
            let isReply = ['reply', 'image_reply', 'document_reply'].includes(msg.message_type);

            if (!isReply) {
                if (!lastReceivedDate || new Date(msg.received_at) > lastReceivedDate) {
                    lastReceivedDate = new Date(msg.received_at);
                }
            }
            
            let mediaDisplay = '';
            if (msg.media_url) {
                let isImg = (msg.message_type === 'image' || msg.message_type === 'image_reply');
                if (isImg) {
                    mediaDisplay = `<div class="mb-2"><a href="${msg.media_url}" target="_blank"><img src="${msg.media_url}" class="img-fluid rounded shadow-sm" style="max-height: 150px;"></a></div>`;
                } else {
                    mediaDisplay = `<div class="mb-2"><a href="${msg.media_url}" target="_blank" class="btn btn-sm btn-light border" style="font-size: 0.75rem;"><i class="bi bi-file-earmark-arrow-down"></i> View Attachment</a></div>`;
                }
            }

            let textDisplay = msg.message_text ? `<div class="mb-1 text-dark" style="font-size: 0.9rem;">${msg.message_text}</div>` : '';
            if (!msg.message_text && !msg.media_url) {
                textDisplay = '<span class="text-muted fst-italic">(No text)</span>';
            }
            
            if (isReply) {
                chatHtml += `
                    <div class="mb-3 d-flex justify-content-end">
                        <div class="p-2 bg-white shadow-sm" style="max-width: 85%; border: 1px solid #e2e5ec; border-radius: 12px 12px 0px 12px; background-color: #e3f2fd !important;">
                            ${mediaDisplay}
                            ${textDisplay}
                            <div class="text-muted text-end" style="font-size: 0.65rem;">
                                ${time}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                let badge = (msg.message_type !== 'text' && !msg.media_url) ? `<span class="badge bg-secondary me-1">${msg.message_type}</span>` : '';
                chatHtml += `
                    <div class="mb-3 d-flex justify-content-start">
                        <div class="p-2 bg-white shadow-sm" style="max-width: 85%; border: 1px solid #e2e5ec; border-radius: 12px 12px 12px 0px;">
                            ${mediaDisplay}
                            ${textDisplay}
                            <div class="text-muted text-end" style="font-size: 0.65rem;">
                                ${badge}${time}
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        $('#chatHistoryBody').html(chatHtml);
        
        if (lastReceivedDate) {
            let now = new Date();
            let diffMs = now - lastReceivedDate;
            let diffHours = diffMs / (1000 * 60 * 60);
            
            if (diffHours < 23.5) {
                $('#replyFooter').show();
                $('#replyExpiredAlert').hide();
            } else {
                $('#replyFooter').hide();
                $('#replyExpiredAlert').show();
            }
        } else {
            $('#replyFooter').hide();
            $('#replyExpiredAlert').hide();
        }

        var chatModal = new bootstrap.Modal(document.getElementById('chatHistoryModal'));
        chatModal.show();
        
        setTimeout(() => {
            $('#chatHistoryBody').scrollTop($('#chatHistoryBody')[0].scrollHeight);
        }, 100);
    }

    function clearReplyFile() {
        $('#replyFile').val('');
        $('#filePreviewContainer').hide();
    }

    function handleReplyFileSelect(input) {
        if (input.files && input.files[0]) {
            let file = input.files[0];
            $('#filePreviewName').text(file.name);
            
            let ext = file.name.split('.').pop().toLowerCase();
            let isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
            
            if (isImage) {
                $('#filePreviewIcon').attr('class', 'bi bi-image text-success fs-4 me-2');
            } else {
                $('#filePreviewIcon').attr('class', 'bi bi-file-earmark-text text-primary fs-4 me-2');
            }
            
            $('#filePreviewContainer').show();
        }
    }

    function sendReply() {
        let recipient = $('#modalSenderNumber').text();
        let message = $('#replyMessage').val().trim();
        let fileInput = document.getElementById('replyFile');
        let file = fileInput.files.length > 0 ? fileInput.files[0] : null;
        
        if (!message && !file) {
            alert('Please enter a message or attach a file.');
            return;
        }
        
        $('#sendReplyBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
        
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('recipient_number', recipient);
        if (message) formData.append('message', message);
        if (file) formData.append('file', file);
        
        $.ajax({
            url: `{{ route('whatsapp-inbox.reply') }}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#replyMessage').val('');
                    clearReplyFile();
                    loadMessages(); // reload all messages
                    
                    let time = new Date().toLocaleString();
                    let mediaHtml = '';
                    if (file) {
                        let isImage = file.type.startsWith('image/');
                        if (isImage) {
                            mediaHtml = `<div class="mb-1"><i class="bi bi-image text-primary"></i> <small class="text-muted">Image sent</small></div>`;
                        } else {
                            mediaHtml = `<div class="mb-1"><i class="bi bi-file-earmark-text text-primary"></i> <small class="text-muted">Document sent</small></div>`;
                        }
                    }
                    
                    let chatHtml = `
                        <div class="mb-3 d-flex justify-content-end">
                            <div class="p-2 bg-white shadow-sm" style="max-width: 85%; border: 1px solid #e2e5ec; border-radius: 12px 12px 0px 12px; background-color: #e3f2fd !important;">
                                ${mediaHtml}
                                ${message ? `<div class="mb-1 text-dark" style="font-size: 0.9rem;">${message}</div>` : ''}
                                <div class="text-muted text-end" style="font-size: 0.65rem;">
                                    ${time}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#chatHistoryBody').append(chatHtml);
                    $('#chatHistoryBody').scrollTop($('#chatHistoryBody')[0].scrollHeight);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error sending reply');
            },
            complete: function() {
                $('#sendReplyBtn').prop('disabled', false).html('Send <i class="bi bi-send"></i>');
            }
        });
    }
</script>
@endpush
