<?php

namespace App\Supports\SecretsStorage\Clients;

use App\Http\DTO\SecretPayloadDTO;
use App\Supports\SecretsStorage\Contracts\HashicorpVaultClientInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HashicorpVaultClient implements HashicorpVaultClientInterface
{
    private const MOUNT = 'credentials';

    public function __construct(
        private readonly string $address,
        private readonly string $token,
    ) {}

    public function store(string $uuid, SecretPayloadDTO $payload): void
    {
        $response = $this->http()->post($this->dataUrl($uuid), [
            'data' => $payload->toArray(),
        ]);

        if ($response->failed()) {
            throw new RuntimeException("HashiCorp Vault write failed: {$response->body()}");
        }
    }

    public function get(string $uuid): ?array
    {
        $response = $this->http()->get($this->dataUrl($uuid));

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw new RuntimeException("HashiCorp Vault read failed: {$response->body()}");
        }

        /** @var array<string, mixed>|null $data */
        $data = $response->json('data.data');

        return $data;
    }

    public function delete(string $uuid): void
    {
        $response = $this->http()->delete($this->metadataUrl($uuid));

        if ($response->status() !== 404 && $response->failed()) {
            throw new RuntimeException("HashiCorp Vault delete failed: {$response->body()}");
        }
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(rtrim($this->address, '/'))
            ->withHeaders([
                'X-Vault-Token' => $this->token,
            ])
            ->acceptJson()
            ->asJson();
    }

    private function dataUrl(string $uuid): string
    {
        return '/v1/'.self::MOUNT.'/data/'.$uuid;
    }

    private function metadataUrl(string $uuid): string
    {
        return '/v1/'.self::MOUNT.'/metadata/'.$uuid;
    }
}
