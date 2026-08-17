<?php

namespace App\Models;

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

    /**
     * @return array{login: string, password: string, additional_information: string|null}
     */
    public function decryptedPayload(): array
    {
        /** @var array{login: string, password: string, additional_information?: string|null} $payload */
        $payload = json_decode(decrypt($this->encrypted_payload), true, 512, JSON_THROW_ON_ERROR);

        return [
            'login' => $payload['login'],
            'password' => $payload['password'],
            'additional_information' => $payload['additional_information'] ?? null,
        ];
    }

    /**
     * @param  array{login: string, password: string, additional_information?: string|null}  $payload
     */
    public static function encryptPayload(array $payload): string
    {
        return encrypt(json_encode([
            'login' => $payload['login'],
            'password' => $payload['password'],
            'additional_information' => $payload['additional_information'] ?? null,
        ], JSON_THROW_ON_ERROR));
    }
}
