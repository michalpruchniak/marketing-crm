<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @return Builder<Model>
     */
    protected function newQuery(): Builder
    {
        /** @var Builder<Model> $query */
        $query = ($this->modelClass())::query();

        return $query;
    }

    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model|null
     */
    public function find(string|int $id, array $columns = ['*']): ?Model
    {
        return $this->newQuery()->find($id, $columns);
    }

    /**
     * @param  string|int  $id
     * @param  list<string>  $columns
     * @return Model
     */
    public function findOrFail(string|int $id, array $columns = ['*']): Model
    {
        return $this->newQuery()->findOrFail($id, $columns);
    }

    /**
     * @param  array<string, mixed>  $where
     * @param  array<string, 'asc'|'desc'>  $orderBy
     * @param  list<string>  $columns
     * @return Collection<int, Model>
     */
    public function get(array $where = [], array $orderBy = [], array $columns = ['*']): Collection
    {
        $query = $this->applyFilters($this->newQuery()->select($columns), $where);
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
        $query = $this->applyFilters($this->newQuery()->select($columns), $where);

        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->newQuery()->create($data);
    }

    /**
     * @param  string|int  $id
     * @return bool
     */
    public function deleteById(string|int $id): bool
    {
        return (bool) $this->newQuery()->whereKey($id)->delete();
    }

    /**
     * @param  Model  $model
     * @return bool
     */
    public function deleteModel(Model $model): bool
    {
        return (bool) $model->delete();
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
     */
    protected function applyOrderBy(Builder $query, array $orderBy): void
    {
        foreach ($orderBy as $column => $direction) {
            $query->orderBy($column, $direction);
        }
    }
}
