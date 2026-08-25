<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @param  list<string>  $columns
     */
    public function find(string|int $id, array $columns = ['*']): ?Model;

    /**
     * @param  list<string>  $columns
     */
    public function findOrFail(string|int $id, array $columns = ['*']): Model;

    /**
     * @param  array<string, mixed>  $where
     * @param  array<string, 'asc'|'desc'>  $orderBy
     * @param  list<string>  $columns
     * @return Collection<int, Model>
     */
    public function get(array $where = [], array $orderBy = [], array $columns = ['*']): Collection;

    /**
     * @param  array<string, mixed>  $where
     * @param  list<string>  $columns
     */
    public function first(array $where = [], array $columns = ['*']): ?Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model;

    public function delete(Model $model): bool;

    public function deleteByUuid(string $uuid): bool;
}
