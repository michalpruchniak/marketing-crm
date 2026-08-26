<?php

namespace App\Supports\SecretsStorage\Factories;

use App\Supports\SecretsStorage\Contracts\SecretsDriverStrategyFactoryInterface;
use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Strategies\DatabaseSecretStorageStrategy;
use App\Supports\SecretsStorage\Strategies\HashicorpSecretStorageStrategy;
use InvalidArgumentException;

class SecretsDriverStrategyFactory implements SecretsDriverStrategyFactoryInterface
{
    /**
     * @return SecretStorageStrategyInterface
     */
    public function create(): SecretStorageStrategyInterface
    {
        return $this->createFor($this->currentDriver());
    }

    /**
     * @param  SecretsDriver  $driver
     * @return SecretStorageStrategyInterface
     */
    public function createFor(SecretsDriver $driver): SecretStorageStrategyInterface
    {
        return match ($driver) {
            SecretsDriver::Database => app(DatabaseSecretStorageStrategy::class),
            SecretsDriver::Hashicorp => app(HashicorpSecretStorageStrategy::class),
        };
    }

    /**
     * @return SecretsDriver
     *
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
