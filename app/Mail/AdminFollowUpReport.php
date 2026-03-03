<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminFollowUpReport extends Mailable
{
    use Queueable, SerializesModels;

    public $records;
    public $newLeads;
    public $today;
    public $summary;

    /**
     * Create a new message instance.
     */
    public function __construct($records, $newLeads, $today, $summary)
    {
        $this->records = $records;
        $this->newLeads = $newLeads;
        $this->today = $today;
        $this->summary = $summary;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Follow-up Report ({$this->today})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_followup_report',
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
