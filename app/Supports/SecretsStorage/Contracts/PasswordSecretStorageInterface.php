<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\ValueObjects\StoredSecret;
use RuntimeException;
use Throwable;

interface PasswordSecretStorageInterface
{
    /**
     * @return SecretsDriver
     */
    public function currentDriver(): SecretsDriver;

    /**
     * @param  SecretPayloadDTO  $payload
     * @return StoredSecret
     *
     * @throws Throwable
     */
    public function store(SecretPayloadDTO $payload): StoredSecret;

    /**
     * @param  string  $uuid
     * @return SecretPayloadDTO
     *
     * @throws RuntimeException
     */
    public function reveal(string $uuid): SecretPayloadDTO;

    /**
     * @param  string  $uuid
     * @param  SecretsDriver  $driver
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $uuid, SecretsDriver $driver): void;
}
