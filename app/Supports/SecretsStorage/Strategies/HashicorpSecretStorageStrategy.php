<?php

namespace App\Supports\SecretsStorage\Strategies;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Clients\HashicorpVaultClient;
use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;
use RuntimeException;
use Throwable;

final class HashicorpSecretStorageStrategy implements SecretStorageStrategyInterface
{
    public function __construct(
        private readonly HashicorpVaultClient $client,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $uuid, array $payload): void
    {
        $this->client->store($uuid, SecretPayloadDTO::fromArray($payload));
    }

    public function delete(string $uuid): void
    {
        $this->client->delete($uuid);
    }

    /**
     * @return array<string, mixed>
     */
    public function reveal(string $uuid): array
    {
        try {
            $secret = $this->client->get($uuid);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'The secret cannot be displayed. The connection to HashiCorp Vault failed: '.$exception->getMessage(),
            );
        }

        if ($secret === null) {
            throw new RuntimeException('The secret cannot be displayed. The entry was not found in HashiCorp Vault.');
        }

        return SecretPayloadDTO::fromArray($secret)->toArray();
    }
}
