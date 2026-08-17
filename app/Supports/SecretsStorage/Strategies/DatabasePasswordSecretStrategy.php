<?php

namespace App\Supports\SecretsStorage\Strategies;

use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;
use App\Supports\SecretsStorage\Contracts\PasswordSecretStrategyInterface;
use App\Http\DTO\SecretPayloadDTO;
use RuntimeException;

final class DatabasePasswordSecretStrategy implements PasswordSecretStrategyInterface
{
    public function __construct(
        private readonly CredentialPayloadRepositoryInterface $payloads,
    ) {}

    public function store(string $uuid, SecretPayloadDTO $payload): void
    {
        $this->payloads->upsertEncrypted(
            uuid: $uuid,
            encryptedPayload: CredentialPayload::encryptPayload([
                'login' => $payload->login,
                'password' => $payload->password,
                'additional_information' => $payload->additionalInformation,
            ]),
        );
    }

    public function remove(string $uuid): void
    {
        $this->payloads->deleteByUuid($uuid);
    }

    public function reveal(string $uuid): SecretPayloadDTO
    {
        /** @var CredentialPayload|null $record */
        $record = $this->payloads->first(['uuid' => $uuid]);

        if ($record === null) {
            throw new RuntimeException('Nie można wyświetlić sekretu. Zaszyfrowane dane nie zostały znalezione w lokalnej bazie.');
        }

        try {
            $payload = $record->decryptedPayload();
        } catch (\Throwable) {
            throw new RuntimeException('Nie można wyświetlić sekretu. Odszyfrowanie danych w lokalnej bazie nie powiodło się.');
        }

        return new SecretPayloadDTO(
            login: $payload['login'],
            password: $payload['password'],
            additionalInformation: $payload['additional_information'],
        );
    }
}
