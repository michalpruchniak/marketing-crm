<?php

namespace App\Services;

use App\Http\DTO\StoreClientDTO;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientService implements ClientServiceInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientsRepository,
        private readonly CredentialServiceInterface $credentialsService,
    ) {}

    /**
     * @return Collection<int, Client>
     */
    public function getAll(): Collection
    {
        /** @var Collection<int, Client> */
        return $this->clientsRepository->get(orderBy: ['name' => 'asc'], columns: [
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
        return $this->clientsRepository->create($dto->toArray());
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id): Client
    {
        /** @var Client $client */
        $client = $this->clientsRepository->findOrFail($id);

        return $client;
    }

    public function delete(Client $client): void
    {
        $this->credentialsService->deleteAllForClient($client);
        $this->clientsRepository->deleteModel($client);
    }
}
