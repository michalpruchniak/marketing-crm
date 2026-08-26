<?php

namespace App\Supports\SecretsStorage;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStorageInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Factories\SecretsDriverStrategyFactory;
use App\Supports\SecretsStorage\ValueObjects\StoredSecret;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

final class PasswordSecretStorage implements PasswordSecretStorageInterface
{
    /**
     * @param  SecretsDriverStrategyFactory  $secretsDriverStrategyFactory
     */
    public function __construct(
        private readonly SecretsDriverStrategyFactory $secretsDriverStrategyFactory,
    ) {}

    /**
     * @return SecretsDriver
     */
    public function currentDriver(): SecretsDriver
    {
        return $this->secretsDriverStrategyFactory->currentDriver();
    }

    /**
     * @param  SecretPayloadDTO  $payload
     * @return StoredSecret
     *
     * @throws Throwable
     */
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

    /**
     * @param  string  $uuid
     * @return SecretPayloadDTO
     *
     * @throws RuntimeException
     */
    public function reveal(string $uuid): SecretPayloadDTO
    {
        $strategy = $this->secretsDriverStrategyFactory->create();

        return SecretPayloadDTO::fromArray($strategy->reveal($uuid));
    }

    /**
     * @param  string  $uuid
     * @param  SecretsDriver  $driver
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $uuid, SecretsDriver $driver): void
    {
        $this->secretsDriverStrategyFactory->createFor($driver)->delete($uuid);
    }
}
