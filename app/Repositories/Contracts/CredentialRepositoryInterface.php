<?php

namespace App\Repositories\Contracts;

use App\Models\Credential;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface CredentialRepositoryInterface extends RepositoryInterface
{
    /**
     * @param  string  $clientId
     * @param  string  $type
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function forClientAndType(string $clientId, string $type, array $columns = ['*']): Collection;

    /**
     * @param  string  $clientId
     * @param  string  $credentialId
     * @return Credential
     *
     * @throws ModelNotFoundException
     */
    public function findForClient(string $clientId, string $credentialId): Credential;

    /**
     * @param  string  $clientId
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId, array $columns = ['*']): Collection;
}
