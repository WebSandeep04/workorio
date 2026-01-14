<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IndiaMartLeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $leadData;

    /**
     * Create a new message instance.
     */
    public function __construct($leadData)
    {
        $this->leadData = $leadData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "New IndiaMART Lead: " . ($this->leadData['sender_company'] ?? $this->leadData['sender_name'] ?? 'Unknown');
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.indiamart-lead-notification',
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

