<?php

namespace App\Repositories\Contracts;

use App\Models\Credential;
use Illuminate\Database\Eloquent\Collection;

interface CredentialRepositoryInterface extends RepositoryInterface
{
    /**
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function forClientAndType(string $clientId, string $type, array $columns = ['*']): Collection;

    public function findForClient(string $clientId, string $credentialId): Credential;

    /**
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId, array $columns = ['*']): Collection;
}
