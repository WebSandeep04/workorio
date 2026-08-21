@extends('layouts.app')
@section('title', 'WhatsApp Inbox')
@section('page_title', 'WhatsApp Inbox')
@section('content')

@push('styles')
<style>
  .inbox-card { background: #fff; border-radius: 10px; border: 1px solid #eceef3; box-shadow: 0px 4px 4px 0px #0000000A; overflow: hidden; }
  .message-item { padding: 15px; border-bottom: 1px solid #f4f4f6; transition: background 0.2s ease; }
  .message-item:hover { background: rgba(102, 126, 234, 0.08); }
  .message-sender { font-weight: 600; color: #333; font-size: 0.95rem; }
  .message-time { font-size: 0.75rem; color: #888; }
  .message-body { font-size: 0.85rem; color: #555; margin-top: 5px; }
  .message-type { font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; background: #e9ecef; color: #495057; }
</style>
@endpush

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card inbox-card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-family: Montserrat; font-weight: 600;">Incoming Messages</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="loadMessages()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
            </div>
            <div class="card-body p-0">
                <div id="messages_container">
                    <div class="text-center py-4">Loading messages...</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        loadMessages();
    });

    function loadMessages() {
        $('#messages_container').html('<div class="text-center py-4">Loading messages...</div>');
        
        $.ajax({
            url: `{{ route('whatsapp-inbox.fetch') }}`,
            type: 'GET',
            success: function(response) {
                let messages = response.data;
                let html = '';

                if (messages.length === 0) {
                    html = '<div class="text-center py-4 text-muted">No incoming messages found.</div>';
                } else {
                    messages.forEach(msg => {
                        let text = msg.message_text ? msg.message_text : '(No text)';
                        let typeBadge = msg.message_type !== 'text' ? `<span class="message-type ms-2">${msg.message_type}</span>` : '';
                        let time = new Date(msg.received_at).toLocaleString();
                        
                        html += `
                            <div class="message-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="message-sender">${msg.sender_number} ${typeBadge}</div>
                                    <div class="message-time">${time}</div>
                                </div>
                                <div class="message-body">${text}</div>
                            </div>
                        `;
                    });
                }
                
                $('#messages_container').html(html);
            },
            error: function() {
                $('#messages_container').html('<div class="text-center py-4 text-danger">Error loading messages.</div>');
            }
        });
    }
</script>
@endpush
