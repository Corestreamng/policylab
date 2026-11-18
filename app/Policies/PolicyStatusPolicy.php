<?php

namespace App\Policies;

use App\Models\PolicyStatus;
use App\Models\User;

class PolicyStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_any_policy_status', 'manage_users']);
    }

    public function view(User $user, PolicyStatus $model): bool
    {
        return $user->hasAnyPermission(['view_policy_status', 'manage_users']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create_policy_status', 'manage_users']);
    }

    public function update(User $user, PolicyStatus $model): bool
    {
        return $user->hasAnyPermission(['update_policy_status', 'manage_users']);
    }

    public function delete(User $user, PolicyStatus $model): bool
    {
        return $user->hasAnyPermission(['delete_policy_status', 'manage_users']);
    }
}
