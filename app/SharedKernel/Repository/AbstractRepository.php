<?php

namespace App\SharedKernel\Repository;

use App\SharedKernel\Repository\Contracts\{
    CreateContract, 
    UpdateContract,
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;

abstract class AbstractRepository implements CreateContract, UpdateContract
{
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function insert(array $data): void
    {
        $this->model->insert($data);
    }

    public function update(array $filters, array $data): void
    {
        $this->model->where($filters)->update($data);
    }

    public function updateWhereIn(string $row, array $values, array $data): void
    {
        $this->model->whereIn($row, $values)->update($data);
    }

    public function delete(array $filters): void
    {
        $this->model->where($filters)->delete();
    }

    public function getWhere(array $filters, string|Expression|null|array $row = null): Collection
    {
        return $this->model
            ->when($row != null, function ($query) use ($row) {
                $query->select($row);
            })
            ->where($filters)
            ->get();
    }

    public function firstWhere(array $filters, string|Expression|null|array $row = null): Model|null
    {
        return $this->model
            ->where($filters)
            ->when($row != null, function ($query) use ($row) {
                $query->select($row);
            })
            ->first();
    }
}
