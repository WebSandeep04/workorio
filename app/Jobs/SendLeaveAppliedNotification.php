<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Mail\LeaveApplicationMail;
use App\Services\TenantDatabaseService;

class SendLeaveAppliedNotification implements ShouldQueue
{
    use Queueable;

    protected int $leaveRequestId;
    protected int $applicantId;
    protected array $recipientUserIds;
    protected string $connectionName;

    /**
     * Create a new job instance.
     */
    public function __construct(int $leaveRequestId, int $applicantId, array $recipientUserIds, string $connectionName)
    {
        $this->leaveRequestId = $leaveRequestId;
        $this->applicantId = $applicantId;
        $this->recipientUserIds = $recipientUserIds;
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

            $leaveReq = LeaveRequest::find($this->leaveRequestId);
            $applicant = User::find($this->applicantId);

            if (!$leaveReq || !$applicant || empty($this->recipientUserIds)) {
                return;
            }

            $recipients = User::whereIn('id', $this->recipientUserIds)
                ->whereNotNull('email')
                ->get();

            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(new LeaveApplicationMail([
                        'leave_request_id' => $leaveReq->id,
                        'applicant_id' => $applicant->id,
                        'recipient_name' => $recipient->name
                    ]));
                } catch (\Throwable $mailEx) {
                    Log::error("Failed to send queued leave application email to {$recipient->email}: " . $mailEx->getMessage());
                }
            }

            Log::info("Queued leave application emails processed for leave ID #{$this->leaveRequestId} to " . count($recipients) . " recipients.");
        } catch (\Throwable $e) {
            Log::error("Failed to execute queued SendLeaveAppliedNotification for leave #{$this->leaveRequestId}: " . $e->getMessage());
        }
    }
}
