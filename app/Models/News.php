<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'code',
        'description',
        'imagen',
        'start_date',
        'end_date',
        'target_category',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'code' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ==================== Relationships ====================

    /**
     * Get the admin user who created this news.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== Query Scopes ====================

    /**
     * Scope to filter only active news (not expired).
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * Scope to filter expired news.
     */
    public function scopeExpired($query)
    {
        $today = Carbon::today();
        return $query->whereDate('end_date', '<', $today);
    }

    /**
     * Scope to filter news accessible by a given client category.
     * Based on hierarchy: Premium sees all, Medium sees Medium+Inicial, Inicial only Inicial.
     */
    public function scopeForCategory($query, string $clientCategory)
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $clientLevel = $hierarchy[$clientCategory] ?? 0;

        return $query->where(function ($q) use ($hierarchy, $clientLevel) {
            foreach ($hierarchy as $category => $level) {
                if ($level <= $clientLevel) {
                    $q->orWhere('target_category', $category);
                }
            }
        });
    }

    // ==================== Model Events ====================

    /**
     * Boot the model.
     * Generate sequential codigo on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            if (empty($news->code)) {
                // Get the highest codigo and add 1
                $maxCodigo = static::max('code') ?? 0;
                $news->code = $maxCodigo + 1;
            }
        });
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Check if news is currently active (not expired).
     */
    public function isActive(): bool
    {
        $today = Carbon::today();
        return $this->start_date <= $today && $this->end_date >= $today;
    }

    /**
     * Check if news is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < Carbon::today();
    }

    /**
     * Check if a client can view this news based on their category.
     */
    public function isAccessibleByCategory(string $clientCategory): bool
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $clientLevel = $hierarchy[$clientCategory] ?? 0;
        $requiredLevel = $hierarchy[$this->target_category] ?? 0;

        return $clientLevel >= $requiredLevel;
    }

    /**
     * Get days remaining until expiration.
     */
    public function getDaysUntilExpiration(): int
    {
        return Carbon::today()->diffInDays($this->end_date, false);
    }
}
