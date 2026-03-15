<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MorningAttendanceReport extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;
    public $today;

    /**
     * Create a new message instance.
     *
     * @param array $reportData
     * @param string $today
     */
    public function __construct(array $reportData, string $today)
    {
        $this->reportData = $reportData;
        $this->today = $today;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Today's Attendance Summary - " . date('d M Y', strtotime($this->today)),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.morning_attendance',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
