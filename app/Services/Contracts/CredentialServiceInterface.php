<?php

namespace App\Services\Contracts;

use App\Http\DTO\RevealedCredentialDTO;
use App\Http\DTO\StoreCredentialDTO;
use App\Models\Client;
use App\Models\Credential;
use App\Supports\SecretsStorage\Enums\SecretsDriver;
use Illuminate\Database\Eloquent\Collection;
use RuntimeException;

interface CredentialServiceInterface
{
    /**
     * @param  string  $clientId
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId): Collection;

    /**
     * @param  StoreCredentialDTO  $data
     * @return Credential
     */
    public function store(StoreCredentialDTO $data): Credential;

    /**
     * @param  Client  $client
     * @param  string  $credentialId
     * @return RevealedCredentialDTO
     *
     * @throws RuntimeException
     */
    public function reveal(Client $client, string $credentialId): RevealedCredentialDTO;

    /**
     * @param  Client  $client
     * @param  string  $credentialId
     * @return void
     */
    public function delete(Client $client, string $credentialId): void;

    /**
     * @param  Client  $client
     * @return void
     */
    public function deleteAllForClient(Client $client): void;

    /**
     * @return SecretsDriver
     */
    public function currentDriver(): SecretsDriver;
}
