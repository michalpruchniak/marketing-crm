<?php

namespace App\Http\DTO;

final readonly class SecretPayloadDTO
{
    public function __construct(
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
        public ?string $url = null,
    ) {}
}
