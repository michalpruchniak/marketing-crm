<?php

namespace App\Supports\SecretsStorage\ValueObjects;

use App\Supports\SecretsStorage\Enums\SecretsDriver;

final readonly class StoredSecret
{
    public function __construct(
        public string $uuid,
        public SecretsDriver $driver,
    ) {}
}
