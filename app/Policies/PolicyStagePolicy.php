<?php

namespace App\Policies;

use App\Models\PolicyStage;
use App\Models\User;

class PolicyStagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Data Entry Manager', 'Viewer']);
    }

    public function view(User $user, PolicyStage $model): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Data Entry Manager', 'Viewer']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update(User $user, PolicyStage $model): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete(User $user, PolicyStage $model): bool
    {
        return $user->hasRole('Super Admin') && !$model->policyStatuses()->exists();
    }
}
