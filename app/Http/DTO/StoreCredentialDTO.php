<?php

namespace App\Http\DTO;

final readonly class StoreCredentialDTO
{
    public function __construct(
        public string $clientId,
        public int $userId,
        public string $name,
        public ?string $description,
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
    ) {}
}
