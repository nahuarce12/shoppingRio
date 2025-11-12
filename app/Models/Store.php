<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'ubicacion',
        'rubro',
        'logo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'codigo' => 'integer',
    ];

    // ==================== Relationships ====================

    /**
     * Get all owners (users) of this store.
     * A store can have multiple owners.
     */
    public function owners(): HasMany
    {
        return $this->hasMany(User::class, 'store_id')
            ->where('tipo_usuario', 'dueño de local');
    }

    /**
     * Get the primary owner (first user) of this store.
     * Convenience method for singular relationships.
     */
    public function owner(): HasMany
    {
        return $this->owners();
    }

    /**
     * Get all promotions for this store.
     */
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class);
    }

    // ==================== Query Scopes ====================

    /**
     * Scope to filter only non-deleted (active) stores.
     * Note: This is redundant with SoftDeletes, but explicit for clarity.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope to filter stores by business category (rubro).
     */
    public function scopeByRubro($query, string $rubro)
    {
        return $query->where('rubro', $rubro);
    }

    /**
     * Scope to search stores by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('nombre', 'like', "%{$search}%");
    }

    // ==================== Model Events ====================

    /**
     * Boot the model.
     * Generate sequential codigo on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->codigo)) {
                // Get the highest codigo and add 1
                $maxCodigo = static::withTrashed()->max('codigo') ?? 0;
                $store->codigo = $maxCodigo + 1;
            }
        });
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Get the count of active promotions for this store.
     */
    public function getActivePromotionsCountAttribute(): int
    {
        return $this->promotions()
            ->where('estado', 'aprobada')
            ->whereDate('fecha_desde', '<=', now())
            ->whereDate('fecha_hasta', '>=', now())
            ->count();
    }
}
