
<?php $__env->startSection('title', 'WhatsApp Inbox'); ?>
<?php $__env->startSection('page_title', 'WhatsApp Inbox'); ?>
<?php $__env->startSection('content'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .stat-card { border-radius: 8px; border: 1px solid #f0f0f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s; }
  .stat-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
  .stat-icon-box { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 1.2rem; }
  .stat-title { font-size: 0.65rem; font-weight: 700; color: #6c757d; letter-spacing: 0.5px; }
  .stat-value { font-size: 1.2rem; font-weight: 700; color: #212529; line-height: 1.2; }
  
  .filter-section { background-color: #434afa; border-radius: 8px; padding: 12px 16px; margin-bottom: 12px; }
  .filter-label { color: #fff; font-size: 0.7rem; font-weight: 500; margin-bottom: 4px; display: flex; align-items: center; gap: 4px; }
  .filter-select { font-size: 0.8rem; border: none; padding: 6px 10px; box-shadow: none; }
  
  .search-bar-container { background: #fff; border: 1px solid #e2e5ec; border-radius: 8px; padding: 6px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
  .search-input { border: none; font-size: 0.85rem; padding: 5px 10px; width: 300px; box-shadow: none !important; }
  .btn-add { background-color: #434afa; color: white; border: none; border-radius: 4px; font-weight: 500; padding: 6px 16px; font-size: 0.85rem; }
  .btn-add:hover { background-color: #3238d9; color: white; }

  .custom-table-card { background: #fff; border-radius: 8px; border: 1px solid #e2e5ec; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden; }
  .custom-table th { background: #f8f9fa; border-bottom: 1px solid #e2e5ec; font-size: 0.7rem; font-weight: 600; color: #6c757d; padding: 12px 16px; letter-spacing: 0.5px; }
  .custom-table td { padding: 12px 16px; font-size: 0.85rem; vertical-align: middle; border-bottom: 1px solid #f4f4f6; color: #495057; }
  .custom-table tr:hover { background-color: #fcfcfd; }
  .custom-table tr:last-child td { border-bottom: none; }
  .badge-type { font-size: 0.65rem; padding: 4px 8px; font-weight: 500; }
</style>
<?php $__env->stopPush(); ?>

<div class="container-fluid px-0">
    <!-- Top Stats Row -->
    <div class="row mb-3">
        <div class="col-12 col-md-4 col-xl-3">
            <div class="card stat-card h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="stat-icon-box me-3" style="background-color: rgba(67, 74, 250, 0.1); color: #434afa;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stat-title text-capitalize">Total Numbers</div>
                        <div class="stat-value" id="total_numbers_count">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-bar-container">
        <div class="d-flex align-items-center w-100">
            <i class="bi bi-search text-muted ms-2 me-2"></i>
            <input type="text" class="search-input flex-grow-1 w-100" placeholder="Search leads, contacts, emails...">
        </div>
    </div>

    <!-- Table Section -->
    <div class="custom-table-card">
        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th class="text-capitalize border-0">Number</th>
                        <th class="text-capitalize border-0">Latest Message</th>
                        <th class="text-capitalize border-0">Type</th>
                        <th class="text-capitalize border-0">Last Received</th>
                        <th class="text-capitalize border-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="messages_container">
                    <tr><td colspan="4" class="text-center py-4">Loading messages...</td></tr>
                </tbody>
            </table>
        </div>
        <!-- Pagination Section -->
        <div class="d-flex justify-content-between align-items-center p-3 border-top" id="pagination_container" style="display: none;">
            <div class="text-muted" style="font-size: 0.85rem;" id="pagination_info">Showing 0 to 0 of 0 entries</div>
            <ul class="pagination pagination-sm mb-0" id="pagination_controls">
                <!-- Pagination buttons will go here -->
            </ul>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
            url: `<?php echo e(route('whatsapp-inbox.fetch')); ?>`,
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
            let text = msg.message_text ? msg.message_text : '<span class="text-muted fst-italic">(No text)</span>';
            let typeBadge = msg.message_type !== 'text' 
                ? `<span class="badge bg-secondary badge-type">${msg.message_type}</span>` 
                : `<span class="badge bg-light text-dark border badge-type">text</span>`;
            let time = new Date(msg.received_at).toLocaleString();
            
            html += `
                <tr>
                    <td class="fw-medium text-dark">${msg.sender_number}</td>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\workorio\resources\views/whatsapp_inbox/index.blade.php ENDPATH**/ ?>