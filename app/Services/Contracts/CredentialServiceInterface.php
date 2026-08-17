<?php

namespace App\Services\Contracts;

use App\Models\Client;
use App\Models\Credential;
use App\Http\DTO\RevealedCredentialDTO;
use App\Http\DTO\StoreCredentialDTO;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use Illuminate\Database\Eloquent\Collection;

interface CredentialServiceInterface
{
    /**
     * @return Collection<int, Credential>
     */
    public function listForClient(string $clientId): Collection;

    public function store(StoreCredentialDTO $data): Credential;

    public function reveal(Client $client, string $credentialId): RevealedCredentialDTO;

    public function delete(Client $client, string $credentialId): void;

    public function deleteAllForClient(Client $client): void;

    public function currentDriver(): SecretsDriver;
}
