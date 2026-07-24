<?php

namespace App\Policies;

use App\Models\Content;
use App\Models\User;

class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Content $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Content $model): bool
    {
        return true;
    }

    public function delete(User $user, Content $model): bool
    {
        return true;
    }

    public function restore(User $user, Content $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, Content $model): bool
    {
        return true;
    }
}
