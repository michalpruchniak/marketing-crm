<?php

namespace App\Repositories;

use App\Models\Credential;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CredentialRepository extends BaseRepository implements CredentialRepositoryInterface
{
    /**
     * @param  Credential  $model
     */
    public function __construct(Credential $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  string  $clientId
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function allForClient(string $clientId, array $columns = ['*']): Collection
    {
        /** @var Collection<int, Credential> */
        return $this->get(
            where: ['client_id' => $clientId],
            columns: $columns,
        );
    }

    /**
     * @param  string  $clientId
     * @param  string  $type
     * @param  list<string>  $columns
     * @return Collection<int, Credential>
     */
    public function forClientAndType(string $clientId, string $type, array $columns = ['*']): Collection
    {
        /** @var Collection<int, Credential> */
        return $this->get(
            where: [
                'client_id' => $clientId,
                'type' => $type,
            ],
            orderBy: ['created_at' => 'desc'],
            columns: $columns,
        );
    }

    /**
     * @param  string  $clientId
     * @param  string  $credentialId
     * @param  string  $type
     * @return Credential
     *
     * @throws ModelNotFoundException
     */
    public function findForClient(string $clientId, string $credentialId, string $type): Credential
    {
        /** @var Credential $credential */
        $credential = $this->firstOrFail(
            where: [
                'client_id' => $clientId,
                'id' => $credentialId,
                'type' => $type,
            ],
        );

        return $credential;
    }
}
