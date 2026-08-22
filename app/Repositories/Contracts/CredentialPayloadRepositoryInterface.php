<?php

namespace App\Repositories\Contracts;

interface CredentialPayloadRepositoryInterface extends RepositoryInterface
{
    /**
     * @param  string  $uuid
     * @param  string  $encryptedPayload
     */
    public function upsertEncrypted(string $uuid, string $encryptedPayload): void;

    /**
     * @param  string  $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool;
}
