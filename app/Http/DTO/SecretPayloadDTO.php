<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string|null>
 */
final readonly class SecretPayloadDTO implements Arrayable
{
    public function __construct(
        public ?string $login,
        public ?string $password,
        public ?string $additionalInformation = null,
        public ?string $url = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {

        return new self(
            login: isset($data['login']) && is_string($data['login']) ? $data['login'] : null,
            password: isset($data['password']) && is_string($data['password']) ? $data['password'] : null,
            additionalInformation: isset($data['additional_information']) && is_string($data['additional_information']) ? $data['additional_information'] : null,
            url: isset($data['url']) ? $data['url'] : null,
        );
    }

    /**
     * @return array{login: string|null, password: string|null, additional_information: string|null, url: string|null}
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
