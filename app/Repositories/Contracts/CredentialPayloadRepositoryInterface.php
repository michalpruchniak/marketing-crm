<?php

namespace App\Repositories\Contracts;

interface CredentialPayloadRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  string  $uuid
     * @param  string  $encryptedPayload
     * @return void
     */
    public function upsertEncrypted(string $uuid, string $encryptedPayload): void;
}
