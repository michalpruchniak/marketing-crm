<?php

namespace App\Repositories;

use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;

class CredentialPayloadRepository extends BaseRepository implements CredentialPayloadRepositoryInterface
{
    /**
     * @return class-string<CredentialPayload>
     */
    protected function modelClass(): string
    {
        return CredentialPayload::class;
    }

    /**
     * @param  string  $uuid
     * @param  string  $encryptedPayload
     */
    public function upsertEncrypted(string $uuid, string $encryptedPayload): void
    {
        CredentialPayload::query()->updateOrCreate(
            ['uuid' => $uuid],
            ['encrypted_payload' => $encryptedPayload],
        );
    }

    /**
     * @param  string  $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        return (bool) CredentialPayload::query()->whereKey($uuid)->delete();
    }
}
