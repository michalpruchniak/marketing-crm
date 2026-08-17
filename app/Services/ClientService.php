<?php

namespace App\Services;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Database\Eloquent\Collection;

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

    public function create(array $data): Client
    {
        /** @var Client $client */
        $client = $this->clients->create($data);

        return $client;
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
