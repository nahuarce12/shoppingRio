<?php

namespace App\Mail;

use App\Models\PromotionUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying clients when their promotion usage request is accepted.
 */
class PromotionUsageAcceptedMail extends Mailable
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
            subject: 'Promotion Request Accepted - ' . $this->usage->promotion->store->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotion-usage-accepted',
            with: [
                'clientName' => $this->usage->client->name,
                'storeName' => $this->usage->promotion->store->name,
                'storeLocation' => $this->usage->promotion->store->location,
                'promotionText' => $this->usage->promotion->description,
                'promotionCode' => $this->usage->promotion->code,
                'usageDate' => $this->usage->usage_date->format('d/m/Y'),
                'validUntil' => $this->usage->promotion->end_date->format('d/m/Y'),
                'storeLocatorUrl' => url('/stores/' . $this->usage->promotion->store->id),
                'promotionsUrl' => url('/promotions'),
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
