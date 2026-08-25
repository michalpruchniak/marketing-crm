<?php

namespace App\Supports\SecretsStorage\Contracts;

use RuntimeException;

interface SecretStorageStrategyInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $uuid, array $payload): void;

    public function remove(string $uuid): void;

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException when the secret cannot be retrieved
     */
    public function reveal(string $uuid): array;
}
