<?php

namespace App\Models;

use App\Http\DTO\SecretPayloadDTO;
use Illuminate\Database\Eloquent\Model;

class CredentialPayload extends Model
{
    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'encrypted_payload',
    ];

    public function decryptedPayload(): SecretPayloadDTO
    {
        /** @var array{login: string, password: string, additional_information?: string|null, url?: string|null} $payload */
        $payload = json_decode(decrypt($this->encrypted_payload), true, 512, JSON_THROW_ON_ERROR);

        return new SecretPayloadDTO(
            login: $payload['login'] ?? null,
            password: $payload['password'] ?? null,
            additionalInformation: $payload['additional_information'] ?? null,
            url: $payload['url'] ?? null,
        );
    }

    public static function encryptPayload(SecretPayloadDTO $payload): string
    {
        return encrypt(
            json_encode($payload->toArray(), JSON_THROW_ON_ERROR)
        );
    }
}
