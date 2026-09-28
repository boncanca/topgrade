<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     * System/technical accounts are hidden from non-system club admins.
     */
    public function view(User $user, User $model): bool
    {
        if ($model->is_system_account && ! $user->is_system_account) {
            return false;
        }

        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     * System/technical accounts cannot be modified by non-system club admins.
     */
    public function update(User $user, User $model): bool
    {
        if ($model->is_system_account && ! $user->is_system_account) {
            return false;
        }

        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     * Protected system/technical accounts cannot be deleted by club admins.
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->is_system_account) {
            return false;
        }

        return $user->is_admin;
    }

    /**
     * Determine whether the user can impersonate the model.
     */
    public function impersonate(User $user, User $model): bool
    {
        if ($model->is_system_account) {
            return false;
        }

        return $user->is_admin;
    }
}
