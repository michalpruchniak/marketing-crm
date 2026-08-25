<?php

namespace App\Repositories;

use App\Models\Credential;
use App\Repositories\Contracts\CredentialRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CredentialRepository extends BaseRepository implements CredentialRepositoryInterface
{
    public function __construct(Credential $model)
    {
        parent::__construct($model);
    }

    /**
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
            orderBy: ['name' => 'asc'],
            columns: $columns,
        );
    }

    /**
     * @throws ModelNotFoundException
     */
    public function findForClient(string $clientId, string $credentialId, string $type): Credential
    {
        /** @var Credential|null $credential */
        $credential = $this->first(
            where: [
                'client_id' => $clientId,
                'id' => $credentialId,
                'type' => $type,
            ],
        );

        if ($credential === null) {
            throw (new ModelNotFoundException)->setModel(Credential::class, [$credentialId]);
        }

        return $credential;
    }
}
