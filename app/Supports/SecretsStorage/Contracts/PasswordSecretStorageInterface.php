<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\ValueObjects\StoredSecret;
use RuntimeException;
use Throwable;

interface PasswordSecretStorageInterface
{
    public function currentDriver(): SecretsDriver;

    /**
     * @throws Throwable
     */
    public function store(SecretPayloadDTO $payload): StoredSecret;

    /**
     * @throws RuntimeException
     */
    public function reveal(string $uuid): SecretPayloadDTO;

    /**
     * @throws RuntimeException
     */
    public function delete(string $uuid, SecretsDriver $driver): void;
}
