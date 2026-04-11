<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CallingAnalyticReport extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $statusData;
    public $today;
    public $tenantName;
    public $userStatusCounts;
    public $userLeads;
    public $allCallingTypes;
    public $alert_prefix;

    /**
     * Create a new message instance.
     */
    public function __construct($userData, $statusData, $today, $tenantName, $userStatusCounts = [], $userLeads = [], $allCallingTypes = [], $alert_prefix = null)
    {
        $this->userData = $userData;
        $this->statusData = $statusData;
        $this->today = $today;
        $this->tenantName = $tenantName;
        $this->userStatusCounts = $userStatusCounts;
        $this->userLeads = $userLeads;
        $this->allCallingTypes = $allCallingTypes;
        $this->alert_prefix = $alert_prefix;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->alert_prefix ? "[Workorio Alert : {$this->alert_prefix}] " : "") . "Daily Calling Analytic Report - {$this->tenantName} ({$this->today})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.calling_analytic_report',
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
