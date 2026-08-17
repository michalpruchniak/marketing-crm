<?php

namespace App\Services\Contracts;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientServiceInterface
{
    /**
     * @return Collection<int, Client>
     */
    public function getAll(): Collection;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Client;

    public function findOrFail(string $id): Client;

    public function delete(Client $client): void;
}
