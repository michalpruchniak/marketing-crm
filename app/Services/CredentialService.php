<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Credential;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use App\Services\Contracts\CredentialServiceInterface;
use App\Http\DTO\RevealedCredentialDTO;
use App\Http\DTO\SecretPayloadDTO;
use App\Http\DTO\StoreCredentialDTO;
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

    public function listForClient(string $clientId): Collection
    {
        return $this->credentials->forClientAndType(
            clientId: $clientId,
            type: $this->factory->currentDriver()->value,
            columns: ['id', 'client_id', 'user_id', 'uuid', 'type', 'name', 'description', 'created_at'],
        );
    }

    public function store(StoreCredentialDTO $data): Credential
    {
        $driver = $this->factory->currentDriver();
        $uuid = hash('sha256', (string) Str::ulid());
        $strategy = $this->factory->create($driver);

        return DB::transaction(function () use ($data, $driver, $uuid, $strategy): Credential {
            $strategy->store($uuid, new SecretPayloadDTO(
                login: $data->login,
                password: $data->password,
                additionalInformation: $data->additionalInformation,
                url: $data->url,
            ));

            /** @var Credential $credential */
            $credential = $this->credentials->create([
                'client_id' => $data->clientId,
                'user_id' => $data->userId,
                'uuid' => $uuid,
                'type' => $driver->value,
                'name' => $data->name,
                'description' => $data->description,
            ]);

            return $credential;
        });
    }

    public function reveal(Client $client, string $credentialId): RevealedCredentialDTO
    {
        $credential = $this->credentials->findForClient($client->id, $credentialId);
        $strategy = $this->factory->create($credential->driver());

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

    public function delete(Client $client, string $credentialId): void
    {
        $credential = $this->credentials->findForClient($client->id, $credentialId);
        $strategy = $this->factory->create($credential->driver());

        DB::transaction(function () use ($credential, $strategy): void {
            $strategy->remove($credential->uuid);
            $this->credentials->deleteModel($credential);
        });
    }

    public function deleteAllForClient(Client $client): void
    {
        foreach ($this->credentials->allForClient($client->id) as $credential) {
            $strategy = $this->factory->create($credential->driver());

            try {
                $strategy->remove($credential->uuid);
            } catch (Throwable) {
                // Meta is removed even if remote payload is already gone.
            }

            $this->credentials->deleteModel($credential);
        }
    }

    public function currentDriver(): SecretsDriver
    {
        return $this->factory->currentDriver();
    }
}
