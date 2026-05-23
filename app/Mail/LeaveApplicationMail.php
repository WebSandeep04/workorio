<?php

namespace App\Mail;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leaveRequest;
    public $applicant;
    public $recipientName;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->leaveRequest = LeaveRequest::with('leaveType')->find($data['leave_request_id']);
        $this->applicant = User::find($data['applicant_id']);
        $this->recipientName = $data['recipient_name'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Leave Request Application',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.leave_application',
        );
    }
}
