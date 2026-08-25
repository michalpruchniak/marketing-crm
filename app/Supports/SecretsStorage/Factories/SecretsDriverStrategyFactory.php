<?php

namespace App\Supports\SecretsStorage\Factories;

use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Strategies\DatabaseSecretStorageStrategy;
use App\Supports\SecretsStorage\Strategies\HashicorpSecretStorageStrategy;
use InvalidArgumentException;

final class SecretsDriverStrategyFactory
{
    public function create(): SecretStorageStrategyInterface
    {
        return $this->createFor($this->currentDriver());
    }

    public function createFor(SecretsDriver $driver): SecretStorageStrategyInterface
    {
        return match ($driver) {
            SecretsDriver::Database => app(DatabaseSecretStorageStrategy::class),
            SecretsDriver::Hashicorp => app(HashicorpSecretStorageStrategy::class),
        };
    }

    /**
     * @throws InvalidArgumentException
     */
    public function currentDriver(): SecretsDriver
    {
        return SecretsDriver::tryFrom((string) config('secrets.driver'))
            ?? throw new InvalidArgumentException(
                'Unknown secrets driver ['.config('secrets.driver').'].',
            );
    }
}
