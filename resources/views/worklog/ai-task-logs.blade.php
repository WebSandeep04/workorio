@extends('layouts.app')
@section('title', 'AI Task Logs')
@section('page_title', 'AI Task Logs')

@section('content')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .container-fluid { padding: 0.5rem; padding-right: 0.5rem; margin-right: 0; }
        .table-search { width: 100%; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .table-search-field { flex: 1; display: inline-flex; align-items: center; gap: 0.35rem; background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 2px; padding: 0.35rem 0.9rem; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6); }
        .table-search-btn { padding: 0.35rem 1rem; background: #434afa; color: white; border: none; border-radius: 2px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; white-space: nowrap; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); display: inline-flex; align-items: center; }
        .table-search-btn:hover { background: #3538d4; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(67, 74, 250, 0.4); color: white; text-decoration: none; }
        .table-search-field i { color: #9ca3af; font-size: 0.85rem; }
        .table-search-field input { border: none; background: transparent; font-size: 0.85rem; width: 100%; outline: none; color: #111827; }
        .modern-card { padding: 0; margin-bottom: 0.5rem; }
        .modern-card-body { padding: 0.5rem; }
        .data-table-card { border-radius: 5px; border: 1px solid #f2f4f7; background: #fff; box-shadow: 0px 30px 60px rgba(15, 23, 42, 0.08); overflow: hidden; }
        .data-table-card .modern-card-body { padding: 0; }
        .data-table-card .table-responsive { border-radius: 5px; border: none; box-shadow: none; padding: 0.5rem 0.75rem 1rem; overflow-x: auto; background: transparent; scrollbar-color: #434AFA #e4e7ec; }
        .data-table-card .table-responsive::-webkit-scrollbar { height: 8px; }
        .data-table-card .table-responsive::-webkit-scrollbar-track { background: #e4e7ec; border-radius: 999px; }
        .data-table-card .table-responsive::-webkit-scrollbar-thumb { background: #434AFA; border-radius: 999px; }
        .data-table-card .custom-table { border-collapse: separate; border-spacing: 0; width: 100%; font-size: 0.85rem; background: transparent; table-layout: auto; min-width: 100%; }
        .data-table-card .custom-table thead th { background: #fff; color: #000; font-size: 0.65rem; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 700; padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #f1f3f5; position: sticky; top: 0; z-index: 5; white-space: nowrap; font-family: Montserrat; }
        .data-table-card .custom-table tbody td { font-size: 0.85rem; padding: 0.65rem 0.75rem; color: #000; border-bottom: 1px solid #f4f4f6; text-align: left; background: transparent; white-space: nowrap; font-family: Montserrat; }
        .data-table-card .custom-table tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
        .data-table-card .custom-table tbody tr:hover { background: #f8f9ff; box-shadow: 0px 8px 18px rgba(124, 58, 237, 0.08); transform: translateY(-1px); }
        .data-table-card .custom-table tbody tr:last-child td { border-bottom: none; }
        
        .pagination .page-link { color: #434afa; border: 2px solid #e0e0e0; border-radius: 6px; padding: 0.25rem 0.5rem; margin: 0 2px; font-size: 10px; transition: all 0.3s ease; font-weight: 500; }
        .pagination .page-item.active .page-link { background: #434afa; border-color: #434afa; color: white; box-shadow: 0 2px 8px rgba(67, 74, 250, 0.3); }
        .pagination .page-link:hover { background: rgba(67, 74, 250, 0.15); border-color: #434afa; transform: translateY(-1px); }
        
        .dataTables_wrapper .dataTables_paginate { padding-top: 10px; }
        
        .btn-action-edit { color: white; background: #343AFA !important; border-radius: 4px; padding: 0.25rem 0.5rem; border: none; }
    </style>
@endpush

<div class="container-fluid px-2">
    <!-- Search -->
    <div class="table-search mb-2">
        <div class="table-search-field">
            <i class="bi bi-search"></i>
            <input type="text" id="aiLogSearch" placeholder="Search logs...">
        </div>
    </div>

    <!-- Table Card -->
    <div class="modern-card data-table-card">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table" id="aiLogsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Timestamp</th>
                            <th>Model</th>
                            <th>Prompt Tokens</th>
                            <th>Completion Tokens</th>
                            <th>Total Tokens</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Payload/Response -->
<div class="modal fade" id="payloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AI Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Payload (Request)</h6>
                <pre id="payloadContent" style="background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;"></pre>
                
                <h6 class="mt-3">Response</h6>
                <pre id="responseContent" style="background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;"></pre>
                
                <h6 class="mt-3 text-danger" id="errorHeader" style="display: none;">Error Message</h6>
                <pre id="errorContent" class="text-danger" style="display: none; background: #fdf2f2; padding: 10px; border-radius: 5px;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
$(document).ready(function() {
    let table = $('#aiLogsTable').DataTable({
        processing: true,
        serverSide: true,
        dom: '<"top">rt<"bottom"p><"clear">',
        language: {
            loadingRecords: "",
            processing: ""
        },
        ajax: {
            url: "{{ route('ai-task-log.fetch') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" }
        },
        columns: [
            { data: 'id', name: 'id' },
            { 
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return moment(data).format('DD-MMM-YYYY HH:mm:ss');
                }
            },
            { data: 'model', name: 'model' },
            { data: 'prompt_tokens', name: 'prompt_tokens' },
            { data: 'completion_tokens', name: 'completion_tokens' },
            { data: 'total_tokens', name: 'total_tokens' },
            { 
                data: null, 
                name: 'price',
                searchable: false,
                orderable: false,
                render: function(data, type, row) {
                    let pTokens = row.prompt_tokens || 0;
                    let cTokens = row.completion_tokens || 0;
                    let model = (row.model || '').toLowerCase();
                    let price = 0;
                    
                    // Basic pricing logic (per 1k tokens)
                    if (model.includes('gpt-4')) {
                        price = (pTokens * 0.005) + (cTokens * 0.015);
                    } else if (model.includes('gpt-5-nano')) {
                        // Assuming nano pricing is similar to mini
                        price = (pTokens * 0.00015) + (cTokens * 0.00060);
                    } else {
                        // Default fallback (e.g. gpt-3.5)
                        price = (pTokens * 0.0005) + (cTokens * 0.0015);
                    }
                    
                    if (price === 0) return '$0.0000';
                    return '$' + price.toFixed(4);
                }
            },
            { 
                data: 'error_message', 
                name: 'status',
                render: function(data) {
                    if (data) {
                        return '<span class="badge bg-danger">Error</span>';
                    }
                    return '<span class="badge bg-success">Success</span>';
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    const logData = encodeURIComponent(JSON.stringify(row));
                    return `<button class="btn btn-sm text-white view-log-btn" style="background: #434afa;" data-log="${logData}">View Details</button>`;
                }
            }
        ],
        order: [[0, 'desc']]
    });

    $('#aiLogSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $(document).on('click', '.view-log-btn', function() {
        const log = JSON.parse(decodeURIComponent($(this).data('log')));
        
        $('#payloadContent').text(JSON.stringify(log.payload, null, 2));
        $('#responseContent').text(JSON.stringify(log.response, null, 2));
        
        if (log.error_message) {
            $('#errorHeader').show();
            $('#errorContent').text(log.error_message).show();
        } else {
            $('#errorHeader').hide();
            $('#errorContent').hide();
        }

        $('#payloadModal').modal('show');
    });
});
</script>
@endpush
