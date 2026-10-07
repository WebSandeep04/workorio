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
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="search_inbox" class="form-control border-start-0 ps-0" placeholder="Search contacts..." onkeyup="filterContacts()">
                </div>
                <div class="d-flex gap-1" style="font-size: 0.8rem;">
                    <button class="btn btn-sm btn-primary flex-fill active" id="filter_all" onclick="setFilter('all')">All (<span id="count_all">0</span>)</button>
                    <button class="btn btn-sm btn-outline-primary flex-fill" id="filter_unread" onclick="setFilter('unread')">Unread (<span id="count_unread">0</span>)</button>
                    <button class="btn btn-sm btn-outline-primary flex-fill" id="filter_read" onclick="setFilter('read')">Read (<span id="count_read">0</span>)</button>
                    <button class="btn btn-sm btn-outline-primary flex-fill" id="filter_converted" onclick="setFilter('converted')">Converted (<span id="count_converted">0</span>)</button>
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
                <div class="d-flex">
                    <button class="btn btn-sm me-2" style="background-color: #434afa; color: white; display: none;" id="btnMarkAsRead" onclick="markChatAsRead()">
                        <i class="bi bi-check2-all"></i> Mark as Read
                    </button>
                    <button class="btn btn-sm" style="background-color: #434afa; color: white; display: none;" onclick="openLeadModalWithData()" id="btnConvertToLead">
                        <i class="bi bi-person-plus-fill"></i> Convert to Lead
                    </button>
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

@include('partials.add-lead-modal')
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

    function openLeadModalWithData() {
        let name = $('#chat_header_name').text();
        let number = $('#modalSenderNumber').text();
        
        if (name && name !== '-' && name !== number) {
            $('#add_lead_contactPerson').val(name);
        } else {
            $('#add_lead_contactPerson').val('');
        }
        $('#add_lead_contactNumber').val(number);
        
        $('#addLeadModal').modal('show');
    }

    let currentFilter = 'all';

    function loadMessages() {
        $('#messages_container').html('<tr><td colspan="5" class="text-center py-4">Loading messages...</td></tr>');
        $('#pagination_container').hide();
        
        $.ajax({
            url: `{{ route('whatsapp-inbox.fetch') }}`,
            type: 'GET',
            success: function(response) {
                allMessages = response.data;
                
                // We still want to calculate unread counts properly based on incoming messages
                if (allMessages.length === 0) {
                    $('#messages_container').html('<tr><td colspan="5" class="text-center py-4 text-muted">No messages found.</td></tr>');
                    $('#pagination_container').hide();
                } else {
                    // Group messages by contact number (sender_number for incoming, receiver_number for outgoing)
                    let groupedMessages = {};
                    allMessages.forEach(msg => {
                        let isReply = ['reply', 'image_reply', 'document_reply'].includes(msg.message_type);
                        let contactNumber = isReply ? msg.receiver_number : msg.sender_number;
                        
                        if (!contactNumber) return;

                        if (!groupedMessages[contactNumber]) {
                            // Initialize
                            groupedMessages[contactNumber] = {...msg};
                            groupedMessages[contactNumber].contact_number = contactNumber;
                            groupedMessages[contactNumber].unread_count = 0;
                            
                            // If it's the first message we see and it's incoming unread, count it
                            if (!isReply && msg.is_read == 0) {
                                groupedMessages[contactNumber].unread_count = 1;
                            }
                        } else {
                            // Update unread count for incoming messages
                            if (!isReply && msg.is_read == 0) {
                                groupedMessages[contactNumber].unread_count++;
                            }
                            
                            // Retain name and is_lead from incoming messages (replies often don't have sender_name set to the contact's name)
                            let currentName = groupedMessages[contactNumber].sender_name;
                            let newName = msg.sender_name;
                            let bestName = newName && !isReply ? newName : (currentName ? currentName : newName);
                            let isLead = groupedMessages[contactNumber].is_lead || msg.is_lead;

                            let existingDate = new Date(groupedMessages[contactNumber].received_at);
                            let newDate = new Date(msg.received_at);

                            if (newDate > existingDate) {
                                let oldUnread = groupedMessages[contactNumber].unread_count;
                                groupedMessages[contactNumber] = {...msg};
                                groupedMessages[contactNumber].contact_number = contactNumber;
                                groupedMessages[contactNumber].unread_count = oldUnread;
                                groupedMessages[contactNumber].sender_name = bestName;
                                groupedMessages[contactNumber].is_lead = isLead;
                            } else {
                                groupedMessages[contactNumber].sender_name = bestName;
                                groupedMessages[contactNumber].is_lead = isLead;
                            }
                        }
                    });

                    // Ensure sender_number is set correctly so viewChatHistory works as expected
                    Object.values(groupedMessages).forEach(msg => {
                        msg.sender_number = msg.contact_number;
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
        let unreadCount = 0;
        let readCount = 0;
        let convertedCount = 0;

        let html = '';
        if (uniqueMessages.length === 0) {
            html = '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No chats found.</div>';
        } else {
            uniqueMessages.forEach(msg => {
                if (msg.is_lead) {
                    convertedCount++;
                } else {
                    if (msg.unread_count > 0) unreadCount++;
                    else readCount++;
                }

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
                
                let isMedia = msg.media_url || ['image', 'document', 'image_reply', 'document_reply'].includes(msg.message_type);
                let mediaIcon = isMedia ? '<i class="bi bi-image me-1"></i> ' : '';
                
                let isReply = ['reply', 'image_reply', 'document_reply'].includes(msg.message_type);
                let senderIndicator = isReply ? '<i class="bi bi-check2-all text-primary me-1" style="font-size: 1.1em;"></i>' : '';

                let unreadBadge = msg.unread_count > 0 ? `<span class="badge bg-success rounded-pill ms-2" style="font-size: 0.7rem;">${msg.unread_count}</span>` : '';
                let fwClass = msg.unread_count > 0 ? 'fw-bold' : '';
                
                let convertedBadge = msg.is_lead ? '<span class="badge bg-info rounded-pill ms-2" style="font-size: 0.7rem;">Converted</span>' : '';
                
                html += `
                    <div class="contact-item p-3 border-bottom" data-unread="${msg.unread_count > 0 ? 'true' : 'false'}" data-converted="${msg.is_lead ? 'true' : 'false'}" onclick="viewChatHistory('${msg.sender_number}', '${msg.sender_name || ''}')" style="cursor: pointer; transition: background 0.2s;" data-number="${msg.sender_number}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-truncate text-dark ${fwClass}" style="max-width: 70%; font-size: 0.95rem;">${senderName}</h6>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">${timeStr}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted text-truncate d-flex align-items-center ${fwClass}" style="font-size: 0.85rem; max-width: 70%;">
                                ${senderIndicator}${mediaIcon}${text}
                            </div>
                            <div>
                                ${unreadBadge}
                                ${convertedBadge}
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        $('#contacts_list').html(html);

        let activeCount = uniqueMessages.length - convertedCount;
        $('#count_all').text(unreadCount);
        $('#count_unread').text(unreadCount);
        $('#count_read').text(readCount);
        $('#count_converted').text(convertedCount);
        $('#total_numbers_count').text(unreadCount);
        
        // Re-apply filter
        filterContacts();
        
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

    function setFilter(filterType) {
        currentFilter = filterType;
        $('#filter_all, #filter_read, #filter_unread, #filter_converted').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(`#filter_${filterType}`).removeClass('btn-outline-primary').addClass('active btn-primary');
        filterContacts();
    }

    function filterContacts() {
        let query = $('#search_inbox').val().toLowerCase();
        $('.contact-item').each(function() {
            let text = $(this).text().toLowerCase();
            let isUnread = $(this).data('unread') === true;
            let isConverted = $(this).data('converted') === true;

            let matchQuery = text.indexOf(query) > -1;
            let matchFilter = true;
            
            if (currentFilter !== 'converted' && isConverted) matchFilter = false;
            if (currentFilter === 'unread' && !isUnread) matchFilter = false;
            if (currentFilter === 'read' && isUnread) matchFilter = false;
            if (currentFilter === 'converted' && !isConverted) matchFilter = false;
            if (currentFilter === 'all' && !isUnread) matchFilter = false;

            if (matchQuery && matchFilter) {
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
        
        let msgObj = uniqueMessages.find(m => m.sender_number === senderNumber);
        if (msgObj && msgObj.is_lead) {
            $('#btnConvertToLead').hide();
        } else {
            $('#btnConvertToLead').show();
        }
        
        // Style active contact in list
        $('.contact-item').removeClass('active-chat').css({'background-color': 'transparent', 'border-left': 'none'});
        let activeEl = $(`.contact-item[data-number="${senderNumber}"]`);
        activeEl.addClass('active-chat').css({'background-color': '#f0f2f5', 'border-left': '4px solid #434afa'});
        
        // Show or hide "Mark as Read" button
        if (activeEl.data('unread') === true) {
            $('#btnMarkAsRead').show();
        } else {
            $('#btnMarkAsRead').hide();
        }
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

    function markChatAsRead() {
        const senderNumber = $('#modalSenderNumber').text();
        if (!senderNumber || senderNumber === '-') return;
        
        const activeEl = $(`.contact-item[data-number="${senderNumber}"]`);
        if (activeEl.length === 0 || activeEl.data('unread') === false) return;
        
        let $btn = $('#btnMarkAsRead');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Marking...');

        $.ajax({
            url: `{{ route('whatsapp-inbox.read') }}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sender_number: senderNumber
            },
            success: function(res) {
                if (res.success) {
                    let msgObj = uniqueMessages.find(m => m.sender_number === senderNumber);
                    if (msgObj) msgObj.unread_count = 0;
                    
                    // Update DOM elements instead of full render
                    activeEl.data('unread', false);
                    activeEl.find('.bg-success').remove(); // remove badge
                    activeEl.find('h6').removeClass('fw-bold');
                    activeEl.find('.text-muted.text-truncate').removeClass('fw-bold');
                    
                    // Update counts
                    let unreadCount = parseInt($('#count_unread').text()) - 1;
                    let readCount = parseInt($('#count_read').text()) + 1;
                    $('#count_unread').text(Math.max(0, unreadCount));
                    $('#count_read').text(readCount);
                    
                    // Hide button
                    $btn.hide();
                    
                    // Refresh filter visibility if needed
                    filterContacts();
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="bi bi-check2-all"></i> Mark as Read');
            }
        });
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
