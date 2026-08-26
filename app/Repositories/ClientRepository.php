<?php

namespace App\Repositories;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    /**
     * @param  Client  $model
     */
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
}
