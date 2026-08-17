<?php

namespace App\Http\DTO;

final readonly class RevealedCredentialDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
    ) {}
}
