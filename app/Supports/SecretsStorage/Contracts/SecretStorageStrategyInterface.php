<?php

namespace App\Supports\SecretsStorage\Contracts;

use RuntimeException;

interface SecretStorageStrategyInterface
{
    /**
     * @param  string  $uuid
     * @param  array<string, mixed>  $payload
     * @return void
     *
     * @throws RuntimeException
     */
    public function store(string $uuid, array $payload): void;

    /**
     * @param  string  $uuid
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $uuid): void;

    /**
     * @param  string  $uuid
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function reveal(string $uuid): array;
}
