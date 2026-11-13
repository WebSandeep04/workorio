<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PettyCashSummaryReportMail extends Mailable
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
        $alertPrefix = (isset($this->payload['alert_prefix']) && $this->payload['alert_prefix'])
            ? '[Workorio Alert : ' . $this->payload['alert_prefix'] . '] '
            : '';
        $tenantName = $this->payload['tenant_name'] ?? 'Petty Cash';
        $dateDisplay = $this->payload['date_display'] ?? now()->format('d M Y');

        return new Envelope(
            subject: "{$alertPrefix}Petty Cash Summary & 10-Day Report - {$tenantName} ({$dateDisplay})"
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.petty_cash_summary_report',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}

