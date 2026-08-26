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
use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;

final class PasswordSecretStorage implements PasswordSecretStorageInterface
{

    private SecretStorageStrategyInterface $strategy;

    /**
     * @param  SecretsDriverStrategyFactory  $secretsDriverStrategyFactory
     */
    public function __construct(
        private readonly SecretsDriverStrategyFactory $secretsDriverStrategyFactory,
    ) {
        $this->strategy = $this->secretsDriverStrategyFactory->create();
    }

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
        $uuid = (string) Str::ulid();

        try {
            $this->strategy->store($uuid, $payload->toArray());
        } catch (Throwable $exception) {
            $this->strategy->delete($uuid);

            throw $exception;
        }

        return new StoredSecret(
            uuid: $uuid,
            driver: $this->currentDriver(),
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
        return SecretPayloadDTO::fromArray($this->strategy->reveal($uuid));
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
        $this->strategy->delete($uuid);
    }
}
