<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\User;

class RegionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Data Entry Manager', 'Viewer']);
    }

    public function view(User $user, Region $model): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Data Entry Manager', 'Viewer']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, Region $model): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Data Entry Manager']);
    }

    public function delete(User $user, Region $model): bool
    {
        return $user->hasRole('Super Admin');
    }
}
