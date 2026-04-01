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
    public $completedToday;
    public $today;
    public $summary;
    public $alert_prefix;

    /**
     * Create a new message instance.
     */
    public function __construct($records, $newLeads, $completedToday, $today, $summary, $alert_prefix = null)
    {
        $this->records = $records;
        $this->newLeads = $newLeads;
        $this->completedToday = $completedToday;
        $this->today = $today;
        $this->summary = $summary;
        $this->alert_prefix = $alert_prefix;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->alert_prefix ? "[Workorio Alert : {$this->alert_prefix}] " : "") . "Follow-up Report ({$this->today})",
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
