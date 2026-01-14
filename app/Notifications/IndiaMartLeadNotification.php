<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndiaMartLeadNotification extends Notification
{
    use Queueable;

    public $leadData;

    /**
     * Create a new notification instance.
     */
    public function __construct($leadData)
    {
        $this->leadData = $leadData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New IndiaMART Lead')
                    ->line('A new lead has been received from IndiaMART.')
                    ->action('View Lead', url('/indiamart/leads'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'indiamart_lead',
            'title' => 'New IndiaMART Lead',
            'message' => 'New lead from ' . ($this->leadData['sender_company'] ?? $this->leadData['sender_name'] ?? 'Unknown'),
            'lead_id' => $this->leadData['unique_query_id'] ?? null,
            'sender_name' => $this->leadData['sender_name'] ?? null,
            'sender_company' => $this->leadData['sender_company'] ?? null,
            'sender_mobile' => $this->leadData['sender_mobile'] ?? null,
            'sender_city' => $this->leadData['sender_city'] ?? null,
            'sender_state' => $this->leadData['sender_state'] ?? null,
            'query_product_name' => $this->leadData['query_product_name'] ?? null,
            'query_type' => $this->leadData['query_type'] ?? null,
            'created_at' => now(),
        ];
    }
}
