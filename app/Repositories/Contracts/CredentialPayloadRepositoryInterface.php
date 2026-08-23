<?php

namespace App\Repositories\Contracts;

interface CredentialPayloadRepositoryInterface extends RepositoryInterface
{
    public function upsertEncrypted(string $uuid, string $encryptedPayload): void;

    public function deleteByUuid(string $uuid): bool;
}
