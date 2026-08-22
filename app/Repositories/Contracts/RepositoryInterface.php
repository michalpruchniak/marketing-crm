<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model|null
     */
    public function find(string|int $id, array $columns = ['*']): ?Model;

    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model
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
     * @return Model|null
     */
    public function first(array $where = [], array $columns = ['*']): ?Model;

    /**
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * @param  string|int  $id
     * @return bool
     */
    public function deleteById(string|int $id): bool;

    /**
     * @param  Model  $model
     * @return bool
     */
    public function deleteModel(Model $model): bool;
}
