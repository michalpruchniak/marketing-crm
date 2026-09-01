<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @param  Model  $model
     */
    public function __construct(
        protected Model $model,
    ) {}

    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model|null
     */
    public function find(string|int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(string|int $id, array $columns = ['*']): Model
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param  array<string, mixed>  $where
     * @param  array<string, 'asc'|'desc'>  $orderBy
     * @param  list<string>  $columns
     * @return Collection<int, Model>
     */
    public function get(array $where = [], array $orderBy = [], array $columns = ['*']): Collection
    {
        $query = $this->applyFilters($this->model->select($columns), $where);
        $this->applyOrderBy($query, $orderBy);

        return $query->get();
    }

    /**
     * @param  array<string, mixed>  $where
     * @param  list<string>  $columns
     * @return Model|null
     */
    public function first(array $where = [], array $columns = ['*']): ?Model
    {
        $query = $this->applyFilters($this->model->select($columns), $where);

        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $where
     * @param  list<string>  $columns
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function firstOrFail(array $where = [], array $columns = ['*']): Model
    {
        $query = $this->applyFilters($this->model->select($columns), $where);

        return $query->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @param  Model  $model
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh();
    }

    /**
     * @param  Model  $model
     * @return bool
     */
    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }

    /**
     * @param  array<string, mixed>  $where
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function updateOrCreate(array $where, array $data): Model
    {
        return $this->model->updateOrCreate($where, $data);
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, mixed>  $where
     * @return Builder<Model>
     */
    protected function applyFilters(Builder $query, array $where): Builder
    {
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, 'asc'|'desc'>  $orderBy
     * @return void
     */
    protected function applyOrderBy(Builder $query, array $orderBy): void
    {
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }
}
