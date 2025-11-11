<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'codigo',
        'texto',
        'fecha_desde',
        'fecha_hasta',
        'dias_semana',
        'categoria_minima',
        'estado',
        'store_id',
        'imagen',
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
        'dias_semana' => 'array', // JSON array of 7 booleans
    ];

    // ==================== Relationships ====================

    /**
     * Get the store that owns this promotion.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class)->withTrashed();
    }

    /**
     * Get all usage records for this promotion.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    // ==================== Query Scopes ====================

    /**
     * Scope to filter only approved promotions.
     */
    public function scopeApproved($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope to filter only pending promotions (awaiting admin approval).
     */
    public function scopePending($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope to filter only denied promotions.
     */
    public function scopeDenied($query)
    {
        return $query->where('estado', 'denegada');
    }

    /**
     * Scope to filter promotions that are currently active (date range valid and approved).
     */
    public function scopeActive($query)
    {
        $today = Carbon::today();
        return $query->where('estado', 'aprobada')
            ->whereDate('fecha_desde', '<=', $today)
            ->whereDate('fecha_hasta', '>=', $today);
    }

    /**
     * Scope to filter promotions valid today (including day of week check).
     */
    public function scopeValidToday($query)
    {
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        
        // Adjust to match our convention: Monday=0 to Sunday=6
        $adjustedDay = ($dayOfWeek + 6) % 7;

        return $query->active()
            ->whereRaw("JSON_EXTRACT(dias_semana, '$[{$adjustedDay}]') = true");
    }

    /**
     * Scope to filter promotions accessible by a given client category.
     * Based on hierarchy: Premium sees all, Medium sees Medium+Inicial, Inicial only Inicial.
     */
    public function scopeForCategory($query, string $clientCategory)
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $clientLevel = $hierarchy[$clientCategory] ?? 0;

        return $query->where(function ($q) use ($hierarchy, $clientLevel) {
            foreach ($hierarchy as $category => $level) {
                if ($level <= $clientLevel) {
                    $q->orWhere('categoria_minima', $category);
                }
            }
        });
    }

    /**
     * Scope to filter promotions by store.
     */
    public function scopeByStore($query, int $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // ==================== Model Events ====================

    /**
     * Boot the model.
     * Generate sequential codigo on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($promotion) {
            if (empty($promotion->codigo)) {
                // Get the highest codigo and add 1
                $maxCodigo = static::withTrashed()->max('codigo') ?? 0;
                $promotion->codigo = $maxCodigo + 1;
            }
        });
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Check if promotion is currently active (within date range and approved).
     */
    public function isActive(): bool
    {
        $today = Carbon::today();
        return $this->estado === 'aprobada' &&
            $this->fecha_desde <= $today &&
            $this->fecha_hasta >= $today;
    }

    /**
     * Check if promotion is valid for a specific day of week.
     * @param int $dayOfWeek 0=Monday to 6=Sunday (our convention)
     */
    public function isValidForDay(int $dayOfWeek): bool
    {
        if (!is_array($this->dias_semana) || count($this->dias_semana) !== 7) {
            return false;
        }
        return $this->dias_semana[$dayOfWeek] === true;
    }

    /**
     * Check if promotion is valid today (date range + day of week).
     */
    public function isValidToday(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeek; // 0 = Sunday, 1 = Monday
        // Adjust to our convention: Monday=0 to Sunday=6
        $adjustedDay = ($dayOfWeek + 6) % 7;

        return $this->isValidForDay($adjustedDay);
    }

    /**
     * Check if a client can access this promotion based on their category.
     */
    public function isAccessibleByCategory(string $clientCategory): bool
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $clientLevel = $hierarchy[$clientCategory] ?? 0;
        $requiredLevel = $hierarchy[$this->categoria_minima] ?? 0;

        return $clientLevel >= $requiredLevel;
    }

    /**
     * Check if a client has already used this promotion.
     */
    public function hasBeenUsedBy(int $clientId): bool
    {
        return $this->usages()
            ->where('client_id', $clientId)
            ->exists();
    }

    /**
     * Check full eligibility for a client (active, valid today, category match, not used).
     */
    public function isEligibleForClient(User $client): bool
    {
        if (!$client->isClient()) {
            return false;
        }

        return $this->isValidToday() &&
            $this->isAccessibleByCategory($client->categoria_cliente) &&
            !$this->hasBeenUsedBy($client->id);
    }

    /**
     * Get usage count (accepted usages only).
     */
    public function getAcceptedUsageCount(): int
    {
        return $this->usages()
            ->where('estado', 'aceptada')
            ->count();
    }
}
