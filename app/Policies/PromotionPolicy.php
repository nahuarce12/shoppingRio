<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    /**
     * Determine whether the user can view any models.
     * Everyone can view promotions (including unregistered users handled at controller level).
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public access
    }

    /**
     * Determine whether the user can view the model.
     * Everyone can view individual promotions.
     */
    public function view(?User $user, Promotion $promotion): bool
    {
        return true; // Public access
    }

    /**
     * Determine whether the user can create models.
     * Only approved store owners can create promotions for their stores.
     */
    public function create(User $user, ?Store $store = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStoreOwner() && $user->isApproved()) {
            // If store is provided, check ownership
            if ($store) {
                return $user->store_id === $store->id;
            }
            // Otherwise, user can create for their store
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     * No updates allowed per business rules (must delete and recreate).
     */
    public function update(User $user, Promotion $promotion): bool
    {
        return false; // Business rule: no editing allowed
    }

    /**
     * Determine whether the user can delete the model.
     * Store owners can delete their own promotions, admins can delete any.
     */
    public function delete(User $user, Promotion $promotion): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStoreOwner() && $user->isApproved()) {
            return $promotion->store_id === $user->store_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Promotion $promotion): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Promotion $promotion): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve/deny promotions.
     * Only admins can approve or deny promotions.
     */
    public function approve(User $user, Promotion $promotion): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether a client can request to use this promotion.
     * Clients can request if promotion is eligible and not already used.
     */
    public function request(User $user, Promotion $promotion): bool
    {
        if (!$user->isClient()) {
            return false;
        }

        if (!$user->hasVerifiedEmail()) {
            return false;
        }

        return $promotion->isEligibleForClient($user);
    }

    /**
     * Determine whether user can accept/reject promotion usage requests.
     * Store owners can manage requests for their stores, admins can manage any.
     */
    public function manageRequests(User $user, Promotion $promotion): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStoreOwner() && $user->isApproved()) {
            return $promotion->store_id === $user->store_id;
        }

        return false;
    }
}
