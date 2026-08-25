<?php

namespace App\Supports\SecretsStorage\Strategies;

use App\Http\DTO\SecretPayloadDTO;
use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;
use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;
use RuntimeException;

final class DatabaseSecretStorageStrategy implements SecretStorageStrategyInterface
{
    public function __construct(
        private readonly CredentialPayloadRepositoryInterface $credentialPayloadRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $uuid, array $payload): void
    {
        $this->credentialPayloadRepository->upsertEncrypted(
            uuid: $uuid,
            encryptedPayload: CredentialPayload::encryptPayload(SecretPayloadDTO::fromArray($payload)),
        );
    }

    public function delete(string $uuid): void
    {
        /** @var CredentialPayload $payload */
        $payload = $this->credentialPayloadRepository->findOrFail($uuid);

        $this->credentialPayloadRepository->delete($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function reveal(string $uuid): array
    {
        /** @var CredentialPayload|null $credential */
        $credential = $this->credentialPayloadRepository->firstOrFail(['uuid' => $uuid]);

        try {
            $payload = $credential->decryptedPayload();
        } catch (\Throwable) {
            throw new RuntimeException('The secret cannot be displayed. Decryption of the data in the local database has failed.');
        }

        return $payload->toArray();
    }
}
