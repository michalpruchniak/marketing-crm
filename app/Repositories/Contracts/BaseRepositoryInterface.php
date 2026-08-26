<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface BaseRepositoryInterface
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
     *
     * @throws ModelNotFoundException
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
     * @param  array<string, mixed>  $where
     * @param  list<string>  $columns
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function firstOrFail(array $where = [], array $columns = ['*']): Model;

    /**
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * @param  Model  $model
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * @param  Model  $model
     * @return bool
     */
    public function delete(Model $model): bool;
}
