<?php

namespace App\Mail;

use App\Models\PromotionUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying store owners when a client requests promotion usage.
 */
class PromotionUsageRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public PromotionUsage $usage
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Promotion Request - ' . $this->usage->promotion->store->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotion-usage-request',
            with: [
                'storeName' => $this->usage->promotion->store->name,
                'promotionText' => $this->usage->promotion->description,
                'promotionCode' => $this->usage->promotion->code,
                'clientName' => $this->usage->client->name,
                'clientEmail' => $this->usage->client->email,
                'clientCategory' => $this->usage->client->client_category,
                'requestDate' => $this->usage->usage_date->format('d/m/Y'),
                'acceptUrl' => url("/store/usage-requests/{$this->usage->id}/accept"),
                'rejectUrl' => url("/store/usage-requests/{$this->usage->id}/reject"),
                'dashboardUrl' => url('/store/dashboard'),
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
