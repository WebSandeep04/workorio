<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class LeaveStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $status;
    public $user;

    /**
     * Create a new message instance.
     * We pass IDs to avoid multi-tenant serialization issues.
     */
    public function __construct(array $data)
    {
        $this->leaveRequest = LeaveRequest::with('leaveType')->find($data['leave_request_id']);
        $this->user = User::find($data['user_id']);
        $this->status = $data['status'];
    }

    public function envelope(): Envelope
    {
        $subject = 'Leave Request ' . ucfirst($this->status);
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_status',
        );
    }
}
