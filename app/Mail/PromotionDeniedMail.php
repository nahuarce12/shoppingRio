<?php

namespace App\Mail;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying store owners when their promotion is denied.
 */
class PromotionDeniedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Promotion $promotion,
        public ?string $reason = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Promotion Denied - ' . $this->promotion->store->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotion-denied',
            with: [
                'storeName' => $this->promotion->store->name,
                'promotionText' => $this->promotion->description,
                'promotionCode' => $this->promotion->code,
                'reason' => $this->reason ?? 'The promotion did not meet our policy requirements.',
                'guidelinesUrl' => url('/promotion-guidelines'),
                'contactEmail' => config('shopping.admin_contact.email'),
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
