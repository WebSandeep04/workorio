<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Mail\LeaveStatusMail;
use App\Services\TenantDatabaseService;

class SendLeaveStatusNotification implements ShouldQueue
{
    use Queueable;

    protected int $leaveRequestId;
    protected int $userId;
    protected string $status;
    protected ?string $reason;
    protected string $connectionName;

    /**
     * Create a new job instance.
     */
    public function __construct(int $leaveRequestId, int $userId, string $status, ?string $reason, string $connectionName)
    {
        $this->leaveRequestId = $leaveRequestId;
        $this->userId = $userId;
        $this->status = $status;
        $this->reason = $reason;
        $this->connectionName = $connectionName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Re-establish tenant database connection
            if (str_starts_with($this->connectionName, 'tenant_')) {
                $tenantId = (int) str_replace('tenant_', '', $this->connectionName);
                TenantDatabaseService::setDefaultConnection($tenantId);
            } else {
                DB::setDefaultConnection($this->connectionName);
            }

            $leaveReq = LeaveRequest::with(['user.employee', 'leaveType'])->find($this->leaveRequestId);
            if (!$leaveReq || !$leaveReq->user || !$leaveReq->user->email) {
                return;
            }

            // Only notify if employee is active
            if ($leaveReq->user->employee && $leaveReq->user->employee->status !== 'active') {
                return;
            }

            Mail::to($leaveReq->user->email)->send(new LeaveStatusMail([
                'leave_request_id' => $leaveReq->id,
                'user_id' => $leaveReq->user->id,
                'status' => $this->status,
                'reason' => $this->reason
            ]));

            Log::info("Queued leave status email sent successfully for leave ID #{$this->leaveRequestId} ({$this->status}) to {$leaveReq->user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to execute queued SendLeaveStatusNotification for leave #{$this->leaveRequestId}: " . $e->getMessage());
        }
    }
}
