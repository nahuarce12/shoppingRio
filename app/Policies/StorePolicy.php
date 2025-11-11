<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StorePolicy
{
    /**
     * Determine whether the user can view any models.
     * Admins can view all, store owners can view their own.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || ($user->isStoreOwner() && $user->isApproved());
    }

    /**
     * Determine whether the user can view the model.
     * Admins can view any store, store owners can only view their own.
     */
    public function view(User $user, Store $store): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStoreOwner() && $user->isApproved()) {
            return $user->store_id === $store->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * Only admins can create stores.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     * Only admins can update stores.
     */
    public function update(User $user, Store $store): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     * Only admins can delete stores.
     */
    public function delete(User $user, Store $store): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     * Only admins can restore soft-deleted stores.
     */
    public function restore(User $user, Store $store): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Only admins can force delete stores.
     */
    public function forceDelete(User $user, Store $store): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage promotions for this store.
     * Store owners can manage their own store's promotions if approved.
     */
    public function managePromotions(User $user, Store $store): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStoreOwner() && $user->isApproved()) {
            return $user->store_id === $store->id;
        }

        return false;
    }
}
