<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SignalPendingTasksReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;

    /**
     * Create a new message instance.
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
        $prefix = !empty($this->payload['alert_prefix']) ? "[{$this->payload['alert_prefix']}] " : "";
        $tenantName = $this->payload['tenant_name'] ?? 'Workorio';
        $dateDisplay = $this->payload['dateDisplay'] ?? date('d M Y');

        return new Envelope(
            subject: $prefix . "Pending Signal Tasks Report - {$tenantName} ({$dateDisplay})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.signal_pending_tasks_report',
            with: [
                'payload' => $this->payload,
            ]
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

