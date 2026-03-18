<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SelfTaskMailReport extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (isset($this->payload['alert_prefix']) && $this->payload['alert_prefix'] ? 'Workorio Alert ' . $this->payload['alert_prefix'] . ' : ' : '') . 'Your Pending Tasks - ' . $this->payload['dateDisplay'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.self_task_report',
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
