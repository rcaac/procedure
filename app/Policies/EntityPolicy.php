<?php

namespace App\Policies;

use App\Entity;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;

    public function view()
    {
        return auth()->check();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Entity $entity)
    {
        return $user->isAdmin();
    }

    public function delete(User $user)
    {
        return $user->isAdmin();
    }
}
