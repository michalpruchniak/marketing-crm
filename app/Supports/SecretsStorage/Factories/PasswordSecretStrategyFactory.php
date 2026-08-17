<?php

namespace App\Supports\SecretsStorage\Factories;

use App\Supports\SecretsStorage\Clients\HashicorpVaultClient;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStrategyInterface;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use App\Supports\SecretsStorage\Strategies\DatabasePasswordSecretStrategy;
use App\Supports\SecretsStorage\Strategies\HashicorpPasswordSecretStrategy;
use InvalidArgumentException;

final class PasswordSecretStrategyFactory
{
    public function create(): PasswordSecretStrategyInterface
    {
        $resolved = SecretsDriver::tryFrom((string) config('secrets.driver'))
            ?? throw new InvalidArgumentException(
                'Unknown secrets driver ['.config('secrets.driver').'].',
            );

        return match ($resolved) {
            SecretsDriver::Database => app(DatabasePasswordSecretStrategy::class),
            SecretsDriver::Hashicorp => new HashicorpPasswordSecretStrategy(
                client: new HashicorpVaultClient(
                    address: (string) config('secrets.hashicorp.address'),
                    token: (string) config('secrets.hashicorp.token'),
                ),
            ),
        };
    }

    public function currentDriver(): SecretsDriver
    {
        return SecretsDriver::tryFrom((string) config('secrets.driver'))
            ?? throw new InvalidArgumentException(
                'Unknown secrets driver ['.config('secrets.driver').'].',
            );
    }
}
