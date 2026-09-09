<?php

namespace App\Services;

use App\Events\ClientCreated;
use App\Events\ClientUpdated;
use App\Http\DTO\StoreClientDTO;
use App\Http\DTO\UpdateClientDTO;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\CredentialServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use LogicException;

class ClientService implements ClientServiceInterface
{
    /**
     * @param  ClientRepositoryInterface  $clientsRepository
     * @param  CredentialServiceInterface  $credentialsService
     */
    public function __construct(
        private readonly ClientRepositoryInterface $clientsRepository,
        private readonly CredentialServiceInterface $credentialsService,
    ) {}

    /**
     * @return Collection<int, Client>
     */
    public function getAll(): Collection
    {
        $clients = $this->clientsRepository->get(
            orderBy: ['name' => 'asc'],
            columns: [
                'id',
                'name',
                'email',
                'phone',
                'coordinator_id',
                'created_at',
            ],
        );

        $clients->load(['coordinator:id,name']);

        /** @var Collection<int, Client> */
        return $clients;
    }

    /**
     * @param  StoreClientDTO  $dto
     * @return Client
     */
    public function create(StoreClientDTO $dto): Client
    {
        $coordinatorId = $dto->coordinatorId ?? Auth::id();

        if ($coordinatorId === null) {
            throw new LogicException('Authenticated user required to create a client.');
        }

        $client = $this->clientsRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'notes' => $dto->notes,
            'coordinator_id' => $coordinatorId,
        ]);

        if (! $client instanceof Client) {
            throw new LogicException('Expected Client model instance.');
        }

        $client->load(['coordinator:id,name']);

        event(new ClientCreated($client));

        return $client;
    }

    /**
     * @param  string  $id
     * @return Client
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string $id): Client
    {
        $client = $this->clientsRepository->findOrFail($id);

        if (! $client instanceof Client) {
            throw new LogicException('Expected Client model instance.');
        }

        return $client;
    }

    /**
     * @param  Client  $client
     * @param  UpdateClientDTO  $dto
     * @return Client
     */
    public function update(Client $client, UpdateClientDTO $dto): Client
    {
        $client = $this->clientsRepository->update($client, $dto->toArray());

        if (! $client instanceof Client) {
            throw new LogicException('Expected Client model instance.');
        }

        event(new ClientUpdated($client));

        return $client;
    }

    /**
     * @param  Client  $client
     * @return void
     */
    public function delete(Client $client): void
    {
        $this->credentialsService->deleteAllForClient($client);
        $this->clientsRepository->delete($client);
    }
}
