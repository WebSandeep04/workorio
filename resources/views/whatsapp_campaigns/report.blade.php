@extends('layouts.app')

@section('title', 'Campaign Report: ' . $campaign->name)
@section('page_title', 'Campaign Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/whatsapp.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0 fw-bold">{{ $campaign->name }} <span class="badge bg-secondary ms-2" style="font-size: 0.5em;">{{ $campaign->status }}</span></h4>
        <a href="{{ route('whatsapp-campaigns.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Campaigns
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-violet">
                <i class="bi bi-people fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Total Members</div>
                <div class="summary-card-value text-dark">{{ $totalMembers }}</div>
            </div>
        </div>
        <div class="summary-card card-5">
            <div class="summary-card-icon icon-rose">
                <i class="bi bi-check-circle fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Successfully Sent</div>
                <div class="summary-card-value text-success">{{ $sentCount }}</div>
            </div>
        </div>
        <div class="summary-card card-1">
            <div class="summary-card-icon icon-sunrise">
                <i class="bi bi-x-circle fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Failed</div>
                <div class="summary-card-value text-danger">{{ $failedCount }}</div>
            </div>
        </div>
        <div class="summary-card card-4">
            <div class="summary-card-icon icon-violet">
                <i class="bi bi-hourglass fs-5 text-white"></i>
            </div>
            <div class="summary-card-content">
                <div class="summary-card-label">Pending</div>
                <div class="summary-card-value text-warning">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="modern-card data-table-card mt-3">
        <div class="modern-card-body">
            <div class="table-responsive">
                <table class="table custom-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Error / Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaign->members as $member)
                            @php
                                $badgeClass = 'bg-secondary';
                                $statusLower = strtolower($member->status ?? '');
                                if (in_array($statusLower, ['delivered', 'read', 'completed'])) $badgeClass = 'bg-success';
                                elseif ($statusLower === 'sent') $badgeClass = 'bg-info';
                                elseif ($statusLower === 'failed') $badgeClass = 'bg-danger';
                                elseif ($statusLower === 'pending') $badgeClass = 'bg-warning text-dark';
                            @endphp
                            <tr>
                                <td>{{ $member->name ?? '-' }}</td>
                                <td>{{ $member->phone_number ?? '-' }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $member->status ?? 'Unknown' }}</span></td>
                                <td class="text-danger small">{{ $member->error_message ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No members found in this campaign.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
