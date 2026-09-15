@extends('layouts.app')
@section('title', 'Signal Tasks')
@section('page_title', 'Signal Tasks')

@push('styles')
<style>
  .data-table-card .custom-table thead th {  
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  }
  .container-fluid {
    padding: 0.5rem;
    padding-right: 0.5rem;
    margin-right: 0;
  }
  
  .table-search {
    display: flex;
    flex-direction: row;
    gap: 0.5rem;
    align-items: center;
  }
  
  .table-search-field {
    width: 100%;
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #eceef3;
    border-radius: 8px;
    padding: 0.5rem 1rem;
  }
  
  .table-search-field i {
    color: #6c757d;
    margin-right: 0.5rem;
  }
  
  .table-search-field input {
    border: none;
    outline: none;
    width: 100%;
    font-size: 0.875rem;
  }

  .table-search-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    border: 1px solid #eceef3;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .data-table-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #eceef3;
    box-shadow: 0px 4px 4px 0px #0000000A;
  }

  .data-table-card .custom-table tbody td {
    font-size: 0.75rem;
  }

  .custom-table {
    margin-bottom: 0 !important;
  }

  .custom-table th {
    padding: 6px 10px;
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    font-size: 13px;
    border-bottom: 1px solid #dee2e6;
  }

  .custom-table td {
    padding: 6px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
  <div class="table-search mb-2 mt-2">
    <div class="table-search-field">
      <i class="bi bi-search"></i>
      <input type="text" id="searchInput" placeholder="Search tasks..." />
    </div>
  </div>

  <div class="data-table-card">
    <div class="table-responsive">
      <table class="table custom-table" id="signalTaskTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sender</th>
            <th>Sender Phone</th>
            <th>Chat</th>
            <th>Message</th>
          </tr>
        </thead>
        <tbody id="signalTaskTableBody">
          <tr>
            <td colspan="5" class="text-center">
              <i class="bi bi-arrow-repeat spin"></i> Loading tasks...
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let allTasks = [];

    fetchSignalTasks();

    function fetchSignalTasks() {
        $.ajax({
            url: "{{ route('signal-task.fetch') }}",
            type: "GET",
            success: function(response) {
                allTasks = response;
                renderTasks(allTasks);
            },
            error: function(xhr) {
                console.error("Error fetching tasks", xhr);
                $('#signalTaskTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load tasks.</td></tr>');
            }
        });
    }

    function renderTasks(tasks) {
        let html = '';
        if(tasks.length > 0) {
            tasks.forEach(function(task) {
                html += `
                    <tr>
                        <td>${task.id}</td>
                        <td>${task.sender || 'N/A'}</td>
                        <td>${task.sender_phone || 'N/A'}</td>
                        <td>${task.chat || 'N/A'}</td>
                        <td>${task.message_text || 'N/A'}</td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="5" class="text-center">No tasks found.</td></tr>';
        }
        $('#signalTaskTableBody').html(html);
    }

    // Basic frontend search
    $('#searchInput').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        let filtered = allTasks.filter(function(task) {
            return (
                (task.sender && task.sender.toLowerCase().includes(value)) ||
                (task.sender_phone && task.sender_phone.toLowerCase().includes(value)) ||
                (task.chat && task.chat.toLowerCase().includes(value)) ||
                (task.message_text && task.message_text.toLowerCase().includes(value))
            );
        });
        renderTasks(filtered);
    });
});
</script>
@endpush
