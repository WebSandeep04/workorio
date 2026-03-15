<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NightAttendanceReport extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;
    public $today;
    public $monthYear;

    /**
     * Create a new message instance.
     *
     * @param array $reportData
     * @param string $today
     * @param string $monthYear
     */
    public function __construct(array $reportData, string $today, string $monthYear)
    {
        $this->reportData = $reportData;
        $this->today = $today;
        $this->monthYear = $monthYear;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Monthly Attendance Summary - " . $this->monthYear,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.night_attendance',
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
