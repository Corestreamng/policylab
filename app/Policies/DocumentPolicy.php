<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_any_document', 'manage_users']);
    }

    public function view(User $user, Document $model): bool
    {
        return $user->hasAnyPermission(['view_document', 'manage_users']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create_document', 'manage_users']);
    }

    public function update(User $user, Document $model): bool
    {
        return $user->hasAnyPermission(['update_document', 'manage_users']);
    }

    public function delete(User $user, Document $model): bool
    {
        return $user->hasAnyPermission(['delete_document', 'manage_users']);
    }
}
