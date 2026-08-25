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
        if (! array_key_exists('login', $data) || ! array_key_exists('password', $data)) {
            throw new \InvalidArgumentException('Secret payload must contain login and password.');
        }

        if (! is_string($data['login']) || ! is_string($data['password'])) {
            throw new \InvalidArgumentException('Secret payload login and password must be strings.');
        }

        return new self(
            login: $data['login'],
            password: $data['password'],
            additionalInformation: isset($data['additional_information']) && is_string($data['additional_information'])
                ? $data['additional_information']
                : null,
            url: isset($data['url']) && is_string($data['url']) ? $data['url'] : null,
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
