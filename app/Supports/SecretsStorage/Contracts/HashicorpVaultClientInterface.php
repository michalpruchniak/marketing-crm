<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;

interface HashicorpVaultClientInterface
{
    /**
     * @param  string  $uuid
     * @param  SecretPayloadDTO  $payload
     * @return void
     *
     * @throws RuntimeException
     */
    public function store(string $uuid, SecretPayloadDTO $payload): void;

    /**
     * @param  string  $uuid
     * @return array<string, mixed>|null
     *
     * @throws RuntimeException
     */
    public function get(string $uuid): ?array;

    /**
     * @param  string  $uuid
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $uuid): void;
}
