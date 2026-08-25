<?php

namespace App\Supports\SecretsStorage\Contracts;

use App\Supports\SecretsStorage\Enums\SecretsDriver;

interface SecretsDriverStrategyFactoryInterface
{
    public function create(): SecretStorageStrategyInterface;

    public function createFor(SecretsDriver $driver): SecretStorageStrategyInterface;

    public function currentDriver(): SecretsDriver;
}
