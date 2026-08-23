<?php

namespace App\Supports\SecretsStorage\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class HashicorpVaultClient
{
    private const COLLECTION = 'credentials';

    public function __construct(
        private readonly string $address,
        private readonly string $token,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws RuntimeException
     */
    public function put(string $uuid, array $data): void
    {
        $response = $this->http()->post($this->dataUrl($uuid), [
            'data' => $data,
        ]);

        if ($response->failed()) {
            throw new RuntimeException("HashiCorp Vault write failed: {$response->body()}");
        }
    }

    /**
     * @return array<string, mixed>|null
     *
     * @throws RuntimeException
     */
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

    /**
     * @throws RuntimeException
     */
    public function remove(string $uuid): void
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
        return '/v1/'.self::COLLECTION.'/data/'.$uuid;
    }

    private function metadataUrl(string $uuid): string
    {
        return '/v1/'.self::COLLECTION.'/metadata/'.$uuid;
    }
}
