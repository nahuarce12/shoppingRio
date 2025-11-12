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
        'codigo',
        'texto',
        'imagen',
        'fecha_desde',
        'fecha_hasta',
        'categoria_destino',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'codigo' => 'integer',
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
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
        return $query->whereDate('fecha_desde', '<=', $today)
            ->whereDate('fecha_hasta', '>=', $today);
    }

    /**
     * Scope to filter expired news.
     */
    public function scopeExpired($query)
    {
        $today = Carbon::today();
        return $query->whereDate('fecha_hasta', '<', $today);
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
                    $q->orWhere('categoria_destino', $category);
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
            if (empty($news->codigo)) {
                // Get the highest codigo and add 1
                $maxCodigo = static::max('codigo') ?? 0;
                $news->codigo = $maxCodigo + 1;
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
        return $this->fecha_desde <= $today && $this->fecha_hasta >= $today;
    }

    /**
     * Check if news is expired.
     */
    public function isExpired(): bool
    {
        return $this->fecha_hasta < Carbon::today();
    }

    /**
     * Check if a client can view this news based on their category.
     */
    public function isAccessibleByCategory(string $clientCategory): bool
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $clientLevel = $hierarchy[$clientCategory] ?? 0;
        $requiredLevel = $hierarchy[$this->categoria_destino] ?? 0;

        return $clientLevel >= $requiredLevel;
    }

    /**
     * Get days remaining until expiration.
     */
    public function getDaysUntilExpiration(): int
    {
        return Carbon::today()->diffInDays($this->fecha_hasta, false);
    }
}
