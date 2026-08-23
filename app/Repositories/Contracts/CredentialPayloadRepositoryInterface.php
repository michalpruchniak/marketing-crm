<?php

namespace App\Repositories\Contracts;

interface CredentialPayloadRepositoryInterface extends RepositoryInterface
{
    public function upsertEncrypted(string $uuid, string $encryptedPayload): void;
}
