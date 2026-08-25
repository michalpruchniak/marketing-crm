<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;

interface HashicorpVaultClientInterface
{
    /**
     * @throws RuntimeException
     */
    public function store(string $uuid, SecretPayloadDTO $payload): void;

    /**
     * @return array<string, mixed>|null
     *
     * @throws RuntimeException
     */
    public function get(string $uuid): ?array;

    /**
     * @throws RuntimeException
     */
    public function delete(string $uuid): void;
}
