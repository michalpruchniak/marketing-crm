<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|null>
 */
final readonly class SecretPayloadDTO implements Arrayable
{
    public function __construct(
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
        public ?string $url = null,
    ) {}

    /**
     * @param  array{login: string, password: string, additional_information?: string|null, url?: string|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            login: $data['login'],
            password: $data['password'],
            additionalInformation: $data['additional_information'] ?? null,
            url: $data['url'] ?? null,
        );
    }

    /**
     * @return array{login: string, password: string, additional_information: string|null, url: string|null}
     */
    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
            'additional_information' => $this->additionalInformation,
            'url' => $this->url,
        ];
    }
}
