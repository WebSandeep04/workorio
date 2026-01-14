@extends('layouts.app')

@section('title', 'Event Client Links')
@section('page_title', 'Event Client Links')

@section('content')
<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
            <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Event Client Links</h6>
        </div>
        <div class="card-body">
            <div id="eventClientContainer">Loading...</div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventClientModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
        <h6 class="modal-title mb-0" id="eventClientModalLabel">Manage Clients</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modal_event_id" />
        <div class="mb-3">
            <label class="form-label fw-bold">Event: <span id="modal_event_name" class="text-primary"></span></label>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Select Clients:</label>
            <div id="modal_clients" class="row"></div>
        </div>
        <div class="mb-3" id="clientSocialInfo">
            <label class="form-label fw-bold">Client Social Handles:</label>
            <div id="clientSocialList" class="text-muted small"></div>
        </div>
        <div class="alert alert-danger d-none mt-2" id="eventClientError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-sm btn-primary" id="saveEventClient">Save</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    let clients = [];
    let socialHandles = [];
    let relationships = {};
    let clientSocialHandles = {};

    function loadData(){
        $.get("{{ route('calendar-event-client.fetch') }}").then(function(data){
            clients = data.clients || [];
            socialHandles = data.social_handles || [];
            relationships = data.relationships || {};
            clientSocialHandles = data.client_social_handles || {};
            renderEvents(data.events || []);
        }).fail(function(){
            $('#eventClientContainer').html('<div class="alert alert-danger">Failed to load data</div>');
        });
    }

    function renderEvents(events){
        let html = '';
        if(!events || events.length === 0){
            html = '<div class="text-muted">No events found.</div>';
        } else {
            html = '<div class="table-responsive"><table class="table table-sm table-hover table-bordered align-middle">';
            html += '<thead class="table-secondary"><tr><th>Event Name</th><th>Linked Clients</th><th>Action</th></tr></thead><tbody>';
            events.forEach(function(event){
                const linkedClients = (relationships[event.id] || []).map(function(cId){
                    const client = clients.find(c => c.id == cId);
                    return client ? client.name : null;
                }).filter(Boolean).join(', ');
                html += `<tr>
                    <td>${event.name}</td>
                    <td>${linkedClients || '<span class="text-muted">None</span>'}</td>
                    <td>
                        <button class="btn btn-sm btn-success edit-relation-btn" data-id="${event.id}" data-name="${event.name}">
                            <i class="bi bi-pencil"></i> Manage
                        </button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table></div>';
        }
        $('#eventClientContainer').html(html);
    }

    function getClientSocialInfo(clientId){
        const handleIds = clientSocialHandles[clientId] || [];
        if(handleIds.length === 0) return 'No social handles';
        const handles = handleIds.map(function(shId){
            const handle = socialHandles.find(h => h.id == shId);
            return handle ? handle.name : null;
        }).filter(Boolean);
        return handles.join(', ') || 'No social handles';
    }

    function updateClientSocialInfo(){
        const selectedClientIds = $('.client-checkbox:checked').map(function(){ return $(this).val(); }).get();
        if(selectedClientIds.length === 0){
            $('#clientSocialList').html('<span class="text-muted">Select clients to see their social handles</span>');
            return;
        }
        let infoHtml = '<ul class="mb-0">';
        selectedClientIds.forEach(function(cId){
            const client = clients.find(c => c.id == cId);
            if(client){
                const socialInfo = getClientSocialInfo(cId);
                infoHtml += `<li><strong>${client.name}:</strong> ${socialInfo}</li>`;
            }
        });
        infoHtml += '</ul>';
        $('#clientSocialList').html(infoHtml);
    }

    function openModal(eventId, eventName){
        $('#modal_event_id').val(eventId);
        $('#modal_event_name').text(eventName);
        
        // Render checkboxes for clients
        let checkboxesHtml = '';
        if(clients.length === 0){
            checkboxesHtml = '<div class="text-muted">No active clients available.</div>';
        } else {
            clients.forEach(function(client){
                const isChecked = (relationships[eventId] || []).includes(client.id);
                checkboxesHtml += `<div class="col-md-6 col-lg-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input client-checkbox" type="checkbox" value="${client.id}" id="client_${client.id}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="client_${client.id}">${client.name}</label>
                    </div>
                </div>`;
            });
        }
        $('#modal_clients').html(checkboxesHtml);
        updateClientSocialInfo();
        $('#eventClientError').addClass('d-none').text('');
        new bootstrap.Modal(document.getElementById('eventClientModal')).show();
    }

    $(document).on('click', '.edit-relation-btn', function(){
        const eventId = $(this).data('id');
        const eventName = $(this).data('name');
        openModal(eventId, eventName);
    });

    $(document).on('change', '.client-checkbox', function(){
        updateClientSocialInfo();
    });

    $('#saveEventClient').on('click', function(){
        const eventId = $('#modal_event_id').val();
        if(!eventId){
            $('#eventClientError').removeClass('d-none').text('Invalid event');
            return;
        }
        
        const selectedClients = $('.client-checkbox:checked').map(function(){
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ route('calendar-event-client.update') }}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                event_id: eventId,
                client_ids: selectedClients
            }
        }).done(function(response){
            if(response.success){
                bootstrap.Modal.getInstance(document.getElementById('eventClientModal')).hide();
                loadData();
            } else {
                $('#eventClientError').removeClass('d-none').text(response.message || 'Failed to save');
            }
        }).fail(function(xhr){
            $('#eventClientError').removeClass('d-none').text(xhr.responseJSON?.message || 'Failed to save');
        });
    });

    $(document).ready(loadData);
})();
</script>
@endpush

