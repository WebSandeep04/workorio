<?php

namespace App\Mail;

use App\Models\SalesRecord;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $creator;
    public $assignedTo;
    public $remarkText;

    public function __construct(SalesRecord $lead, ?User $creator, ?User $assignedTo, ?string $remarkText)
    {
        $this->lead = $lead;
        $this->creator = $creator;
        $this->assignedTo = $assignedTo;
        $this->remarkText = $remarkText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Lead Added: ' . ($this->lead->leads_name ?: 'Unnamed Lead'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_lead_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
