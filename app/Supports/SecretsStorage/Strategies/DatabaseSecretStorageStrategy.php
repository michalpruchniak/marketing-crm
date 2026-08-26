<?php

namespace App\Supports\SecretsStorage\Strategies;

use App\Http\DTO\SecretPayloadDTO;
use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;
use App\Supports\SecretsStorage\Contracts\SecretStorageStrategyInterface;
use RuntimeException;

final class DatabaseSecretStorageStrategy implements SecretStorageStrategyInterface
{
    /**
     * @param  CredentialPayloadRepositoryInterface  $credentialPayloadRepository
     */
    public function __construct(
        private readonly CredentialPayloadRepositoryInterface $credentialPayloadRepository,
    ) {}

    /**
     * @param  string  $uuid
     * @param  array<string, mixed>  $payload
     * @return void
     *
     * @throws RuntimeException
     */
    public function store(string $uuid, array $payload): void
    {
        $this->credentialPayloadRepository->upsertEncrypted(
            uuid: $uuid,
            encryptedPayload: CredentialPayload::encryptPayload(SecretPayloadDTO::fromArray($payload)),
        );
    }

    /**
     * @param  string  $uuid
     * @return void
     *
     * @throws RuntimeException
     */
    public function delete(string $uuid): void
    {
        /** @var CredentialPayload $payload */
        $payload = $this->credentialPayloadRepository->findOrFail($uuid);

        $this->credentialPayloadRepository->delete($payload);
    }

    /**
     * @param  string  $uuid
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function reveal(string $uuid): array
    {
        /** @var CredentialPayload $credential */
        $credential = $this->credentialPayloadRepository->firstOrFail(['uuid' => $uuid]);

        try {
            $payload = $credential->decryptedPayload();
        } catch (\Throwable) {
            throw new RuntimeException('The secret cannot be displayed. Decryption of the data in the local database has failed.');
        }

        return $payload->toArray();
    }
}
