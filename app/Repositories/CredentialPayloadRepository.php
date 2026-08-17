<?php

namespace App\Repositories;

use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;

class CredentialPayloadRepository extends BaseRepository implements CredentialPayloadRepositoryInterface
{
    protected function modelClass(): string
    {
        return CredentialPayload::class;
    }

    public function upsertEncrypted(string $uuid, string $encryptedPayload): void
    {
        CredentialPayload::query()->updateOrCreate(
            ['uuid' => $uuid],
            ['encrypted_payload' => $encryptedPayload],
        );
    }

    public function deleteByUuid(string $uuid): bool
    {
        return (bool) CredentialPayload::query()->whereKey($uuid)->delete();
    }
}
