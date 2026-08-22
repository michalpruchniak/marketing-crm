<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;

interface PasswordSecretStrategyInterface
{
    /**
     * @param  string  $uuid
     * @param  SecretPayloadDTO  $payload
     */
    public function store(string $uuid, SecretPayloadDTO $payload): void;

    /**
     * @param  string  $uuid
     */
    public function remove(string $uuid): void;

    /**
     * @param  string  $uuid
     * @return SecretPayloadDTO
     *
     * @throws RuntimeException when the secret cannot be retrieved
     */
    public function reveal(string $uuid): SecretPayloadDTO;
}
