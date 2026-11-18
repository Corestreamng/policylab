<?php

namespace App\Policies;

use App\Models\Funder;
use App\Models\User;

class FunderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_any_funder', 'manage_users']);
    }

    public function view(User $user, Funder $model): bool
    {
        return $user->hasAnyPermission(['view_any_funder', 'manage_users']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create_funder', 'manage_users']);
    }

    public function update(User $user, Funder $model): bool
    {
        return $user->hasAnyPermission(['update_funder', 'manage_users']);
    }

    public function delete(User $user, Funder $model): bool
    {
        return $user->hasAnyPermission(['delete_funder', 'manage_users']);
    }
}
