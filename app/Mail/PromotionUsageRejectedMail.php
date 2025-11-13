<?php

namespace App\Mail;

use App\Models\PromotionUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying clients when their promotion usage request is rejected.
 */
class PromotionUsageRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public PromotionUsage $usage,
        public ?string $reason = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Promotion Request Update - ' . $this->usage->promotion->store->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get alternative promotions from the same store
        $alternativePromotions = $this->usage->promotion->store->promotions()
            ->approved()
            ->active()
            ->where('id', '!=', $this->usage->promotion_id)
            ->limit(3)
            ->get();

        return new Content(
            view: 'emails.promotion-usage-rejected',
            with: [
                'clientName' => $this->usage->client->name,
                'storeName' => $this->usage->promotion->store->name,
                'promotionText' => $this->usage->promotion->description,
                'promotionCode' => $this->usage->promotion->code,
                'reason' => $this->reason ?? 'Unfortunately, the store could not approve your request at this time.',
                'alternativePromotions' => $alternativePromotions,
                'promotionsUrl' => url('/promotions'),
                'storePromotionsUrl' => url('/stores/' . $this->usage->promotion->store->id . '/promotions'),
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
