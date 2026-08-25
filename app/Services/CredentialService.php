<?php

namespace App\Services;

use App\Http\DTO\RevealedCredentialDTO;
use App\Http\DTO\StoreCredentialDTO;
use App\Models\Client;
use App\Models\Credential;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use App\Services\Contracts\CredentialServiceInterface;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStorageInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class CredentialService implements CredentialServiceInterface
{
    public function __construct(
        private readonly CredentialRepositoryInterface $credentialsRepository,
        private readonly PasswordSecretStorageInterface $passwordSecretStorage,
    ) {}

    /**
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId): Collection
    {
        /** @var Collection<int, Credential> */
        return $this->credentialsRepository->forClientAndType(
            clientId: $clientId,
            type: $this->passwordSecretStorage->currentDriver()->value,
            columns: ['id', 'client_id', 'user_id', 'uuid', 'type', 'name', 'description', 'created_at'],
        );
    }

    public function store(StoreCredentialDTO $data): Credential
    {
        $storedSecret = $this->passwordSecretStorage->store($data->toSecretPayloadDTO());

        try {
            /** @var Credential */
            return DB::transaction(fn () => $this->credentialsRepository->create([
                ...$data->toArray(),
                'uuid' => $storedSecret->uuid,
                'type' => $storedSecret->driver->value,
            ]));
        } catch (Throwable $exception) {
            $this->passwordSecretStorage->delete($storedSecret->uuid, $storedSecret->driver);

            throw $exception;
        }
    }

    /**
     * @throws RuntimeException
     */
    public function reveal(Client $client, string $credentialId): RevealedCredentialDTO
    {
        $credential = $this->findCredentialForActiveDriver($client->id, $credentialId);
        $payload = $this->passwordSecretStorage->reveal($credential->uuid);

        return new RevealedCredentialDTO(
            id: $credential->id,
            name: $credential->name,
            description: $credential->description,
            login: $payload->login,
            password: $payload->password,
            additionalInformation: $payload->additionalInformation,
            url: $payload->url,
        );
    }

    public function delete(Client $client, string $credentialId): void
    {
        $credential = $this->findCredentialForActiveDriver($client->id, $credentialId);

        DB::transaction(function () use ($credential): void {
            $this->passwordSecretStorage->delete($credential->uuid, $credential->driver());
            $this->credentialsRepository->delete($credential);
        });
    }

    public function deleteAllForClient(Client $client): void
    {
        foreach ($this->credentialsRepository->allForClient($client->id) as $credential) {
            $this->passwordSecretStorage->delete($credential->uuid, $credential->driver());
            $this->credentialsRepository->delete($credential);
        }
    }

    public function currentDriver(): SecretsDriver
    {
        return $this->passwordSecretStorage->currentDriver();
    }

    private function findCredentialForActiveDriver(string $clientId, string $credentialId): Credential
    {
        return $this->credentialsRepository->findForClient(
            clientId: $clientId,
            credentialId: $credentialId,
            type: $this->passwordSecretStorage->currentDriver()->value,
        );
    }
}
