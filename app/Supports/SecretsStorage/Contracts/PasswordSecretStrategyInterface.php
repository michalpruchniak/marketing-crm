<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;

interface PasswordSecretStrategyInterface
{
    public function store(string $uuid, SecretPayloadDTO $payload): void;

    public function remove(string $uuid): void;

    /**
     * @throws RuntimeException when the secret cannot be retrieved
     */
    public function reveal(string $uuid): SecretPayloadDTO;
}
