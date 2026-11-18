@extends('layouts.app')

@section('title', 'Email Alerts & Recipient Mapping')
@section('page_title', 'Email Alerts & Recipient Mapping')

@push('styles')
<style>
    .email-alerts-container {
        padding: 0.75rem;
        background: #f8f9fa;
    }
    .metric-card {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .matrix-table-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .matrix-table {
        margin-bottom: 0;
        font-size: 0.85rem;
    }
    .matrix-table th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        vertical-align: middle;
        padding: 0.65rem 0.75rem;
        border-bottom: 2px solid #cbd5e1;
    }
    .matrix-table td {
        vertical-align: middle;
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .matrix-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .user-sticky-col {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 2;
        min-width: 250px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.03);
    }
    .matrix-table thead th.user-sticky-col {
        background: #f1f5f9;
        z-index: 3;
    }
    .badge-time {
        font-size: 0.7rem;
        font-weight: 500;
        background: #e0e7ff;
        color: #3730a3;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .badge-category {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 600;
    }
    .custom-switch-lg .form-check-input {
        width: 2.2rem;
        height: 1.15rem;
        cursor: pointer;
    }
    .custom-switch-lg .form-check-input:checked {
        background-color: #434afa;
        border-color: #434afa;
    }
    .column-header-box {
        min-width: 170px;
        max-width: 210px;
    }
    .nav-tabs-modern {
        border-bottom: 2px solid #e2e8f0;
        gap: 0.5rem;
    }
    .nav-tabs-modern .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.6rem 1rem;
        border-radius: 8px 8px 0 0;
        background: transparent;
        transition: all 0.2s;
    }
    .nav-tabs-modern .nav-link:hover {
        color: #434afa;
        background: rgba(67, 74, 250, 0.05);
    }
    .nav-tabs-modern .nav-link.active {
        color: #434afa;
        border-bottom: 3px solid #434afa;
        background: #fff;
    }
</style>
@endpush

@section('content')
<div class="email-alerts-container">
    <!-- Header Page Row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-envelope-check-fill text-primary me-2"></i>Email Alerts & Recipient Mapping
            </h5>
            <small class="text-muted">Manage company-wide automated daily reports and select which users receive each email.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary px-3 py-2" style="font-size: 0.85rem;">
                <i class="bi bi-clock me-1"></i> {{ count($alerts) }} Automated Reports Active
            </span>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="metric-card mb-3">
        <form method="GET" action="{{ route('software-setup.email-alerts.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search employee name, code, or email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Branches</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i>Filter</button>
                @if(request()->anyFilled(['search', 'branch_id', 'department_id']))
                    <a href="{{ route('software-setup.email-alerts.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Category Tabs Navigation -->
    <ul class="nav nav-tabs nav-tabs-modern mb-3" id="emailCategoryTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-all-link" data-bs-toggle="tab" href="#tab-all" role="tab">
                <i class="bi bi-grid-3x3-gap-fill me-1"></i> All Reports ({{ count($alerts) }})
            </a>
        </li>
        @foreach($categories as $categoryName => $catAlerts)
            <li class="nav-item">
                <a class="nav-link" id="tab-{{ Str::slug($categoryName) }}-link" data-bs-toggle="tab" href="#tab-{{ Str::slug($categoryName) }}" role="tab">
                    {{ $categoryName }} ({{ count($catAlerts) }})
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Tabs Content Container -->
    <div class="tab-content" id="emailCategoryTabsContent">
        <!-- ALL TAB -->
        <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
            @include('software-setup.email-alerts.matrix-table', ['tableAlerts' => $alerts, 'users' => $users, 'recipientMap' => $recipientMap])
        </div>

        <!-- PER CATEGORY TABS -->
        @foreach($categories as $categoryName => $catAlerts)
            <div class="tab-pane fade" id="tab-{{ Str::slug($categoryName) }}" role="tabpanel">
                @include('software-setup.email-alerts.matrix-table', ['tableAlerts' => $catAlerts, 'users' => $users, 'recipientMap' => $recipientMap])
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 1. Checkbox toggle event with AJAX
    $(document).on('change', '.recipient-toggle', function() {
        let $checkbox = $(this);
        let mailCode = $checkbox.data('mail-code');
        let userId = $checkbox.data('user-id');
        let isChecked = $checkbox.is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('software-setup.email-alerts.toggle-recipient') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                mail_code: mailCode,
                user_id: userId,
                assigned: isChecked
            },
            success: function(resp) {
                if (resp.success) {
                    showToast('Saved', 'Recipient updated successfully', 'success');
                    $(`.badge-count-${mailCode}`).text(resp.new_count);
                    // Sync any duplicate checkboxes across tabs
                    $(`.recipient-toggle[data-mail-code="${mailCode}"][data-user-id="${userId}"]`).prop('checked', isChecked == 1);
                }
            },
            error: function(xhr) {
                $checkbox.prop('checked', !isChecked);
                showToast('Error', 'Failed to update recipient', 'danger');
            }
        });
    });

    // 2. Master On/Off Switch for an alert
    $(document).on('change', '.master-alert-toggle', function() {
        let $switch = $(this);
        let mailCode = $switch.data('mail-code');
        let isActive = $switch.is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('software-setup.email-alerts.toggle-status') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                mail_code: mailCode,
                is_active: isActive
            },
            success: function(resp) {
                if (resp.success) {
                    let statusText = isActive ? 'enabled' : 'paused';
                    showToast('Report Status', `Email alert is now ${statusText}`, 'info');
                    $(`.master-alert-toggle[data-mail-code="${mailCode}"]`).prop('checked', isActive == 1);
                }
            },
            error: function(xhr) {
                $switch.prop('checked', !isActive);
                showToast('Error', 'Failed to toggle alert status', 'danger');
            }
        });
    });

    // 3. Bulk actions dropdown (All Admins, All Active, Clear All)
    $(document).on('click', '.btn-bulk-assign', function(e) {
        e.preventDefault();
        let mailCode = $(this).data('mail-code');
        let action = $(this).data('action');

        let confirmMsg = action === 'clear_all' 
            ? 'Are you sure you want to clear all recipients for this alert?' 
            : 'Apply bulk assignment for this alert?';

        if (!confirm(confirmMsg)) return;

        $.ajax({
            url: "{{ route('software-setup.email-alerts.bulk-assign') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                mail_code: mailCode,
                action: action
            },
            success: function(resp) {
                if (resp.success) {
                    showToast('Bulk Update', resp.message, 'success');
                    $(`.badge-count-${mailCode}`).text(resp.new_count);
                    // Reload table smoothly or update checks
                    location.reload();
                }
            },
            error: function(xhr) {
                showToast('Error', 'Bulk assignment failed', 'danger');
            }
        });
    });

    // Simple toast helper
    function showToast(title, message, type) {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: type === 'danger' ? 'error' : (type === 'info' ? 'info' : 'success'),
                title: message
            });
        }
    }
});
</script>
@endpush
