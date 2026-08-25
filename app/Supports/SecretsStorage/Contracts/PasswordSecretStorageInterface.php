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
     * @template T
     *
     * @return T
     *
     * @throws Throwable
     */
        public function store(SecretPayloadDTO $payload): StoredSecret;


    /**
     * @throws RuntimeException
     */
    public function reveal(string $uuid): SecretPayloadDTO;

    public function remove(string $uuid, SecretsDriver $driver): void;
}
