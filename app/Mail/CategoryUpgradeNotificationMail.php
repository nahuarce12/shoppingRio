<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mailable for notifying clients when they are upgraded to a higher category.
 */
class CategoryUpgradeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $client,
        public string $oldCategory,
        public string $newCategory
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! Category Upgrade to ' . $this->newCategory,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get category benefits from config
        $categoryConfig = config('shopping.client_categories.' . $this->newCategory, []);
        $benefits = $categoryConfig['benefits'] ?? [];

        // Get available promotions for new category
        $promotionCount = \App\Models\Promotion::query()
            ->approved()
            ->active()
            ->forCategory($this->newCategory)
            ->count();

        return new Content(
            view: 'emails.category-upgrade',
            with: [
                'clientName' => $this->client->name,
                'oldCategory' => $this->oldCategory,
                'newCategory' => $this->newCategory,
                'benefits' => $benefits,
                'promotionCount' => $promotionCount,
                'categoryColor' => $categoryConfig['color'] ?? '#ffc107',
                'promotionsUrl' => url('/promotions'),
                'dashboardUrl' => url('/client/dashboard'),
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
