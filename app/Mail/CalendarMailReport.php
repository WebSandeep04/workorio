<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CalendarMailReport extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;
    public $endDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $payload, string $endDate)
    {
        $this->payload = $payload;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (isset($this->payload['alert_prefix']) && $this->payload['alert_prefix'] ? 'Workorio Alert ' . $this->payload['alert_prefix'] . ' : ' : '') . '📋 Pending Events + Monthly Summary - ' . $this->endDate,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.calendar_events',
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
