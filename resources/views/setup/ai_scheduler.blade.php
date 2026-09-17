@extends('layouts.app')

@section('title', 'AI Scheduler Setup')
@section('page_title', 'AI Scheduler Setup')

@push('styles')
<style>
  .container-fluid {
    padding: 0.5rem 1rem;
    max-width: 1400px;
    margin: 0 auto;
  }

  .page-header {
    background: #fff;
    padding: 1.5rem 2rem;
    border-radius: 5px;
    color: black;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
  }
  
  .page-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .modern-card {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0px 10px 30px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    margin-bottom: 2rem;
    border: 1px solid #f2f4f7;
  }

  .tabs-container {
    display: flex;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    overflow-x: auto;
    padding: 0 1rem;
  }

  .tab-btn {
    padding: 1rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    transition: all 0.3s ease;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .tab-btn:hover, .tab-btn.active {
    color: #434AFA;
    background: rgba(67, 74, 250, 0.05);
  }

  .tab-btn.active {
    border-bottom-color: #434AFA;
    background: #fff;
  }

  .tab-content {
    display: none;
    padding: 2rem;
  }

  .tab-content.active {
    display: block;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .form-label-modern {
    color: #434AFA;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
    font-size: 0.9rem;
  }

  .form-control-modern {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    width: 100%;
    font-size: 0.95rem;
  }

  .btn-modern {
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    border: none;
    cursor: pointer;
  }
  
  .btn-modern-primary {
    background: #434AFA;
    color: white;
  }

  .btn-modern-sm {
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
  }

  .week-offs-container {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .week-off-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: 1px solid #e0e0e0;
    cursor: pointer;
  }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h2><i class="bi bi-robot"></i> AI Scheduler Setup</h2>
    </div>

    <div class="modern-card">
        <div class="tabs-container">
            <button class="tab-btn active" data-tab="general">
                <i class="bi bi-gear"></i> General Settings
            </button>
            <button class="tab-btn" data-tab="passes">
                <i class="bi bi-clock-history"></i> Extra Fixed Passes
            </button>
        </div>

        <!-- General Settings -->
        <div class="tab-content active" id="tab-general">
            <form id="configForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Office Start Time</label>
                        <input type="time" name="office_start_time" id="office_start_time" class="form-control-modern" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Office End Time</label>
                        <input type="time" name="office_end_time" id="office_end_time" class="form-control-modern" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Office Day Frequency (Minutes)</label>
                        <input type="number" name="office_day_frequency" id="office_day_frequency" class="form-control-modern" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Office Day Lookback (Minutes)</label>
                        <input type="number" name="office_day_lookback" id="office_day_lookback" class="form-control-modern" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Off Day Frequency (Minutes)</label>
                        <input type="number" name="off_day_frequency" id="off_day_frequency" class="form-control-modern" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label-modern">Off Day Lookback (Minutes)</label>
                        <input type="number" name="off_day_lookback" id="off_day_lookback" class="form-control-modern" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label-modern">Weekly Off Days</label>
                    <div class="week-offs-container">
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @foreach($days as $index => $day)
                            <label class="week-off-checkbox">
                                <input type="checkbox" name="week_offs[]" value="{{ $index }}" class="week-off-input"> {{ $day }}
                            </label>
                        @endforeach
                    </div>
                    <small class="text-muted mt-1 d-block">On these days, the "Off Day" frequency and lookback will apply all day.</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn-modern btn-modern-primary" id="saveConfigBtn">
                        <i class="bi bi-save"></i> Save Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- Extra Fixed Passes -->
        <div class="tab-content" id="tab-passes">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="m-0" style="color: #434AFA;">Configured Passes</h5>
                <button class="btn-modern btn-modern-primary btn-modern-sm" onclick="openPassModal()">
                    <i class="bi bi-plus-lg"></i> Add New Pass
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Run At (IST)</th>
                            <th>Lookback (Minutes)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="passesTableBody">
                        <!-- Dynamic content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Pass Modal -->
<div class="modal fade" id="passModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passModalTitle">Add Fixed Pass</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="passForm">
                    <input type="hidden" id="pass_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="pass_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Run At (IST Time)</label>
                        <input type="time" class="form-control" id="pass_run_at" name="run_at" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lookback (Minutes)</label>
                        <input type="number" class="form-control" id="pass_lookback" name="lookback_minutes" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="pass_is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="pass_is_active">Active</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePass()">Save Pass</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Tabs logic
    $('.tab-btn').click(function() {
        $('.tab-btn').removeClass('active');
        $('.tab-content').removeClass('active');
        $(this).addClass('active');
        $('#tab-' + $(this).data('tab')).addClass('active');
    });

    loadData();

    $('#configForm').submit(function(e) {
        e.preventDefault();
        saveConfig();
    });
});

function loadData() {
    $.get("{{ route('ai-scheduler.setup.fetch') }}", function(res) {
        if (res.config) {
            $('#office_start_time').val(res.config.office_start_time.substring(0,5));
            $('#office_end_time').val(res.config.office_end_time.substring(0,5));
            $('#office_day_frequency').val(res.config.office_day_frequency);
            $('#office_day_lookback').val(res.config.office_day_lookback);
            $('#off_day_frequency').val(res.config.off_day_frequency);
            $('#off_day_lookback').val(res.config.off_day_lookback);
            
            $('.week-off-input').prop('checked', false);
            if (res.config.week_offs) {
                res.config.week_offs.forEach(day => {
                    $(`.week-off-input[value="${day}"]`).prop('checked', true);
                });
            }
        }
        
        renderPasses(res.passes);
    });
}

function renderPasses(passes) {
    let html = '';
    passes.forEach(pass => {
        html += `
            <tr>
                <td>${pass.name}</td>
                <td>${pass.run_at.substring(0,5)}</td>
                <td>${pass.lookback_minutes}</td>
                <td>
                    <span class="badge ${pass.is_active ? 'bg-success' : 'bg-danger'}">${pass.is_active ? 'Active' : 'Inactive'}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-info text-white" onclick="editPass(${pass.id}, '${pass.name}', '${pass.run_at}', ${pass.lookback_minutes}, ${pass.is_active})"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deletePass(${pass.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
    });
    $('#passesTableBody').html(html);
}

function saveConfig() {
    let data = $('#configForm').serialize();
    $.post("{{ route('ai-scheduler.setup.store') }}", data, function(res) {
        alert(res.message);
    }).fail(function() {
        alert('Error saving configuration.');
    });
}

function openPassModal() {
    $('#passForm')[0].reset();
    $('#pass_id').val('');
    $('#passModalTitle').text('Add Fixed Pass');
    $('#passModal').modal('show');
}

function editPass(id, name, run_at, lookback, is_active) {
    $('#passForm')[0].reset();
    $('#pass_id').val(id);
    $('#pass_name').val(name);
    $('#pass_run_at').val(run_at.substring(0,5));
    $('#pass_lookback').val(lookback);
    $('#pass_is_active').prop('checked', is_active == 1);
    $('#passModalTitle').text('Edit Fixed Pass');
    $('#passModal').modal('show');
}

function savePass() {
    let id = $('#pass_id').val();
    let url = id ? `/ai-scheduler/setup/pass/${id}` : "{{ route('ai-scheduler.setup.pass.store') }}";
    let type = id ? 'PUT' : 'POST';
    let data = $('#passForm').serialize();
    
    // Add CSRF token manually since we aren't using a form submission strictly
    data += '&_token=' + $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: url,
        type: type,
        data: data,
        success: function(res) {
            $('#passModal').modal('hide');
            loadData();
        },
        error: function(err) {
            alert('Error saving pass.');
        }
    });
}

function deletePass(id) {
    if (confirm('Are you sure you want to delete this pass?')) {
        $.ajax({
            url: `/ai-scheduler/setup/pass/${id}`,
            type: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                loadData();
            }
        });
    }
}
</script>
@endpush
