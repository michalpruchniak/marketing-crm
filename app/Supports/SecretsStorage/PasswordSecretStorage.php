<?php

namespace App\Supports\SecretsStorage;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStorageInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Factories\SecretsDriverStrategyFactory;
use App\Supports\SecretsStorage\ValueObjects\StoredSecret;
use Illuminate\Support\Str;
use Throwable;

final class PasswordSecretStorage implements PasswordSecretStorageInterface
{
    public function __construct(
        private readonly SecretsDriverStrategyFactory $secretsDriverStrategyFactory,
    ) {}

    public function currentDriver(): SecretsDriver
    {
        return $this->secretsDriverStrategyFactory->currentDriver();
    }

    public function store(SecretPayloadDTO $payload): StoredSecret
    {
        $driver = $this->currentDriver();
        $strategy = $this->secretsDriverStrategyFactory->createFor($driver);
        $uuid = (string) Str::ulid();

        try {
            $strategy->store($uuid, $payload->toArray());
        } catch (Throwable $exception) {
            $strategy->delete($uuid);

            throw $exception;
        }

        return new StoredSecret(
            uuid: $uuid,
            driver: $driver,
        );
    }

    public function reveal(string $uuid): SecretPayloadDTO
    {
        $strategy = $this->secretsDriverStrategyFactory->create();

        return SecretPayloadDTO::fromArray($strategy->reveal($uuid));
    }

    public function delete(string $uuid, SecretsDriver $driver): void
    {
        $this->secretsDriverStrategyFactory->createFor($driver)->delete($uuid);
    }
}
