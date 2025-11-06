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
        'fecha_uso',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_uso' => 'date',
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
        return $query->where('estado', 'enviada');
    }

    /**
     * Scope to filter accepted usage requests.
     */
    public function scopeAccepted($query)
    {
        return $query->where('estado', 'aceptada');
    }

    /**
     * Scope to filter rejected usage requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('estado', 'rechazada');
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
        return $query->whereBetween('fecha_uso', [$startDate, $endDate]);
    }

    /**
     * Scope to filter usage requests from last N months (for category upgrade calculation).
     */
    public function scopeLastMonths($query, int $months = 6)
    {
        $startDate = now()->subMonths($months);
        return $query->where('fecha_uso', '>=', $startDate);
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Check if usage request is pending.
     */
    public function isPending(): bool
    {
        return $this->estado === 'enviada';
    }

    /**
     * Check if usage request was accepted.
     */
    public function isAccepted(): bool
    {
        return $this->estado === 'aceptada';
    }

    /**
     * Check if usage request was rejected.
     */
    public function isRejected(): bool
    {
        return $this->estado === 'rechazada';
    }

    /**
     * Accept this usage request.
     */
    public function accept(): bool
    {
        $this->estado = 'aceptada';
        return $this->save();
    }

    /**
     * Reject this usage request.
     */
    public function reject(): bool
    {
        $this->estado = 'rechazada';
        return $this->save();
    }
}
