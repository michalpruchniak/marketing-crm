<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Supports\SecretsStorage\Enums\SecretsDriver;
use InvalidArgumentException;

interface SecretsDriverStrategyFactoryInterface
{
    /**
     * @return SecretStorageStrategyInterface
     */
    public function create(): SecretStorageStrategyInterface;

    /**
     * @param  SecretsDriver  $driver
     * @return SecretStorageStrategyInterface
     */
    public function createFor(SecretsDriver $driver): SecretStorageStrategyInterface;

    /**
     * @return SecretsDriver
     *
     * @throws InvalidArgumentException
     */
    public function currentDriver(): SecretsDriver;
}
