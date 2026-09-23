@extends('layouts.app')

@section('title', 'Send Notifications')
@section('page_title', 'Send Notifications')

@push('styles')
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  
  .modern-card {
    border-radius: 5px;
    border: 1px solid #f2f4f7;
    background: #fff;
    box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05);
    padding: 1.5rem;
    margin-bottom: 0.5rem;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.95rem;
  }
  
  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
    width: 100%;
  }
  
  .form-control-modern:focus {
    border-color: #434AFA;
    box-shadow: 0 0 0 4px rgba(67, 74, 250, 0.1);
    outline: none;
  }

  .btn-modern-primary {
    background: #434AFA;
    color: white;
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    cursor: pointer;
  }
  
  .btn-modern-primary:hover:not(:disabled) {
    background: #3538d4;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(67, 74, 250, 0.2);
    color: white;
  }

  .btn-modern-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .info-alert {
    background-color: #e0f2fe;
    border-left: 4px solid #0284c7;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0 4px 4px 0;
    color: #0c4a6e;
    font-size: 0.9rem;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="row justify-content-center">
    <div class="col-md-8">
      
      <div class="modern-card">
        <h5 class="mb-4" style="color: #333; font-weight: 600;">
          <i class="bi bi-bell-fill me-2 text-primary"></i> Broadcast Notification
        </h5>
        
        <div class="info-alert">
          <i class="bi bi-info-circle-fill me-1"></i>
          This will send a push notification to all users who have logged into the mobile app and allowed notification permissions.
        </div>

        <form id="notificationForm">
          @csrf
          <div class="mb-4">
            <label for="title" class="form-label-modern">Notification Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control-modern" id="title" name="title" required placeholder="e.g. New Feature Update!">
          </div>

          <div class="mb-4">
            <label for="body" class="form-label-modern">Notification Message <span class="text-danger">*</span></label>
            <textarea class="form-control-modern" id="body" name="body" rows="4" required placeholder="Type your message here..."></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" id="sendBtn" class="btn-modern-primary">
              <i class="bi bi-send-fill"></i> Send Notification
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function() {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
    };

    $('#notificationForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#sendBtn');
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Sending...');
        
        $.ajax({
            url: "{{ route('software-setup.notifications.send') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message, 'Success');
                    $('#notificationForm')[0].reset();
                } else {
                    toastr.error(response.message || 'Error sending notification', 'Error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong';
                if(xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                toastr.error(errorMsg, 'Error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
