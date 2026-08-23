<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model,
    ) {}

    /**
     * @param  list<string>  $columns
     */
    public function find(string|int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param  list<string>  $columns
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
     * @param  array<string, mixed>  $where
     * @param  list<string>  $columns
     */
    public function first(array $where = [], array $columns = ['*']): ?Model
    {
        $query = $this->applyFilters($this->model->select($columns), $where);

        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function deleteById(string|int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }

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
     * @param  array<string, mixed>  $where
     * @param  array<string, mixed>  $data
     */
    public function updateOrCreate(array $where, array $data): Model
    {
        return $this->model->updateOrCreate($where, $data);
    }

    public function deleteByUuid(string $uuid): bool
    {
        return (bool) $this->model->whereKey($uuid)->delete();
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
