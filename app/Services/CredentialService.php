<?php

namespace App\Services;

use App\Http\DTO\RevealedCredentialDTO;
use App\Http\DTO\SecretPayloadDTO;
use App\Http\DTO\StoreCredentialDTO;
use App\Models\Client;
use App\Models\Credential;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use App\Services\Contracts\CredentialServiceInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Factories\PasswordSecretStrategyFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CredentialService implements CredentialServiceInterface
{
    public function __construct(
        private readonly CredentialRepositoryInterface $credentials,
        private readonly PasswordSecretStrategyFactory $factory,
    ) {}

    /**
     * @param  string  $clientId
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId): Collection
    {
        /** @var Collection<int, Credential> */
        return $this->credentials->forClientAndType(
            clientId: $clientId,
            type: $this->factory->currentDriver()->value,
            columns: ['id', 'client_id', 'user_id', 'uuid', 'type', 'name', 'description', 'created_at'],
        );
    }

    /**
     * @param  StoreCredentialDTO  $data
     * @return Credential
     */
    public function store(StoreCredentialDTO $data): Credential
    {
        $driver = $this->factory->currentDriver();
        $uuid = hash('sha256', (string) Str::ulid());
        $strategy = $this->factory->create();

        return DB::transaction(function () use ($data, $driver, $uuid, $strategy): Credential {
            $strategy->store($uuid, new SecretPayloadDTO(
                login: $data->login,
                password: $data->password,
                additionalInformation: $data->additionalInformation,
                url: $data->url,
            ));

            $credentialData = $data->toArray();
            $credentialData['uuid'] = $uuid;
            $credentialData['type'] = $driver->value;

            /** @var Credential */
            return $this->credentials->create($credentialData);
        });
    }

    /**
     * @param  Client  $client
     * @param  string  $credentialId
     * @return RevealedCredentialDTO
     *
     * @throws RuntimeException
     */
    public function reveal(Client $client, string $credentialId): RevealedCredentialDTO
    {
        $credential = $this->credentials->findForClient($client->id, $credentialId);
        $strategy = $this->factory->create();

        try {
            $payload = $strategy->reveal($credential->uuid);
        } catch (RuntimeException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new RuntimeException('Nie można wyświetlić sekretu. Wystąpił nieoczekiwany błąd podczas odczytu.');
        }

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

    /**
     * @param  Client  $client
     * @param  string  $credentialId
     */
    public function delete(Client $client, string $credentialId): void
    {
        $credential = $this->credentials->findForClient($client->id, $credentialId);
        $strategy = $this->factory->create();

        DB::transaction(function () use ($credential, $strategy): void {
            $strategy->remove($credential->uuid);
            $this->credentials->deleteModel($credential);
        });
    }

    /**
     * @param  Client  $client
     */
    public function deleteAllForClient(Client $client): void
    {
        foreach ($this->credentials->allForClient($client->id) as $credential) {
            $strategy = $this->factory->create();

            try {
                $strategy->remove($credential->uuid);
            } catch (Throwable) {
                // Meta is removed even if remote payload is already gone.
            }

            $this->credentials->deleteModel($credential);
        }
    }

    /**
     * @return SecretsDriver
     */
    public function currentDriver(): SecretsDriver
    {
        return $this->factory->currentDriver();
    }
}
