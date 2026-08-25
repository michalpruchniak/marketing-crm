<?php

namespace App\Supports\SecretsStorage\Contracts;

use RuntimeException;

interface SecretStorageStrategyInterface
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws RuntimeException
     */
    public function store(string $uuid, array $payload): void;

    /**
     * @throws RuntimeException
     */
    public function delete(string $uuid): void;

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function reveal(string $uuid): array;
}
