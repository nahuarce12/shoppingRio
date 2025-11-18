<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view news (filtered by category).
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public access, filtered by category
    }

    /**
     * Determine whether the user can view the model.
     * Category-based access control.
     */
    public function view(?User $user, News $news): bool
    {
        // If not authenticated, no access to category-specific news
        if (!$user) {
            return false;
        }

        // Check if news is still active
        if (!$news->isActive()) {
            return false;
        }

        // Check category-based access
        return $news->isAccessibleByCategory($user->client_category ?? 'Inicial');
    }

    /**
     * Determine whether the user can create models.
     * Only admins can create news.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     * Only admins can update news.
     */
    public function update(User $user, News $news): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     * Only admins can delete news.
     */
    public function delete(User $user, News $news): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, News $news): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, News $news): bool
    {
        return $user->isAdmin();
    }
}
