<?php

namespace App\Http\DTO;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class StoreCredentialDTO implements Arrayable
{
    public function __construct(
        public string $clientId,
        public int $userId,
        public string $name,
        public ?string $description,
        public string $login,
        public string $password,
        public ?string $additionalInformation = null,
        public ?string $url = null,
    ) {}

    /**
     * @return array{client_id: string, user_id: int, name: string, description: string|null}
     */
    public function toArray(): array
    {
        return [
            'client_id' => $this->clientId,
            'user_id' => $this->userId,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public function toSecretPayloadDTO(): SecretPayloadDTO
    {
        return new SecretPayloadDTO(
            login: $this->login,
            password: $this->password,
            additionalInformation: $this->additionalInformation,
            url: $this->url,
        );
    }
}
