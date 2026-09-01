<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    /**
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can(Permission::UsersView->value);
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::UsersCreate->value);
    }

    /**
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(Permission::UsersUpdate->value);
    }

    /**
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(Permission::UsersDelete->value)
            && $user->id !== $model->id;
    }

    /**
     * @param  User  $user
     * @param  User  $model
     * @return bool
     */
    public function ban(User $user, User $model): bool
    {
        return $user->can(Permission::UsersBan->value)
            && $user->id !== $model->id;
    }
}
