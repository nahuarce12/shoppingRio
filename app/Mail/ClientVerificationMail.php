<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $client,
        public string $verificationUrl
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a ShoppingRio! - Verifica tu cuenta',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client-verification',
            with: [
                'clientName' => $this->client->name,
                'clientEmail' => $this->client->nombreUsuario,
                'verificationUrl' => $this->verificationUrl,
                'expirationMinutes' => config('auth.verification.expire', 60),
                'benefits' => [
                    'Acceso exclusivo a promociones y descuentos',
                    'Sistema de categorías con beneficios progresivos',
                    'Notificaciones personalizadas de ofertas',
                    'Historial de promociones utilizadas',
                ],
                'initialCategory' => 'Inicial',
                'categoryBenefits' => config('shopping.client_categories.Inicial.benefits', []),
                'supportEmail' => config('mail.support_email', 'soporte@shoppingrio.com'),
            ],
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
