<?php

namespace App\Policies;

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
        return true;
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    public function update(User $user, Client $client): bool
    {
        return $this->isCoordinator($user, $client);
    }

    /**
     * @param  User  $user
     * @param  Client  $client
     * @return bool
     */
    public function delete(User $user, Client $client): bool
    {
        return $this->isCoordinator($user, $client);
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
