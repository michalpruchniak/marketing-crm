<?php

namespace App\Repositories;

use App\Models\CredentialPayload;
use App\Repositories\Contracts\CredentialPayloadRepositoryInterface;

class CredentialPayloadRepository extends BaseRepository implements CredentialPayloadRepositoryInterface
{
    public function __construct(CredentialPayload $model)
    {
        parent::__construct($model);
    }

    public function upsertEncrypted(string $uuid, string $encryptedPayload): void
    {
        $this->updateOrCreate(
            ['uuid' => $uuid],
            ['encrypted_payload' => $encryptedPayload],
        );
    }
}
