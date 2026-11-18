<div class="table-responsive">
    <table class="table logs-table">
        <thead>
            <tr>
                <th style="width: 130px;">Status</th>
                <th style="width: 170px;">Sent At</th>
                <th>Mail Alert Type</th>
                <th>Recipient</th>
                <th>Subject</th>
                <th style="width: 100px;" class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>
                        @if($log->status === 'sent')
                            <span class="badge badge-soft-success px-2 py-1">
                                <i class="bi bi-check2-circle me-1"></i>Sent
                            </span>
                        @else
                            <span class="badge badge-soft-danger px-2 py-1">
                                <i class="bi bi-x-circle me-1"></i>Failed
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $log->sent_at ? $log->sent_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : '-' }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $log->alert ? $log->alert->name : $log->mail_code }}</div>
                        <code class="small text-muted" style="font-size: 0.72rem;">{{ $log->mail_code }}</code>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $log->recipient_name ?: ($log->user ? $log->user->name : 'Recipient') }}</div>
                        <a href="mailto:{{ $log->recipient_email }}" class="text-muted small text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>{{ $log->recipient_email }}
                        </a>
                    </td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 320px;" title="{{ $log->subject }}">
                            {{ $log->subject ?: 'Report Mail' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary py-0 px-2 btn-view-log"
                                data-log="{{ json_encode([
                                    'mail_code' => $log->mail_code,
                                    'alert_name' => $log->alert ? $log->alert->name : $log->mail_code,
                                    'recipient_email' => $log->recipient_email,
                                    'recipient_name' => $log->recipient_name ?: ($log->user ? $log->user->name : 'N/A'),
                                    'subject' => $log->subject,
                                    'status' => $log->status,
                                    'error_message' => $log->error_message,
                                    'sent_at' => $log->sent_at ? $log->sent_at->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') : '-',
                                    'meta' => $log->meta
                                ]) }}">
                            <i class="bi bi-eye"></i> Details
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                        No email logs found matching your filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($logs->hasPages())
    <div class="p-3 border-top pagination-wrapper">
        {{ $logs->links() }}
    </div>
@endif
