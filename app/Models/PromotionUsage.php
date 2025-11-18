<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionUsage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotion_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'client_id',
        'promotion_id',
        'usage_date',
        'status',
        'codigo_qr',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_date' => 'date',
    ];

    // ==================== Relationships ====================

    /**
     * Get the client who requested/used this promotion.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the promotion that was used.
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class)->withTrashed();
    }

    // ==================== Query Scopes ====================

    /**
     * Scope to filter pending usage requests (enviada).
     */
    public function scopePending($query)
    {
        return $query->where('status', 'enviada');
    }

    /**
     * Scope to filter accepted usage requests.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'aceptada');
    }

    /**
     * Scope to filter rejected usage requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rechazada');
    }

    /**
     * Scope to filter by client.
     */
    public function scopeByClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to filter by promotion.
     */
    public function scopeByPromotion($query, int $promotionId)
    {
        return $query->where('promotion_id', $promotionId);
    }

    /**
     * Scope to filter usage requests in a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('usage_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter usage requests from last N months (for category upgrade calculation).
     */
    public function scopeLastMonths($query, int $months = 6)
    {
        $startDate = now()->subMonths($months);
        return $query->where('usage_date', '>=', $startDate);
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Check if usage request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'enviada';
    }

    /**
     * Check if usage request was accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'aceptada';
    }

    /**
     * Check if usage request was rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rechazada';
    }

    /**
     * Accept this usage request.
     */
    public function accept(): bool
    {
        $this->status = 'aceptada';
        return $this->save();
    }

    /**
     * Reject this usage request.
     */
    public function reject(): bool
    {
        $this->status = 'rechazada';
        return $this->save();
    }

    /**
     * Generate a unique QR code for this usage request.
     */
    public static function generateUniqueQrCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
        } while (self::where('codigo_qr', $code)->exists());

        return $code;
    }

    /**
     * Get the QR code as SVG image.
     */
    public function getQrCodeSvg(): string
    {
        if (!$this->code_qr) {
            return '';
        }

        $qrcode = new \chillerlan\QRCode\QRCode();
        return $qrcode->render($this->code_qr);
    }

    /**
     * Get the QR code as base64 PNG image.
     */
    public function getQrCodeBase64(): string
    {
        if (!$this->code_qr) {
            return '';
        }

        $options = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\Output\QROutputInterface::GDIMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::H,
            'scale' => 5,
            'imageBase64' => true,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        return $qrcode->render($this->code_qr);
    }
}
