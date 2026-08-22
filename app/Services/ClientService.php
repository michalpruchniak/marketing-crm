<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use App\Http\DTO\StoreClientDTO;

class ClientService implements ClientServiceInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clients,
        private readonly CredentialServiceInterface $credentials,
    ) {}

    public function getAll(): Collection
    {
        return $this->clients->get(orderBy: ['name' => 'asc'], columns: [
            'id',
            'name',
            'email',
            'phone',
            'created_at',
        ]);
    }

    public function create(StoreClientDTO $dto): Client
    {
        /** @var Client */
        return $this->clients->create($dto->toArray());
    }

    public function findOrFail(string $id): Client
    {
        /** @var Client $client */
        $client = $this->clients->findOrFail($id);

        return $client;
    }

    public function delete(Client $client): void
    {
        $this->credentials->deleteAllForClient($client);
        $this->clients->deleteModel($client);
    }
}
