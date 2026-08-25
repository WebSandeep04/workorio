@extends('layouts.app')
@section('title', 'WhatsApp Inbox')
@section('page_title', 'WhatsApp Inbox')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
@endpush

@section('content')
<div class="container-fluid pt-3 pb-3" style="height: calc(100vh - 80px); display: flex; flex-direction: column;">
    
    <!-- Main Chat Interface -->
    <div class="row flex-grow-1 overflow-hidden shadow-sm" style="background: white; border-radius: 8px; border: 1px solid #e2e5ec; margin: 0;">
        
        <!-- Left Panel: Contacts List -->
        <div class="col-md-4 col-lg-3 border-end p-0 d-flex flex-column h-100">
            <!-- Header/Search -->
            <div class="p-3 border-bottom" style="background-color: #f8f9fa;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 fw-bold" style="color: #434afa;">WhatsApp Inbox</h6>
                    <span class="badge rounded-pill" style="background-color: #434afa; font-weight: normal;" id="total_numbers_count">0</span>
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search_inbox" class="form-control border-start-0 ps-0" placeholder="Search contacts..." onkeyup="filterContacts()">
                </div>
            </div>
            
            <!-- Contact List -->
            <div class="flex-grow-1 overflow-auto" id="contacts_list">
                <div class="text-center py-4 text-muted">Loading messages...</div>
            </div>
        </div>

        <!-- Right Panel: Active Chat -->
        <div class="col-md-8 col-lg-9 p-0 d-flex flex-column h-100 position-relative">
            
            <!-- Default Empty State -->
            <div id="chat_empty_state" class="d-flex flex-column justify-content-center align-items-center h-100 w-100 bg-light" style="position: absolute; top: 0; left: 0; z-index: 10;">
                <div class="rounded-circle d-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px; background-color: #e2e5ec;">
                    <i class="bi bi-whatsapp" style="font-size: 2.5rem; color: #434afa;"></i>
                </div>
                <h5 class="text-muted fw-bold">Select a chat to start messaging</h5>
                <p class="text-muted small">Choose a contact from the left panel to view history and reply.</p>
            </div>
            
            <!-- Chat Header -->
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center" style="background-color: #f8f9fa;">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; background-color: #434afa !important;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" id="chat_header_name" style="font-size: 1.05rem;">-</h6>
                        <small class="text-muted" id="modalSenderNumber" style="font-size: 0.8rem;">-</small> <!-- Kept ID for JS compatibility -->
                    </div>
                </div>
            </div>
            
            <!-- Chat Body -->
            <div class="flex-grow-1 overflow-auto p-4" id="chatHistoryBody" style="background-color: #f0f2f5;">
                <!-- Chat bubbles go here -->
            </div>
            
            <!-- Chat Footer (Input) -->
            <div id="replyExpiredAlert" class="alert alert-warning m-2" style="display: none; padding: 0.5rem 1rem; font-size: 0.85rem;">
                <i class="bi bi-info-circle"></i> The 24-hour window for replying has expired.
            </div>
            <div class="p-3 border-top flex-column align-items-start" id="replyFooter" style="display: none; background-color: #f8f9fa;">
                <div id="filePreviewContainer" style="display: none; width: 100%; margin-bottom: 8px;">
                    <div class="d-flex align-items-center bg-white p-2 border rounded shadow-sm" style="max-width: 300px;">
                        <i class="bi bi-file-earmark-text text-primary fs-4 me-2" id="filePreviewIcon"></i>
                        <div class="text-truncate flex-grow-1" id="filePreviewName" style="font-size: 0.85rem;">filename.pdf</div>
                        <button type="button" class="btn-close ms-2" style="font-size: 0.7rem;" onclick="clearReplyFile()"></button>
                    </div>
                </div>
                <div class="input-group w-100 shadow-sm rounded">
                    <button class="btn btn-white border bg-white" type="button" onclick="document.getElementById('replyFile').click()" title="Attach File" style="border-radius: 8px 0 0 8px;">
                        <i class="bi bi-paperclip fs-5 text-secondary"></i>
                    </button>
                    <input type="file" id="replyFile" style="display: none;" onchange="handleReplyFileSelect(this)">
                    <input type="text" id="replyMessage" class="form-control border-start-0" placeholder="Type a message..." style="box-shadow: none;" onkeypress="if(event.key === 'Enter') sendReply();">
                    <button class="btn" style="background-color: #434afa; color: white; border-radius: 0 8px 8px 0;" id="sendReplyBtn" onclick="sendReply()">
                        <i class="bi bi-send-fill"></i>
                    </button>
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
                    
                    renderContactList();
                }
            },
            error: function() {
                $('#contacts_list').html('<div class="text-center py-4 text-danger">Error loading messages.</div>');
            }
        });
    }

    function renderContactList() {
        let html = '';
        if (uniqueMessages.length === 0) {
            html = '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No chats found.</div>';
        } else {
            uniqueMessages.forEach(msg => {
                let senderName = msg.sender_name || msg.sender_number;
                let text = msg.message_text ? msg.message_text : (msg.media_url ? 'Media message' : 'No text');
                
                let time = new Date(msg.received_at);
                let timeStr = time.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                // Add date if not today
                if (time.toDateString() !== new Date().toDateString()) {
                    timeStr = time.toLocaleDateString([], {month: 'short', day: 'numeric'});
                }
                
                // Truncate text
                if(text.length > 35) text = text.substring(0, 35) + '...';
                
                let mediaIcon = msg.message_type !== 'text' ? '<i class="bi bi-image me-1"></i> ' : '';
                
                html += `
                    <div class="contact-item p-3 border-bottom" onclick="viewChatHistory('${msg.sender_number}', '${msg.sender_name || ''}')" style="cursor: pointer; transition: background 0.2s;" data-number="${msg.sender_number}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold text-truncate text-dark" style="max-width: 70%; font-size: 0.95rem;">${senderName}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">${timeStr}</small>
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 0.85rem;">
                            ${mediaIcon}${text}
                        </div>
                    </div>
                `;
            });
        }
        $('#contacts_list').html(html);
        
        // Add hover effect
        $('.contact-item').hover(function() {
            if (!$(this).hasClass('active-chat')) {
                $(this).css('background-color', '#f8f9fa');
            }
        }, function() {
            if (!$(this).hasClass('active-chat')) {
                $(this).css('background-color', 'transparent');
            }
        });
    }

    function filterContacts() {
        let query = $('#search_inbox').val().toLowerCase();
        $('.contact-item').each(function() {
            let text = $(this).text().toLowerCase();
            if (text.indexOf(query) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    function viewChatHistory(senderNumber, senderName = '') {
        // Hide empty state properly (d-flex overrides jQuery hide)
        $('#chat_empty_state').removeClass('d-flex').addClass('d-none');
        
        // Update header
        $('#modalSenderNumber').text(senderNumber);
        $('#chat_header_name').text(senderName || senderNumber);
        
        // Style active contact in list
        $('.contact-item').removeClass('active-chat').css({'background-color': 'transparent', 'border-left': 'none'});
        let activeEl = $(`.contact-item[data-number="${senderNumber}"]`);
        activeEl.addClass('active-chat').css({'background-color': '#f0f2f5', 'border-left': '4px solid #434afa'});
        
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
                $('#replyFooter').css('display', 'flex'); // Use flex for layout
                $('#replyExpiredAlert').hide();
            } else {
                $('#replyFooter').hide();
                $('#replyExpiredAlert').show();
            }
        } else {
            $('#replyFooter').hide();
            $('#replyExpiredAlert').hide();
        }

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
