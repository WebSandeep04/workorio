<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FollowUpReport extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $leads;
    public $today;
    public $alert_prefix;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $leads, $today, $alert_prefix = null)
    {
        $this->user = $user;
        $this->leads = $leads;
        $this->today = $today;
        $this->alert_prefix = $alert_prefix;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->alert_prefix ? "[Workorio Alert : {$this->alert_prefix}] " : "") . "📌 Follow-up Report ({$this->today})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.followup_report',
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
