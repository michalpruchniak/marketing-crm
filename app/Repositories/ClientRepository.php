<?php

namespace App\Repositories;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    protected function modelClass(): string
    {
        return Client::class;
    }
}
