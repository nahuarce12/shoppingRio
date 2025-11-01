<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario',
        'categoria_cliente',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    // ==================== Relationships ====================

    /**
     * Get all stores owned by this user (for store owners).
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    /**
     * Get all promotion usages for this user (for clients).
     */
    public function promotionUsages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class, 'client_id');
    }

    /**
     * Get all news created by this user (for admins).
     */
    public function createdNews(): HasMany
    {
        return $this->hasMany(News::class, 'created_by');
    }

    /**
     * Get all users approved by this admin.
     */
    public function approvedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /**
     * Get the admin who approved this user (for store owners).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==================== Query Scopes ====================

    /**
     * Scope to filter only client users.
     */
    public function scopeClients($query)
    {
        return $query->where('tipo_usuario', 'cliente');
    }

    /**
     * Scope to filter only store owner users.
     */
    public function scopeStoreOwners($query)
    {
        return $query->where('tipo_usuario', 'dueño de local');
    }

    /**
     * Scope to filter only admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('tipo_usuario', 'administrador');
    }

    /**
     * Scope to filter approved store owners.
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope to filter pending store owners (awaiting approval).
     */
    public function scopePending($query)
    {
        return $query->where('tipo_usuario', 'dueño de local')
            ->whereNull('approved_at');
    }

    /**
     * Scope to filter users by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('categoria_cliente', $category);
    }

    // ==================== Accessors & Helpers ====================

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->tipo_usuario === 'administrador';
    }

    /**
     * Check if user is a store owner.
     */
    public function isStoreOwner(): bool
    {
        return $this->tipo_usuario === 'dueño de local';
    }

    /**
     * Check if user is a client.
     */
    public function isClient(): bool
    {
        return $this->tipo_usuario === 'cliente';
    }

    /**
     * Check if store owner is approved.
     */
    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * Check if client can access content for a given category.
     * Based on hierarchy: Premium can access all, Medium can access Medium+Inicial, Inicial only Inicial.
     *
     * @param string $requiredCategory The minimum category required ('Inicial', 'Medium', 'Premium')
     * @return bool
     */
    public function canAccessCategory(string $requiredCategory): bool
    {
        if (!$this->isClient()) {
            return false;
        }

        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        $userLevel = $hierarchy[$this->categoria_cliente] ?? 0;
        $requiredLevel = $hierarchy[$requiredCategory] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Get the user's category level as integer (1=Inicial, 2=Medium, 3=Premium).
     */
    public function getCategoryLevel(): int
    {
        $hierarchy = ['Inicial' => 1, 'Medium' => 2, 'Premium' => 3];
        return $hierarchy[$this->categoria_cliente] ?? 0;
    }
}
