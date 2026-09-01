<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    public function view(User $user, Client $client): bool
    {
        return true;
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::ClientsCreate->value);
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    public function update(User $user, Client $client): bool
    {
        if ($user->can(Permission::ClientsUpdateAny->value)) {
            return true;
        }

        return $user->can(Permission::ClientsUpdateOwn->value)
            && $this->isCoordinator($user, $client);
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    public function delete(User $user, Client $client): bool
    {
        if ($user->can(Permission::ClientsDeleteAny->value)) {
            return true;
        }

        return $user->can(Permission::ClientsDeleteOwn->value)
            && $this->isCoordinator($user, $client);
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    private function isCoordinator(User $user, Client $client): bool
    {
        return $client->coordinator_id !== null
            && (int) $client->coordinator_id === (int) $user->id;
    }
}
