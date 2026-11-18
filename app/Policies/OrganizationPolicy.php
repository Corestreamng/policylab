<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_any_organization', 'manage_users']);
    }

    public function view(User $user, Organization $model): bool
    {
        return $user->hasAnyPermission(['view_any_organization', 'manage_users']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create_organization', 'manage_users']);
    }

    public function update(User $user, Organization $model): bool
    {
        return $user->hasAnyPermission(['update_organization', 'manage_users']);
    }

    public function delete(User $user, Organization $model): bool
    {
        return $user->hasAnyPermission(['delete_organization', 'manage_users']);
    }
}
