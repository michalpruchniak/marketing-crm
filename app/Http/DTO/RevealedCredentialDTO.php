<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

final readonly class RevealedCredentialDTO implements Arrayable
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description,
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
        public ?string $url = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'login' => $this->login,
            'password' => $this->password,
            'additional_information' => $this->additionalInformation,
            'url' => $this->url,
        ];
    }
}
