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
        'code',
        'name',
        'location',
        'category',
        'description',
        'logo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'code' => 'integer',
    ];

    // ==================== Relationships ====================

    /**
     * Get all owners (users) of this store.
     * A store can have multiple owners.
     */
    public function owners(): HasMany
    {
        return $this->hasMany(User::class, 'store_id')
            ->where('user_type', 'dueño de local');
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
     * Scope to filter stores by business category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to search stores by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    // ==================== Model Events ====================

    /**
     * Boot the model.
     * Generate sequential code on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            if (empty($store->code)) {
                // Get the highest code and add 1
                $maxCode = static::withTrashed()->max('code') ?? 0;
                $store->code = $maxCode + 1;
            }
        });
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Get the logo URL (converts storage path to public URL).
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        // If it's already a URL, return as-is
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        // Generate absolute URL to storage file
        return url('storage/' . $this->logo);
    }

    /**
     * Get the count of active promotions for this store.
     */
    public function getActivePromotionsCountAttribute(): int
    {
        return $this->promotions()
            ->where('status', 'aprobada')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();
    }
}
