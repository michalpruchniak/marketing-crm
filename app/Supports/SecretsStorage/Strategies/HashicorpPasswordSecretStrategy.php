<?php

namespace App\Supports\SecretsStorage\Strategies;

use App\Supports\SecretsStorage\Clients\HashicorpVaultClient;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStrategyInterface;
use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;
use Throwable;

final class HashicorpPasswordSecretStrategy implements PasswordSecretStrategyInterface
{
    public function __construct(
        private readonly HashicorpVaultClient $client,
    ) {}

    public function store(string $uuid, SecretPayloadDTO $payload): void
    {
        $this->client->put($uuid, $payload->toArray());
    }

    public function remove(string $uuid): void
    {
        $this->client->remove($uuid);
    }

    public function reveal(string $uuid): SecretPayloadDTO
    {
        try {
            $secret = $this->client->get($uuid);
        } catch (Throwable $exception) {
            throw new RuntimeException(
                'Nie można wyświetlić sekretu. Połączenie z HashiCorp Vault nie powiodło się: '.$exception->getMessage(),
            );
        }

        if ($secret === null) {
            throw new RuntimeException('Nie można wyświetlić sekretu. Wpis nie został znaleziony w HashiCorp Vault.');
        }

        return SecretPayloadDTO::fromArray([
            'login' => (string) $secret['login'],
            'password' => (string) $secret['password'],
            'additional_information' => isset($secret['additional_information'])
                ? (string) $secret['additional_information']
                : null,
            'url' => isset($secret['url']) ? (string) $secret['url'] : null,
        ]);
    }
}
