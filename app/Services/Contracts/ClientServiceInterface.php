<?php

namespace App\Services\Contracts;

use App\Http\DTO\StoreClientDTO;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientServiceInterface
{
    /**
     * @return Collection<int, Client>
     */
    public function getAll(): Collection;

    public function create(StoreClientDTO $dto): Client;

    public function findOrFail(string $id): Client;

    public function delete(Client $client): void;
}
