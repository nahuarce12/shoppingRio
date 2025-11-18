<?php

namespace App\Mail;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying store owners when their promotion is approved.
 */
class PromotionApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Promotion $promotion
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Promotion Approved - ' . $this->promotion->store->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.promotion-approved',
            with: [
                'storeName' => $this->promotion->store->name,
                'promotionText' => $this->promotion->description,
                'promotionCode' => $this->promotion->code,
                'startDate' => $this->promotion->start_date->format('d/m/Y'),
                'endDate' => $this->promotion->end_date->format('d/m/Y'),
                'category' => $this->promotion->minimum_category,
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
