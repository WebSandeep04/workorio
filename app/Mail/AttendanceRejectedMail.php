<?php

namespace App\Mail;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AttendanceRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attendance;
    public $user;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->attendance = Attendance::find($data['attendance_id']);
        $this->user = User::find($data['user_id']);
        $this->reason = $data['reason'] ?? 'No reason provided';
    }

    public function envelope(): Envelope
    {
        $dateStr = $this->attendance->date instanceof Carbon ? $this->attendance->date->format('d M Y') : date('d M Y', strtotime($this->attendance->date));
        return new Envelope(
            subject: 'Attendance Rejected for ' . $dateStr,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance_rejected',
        );
    }
}
