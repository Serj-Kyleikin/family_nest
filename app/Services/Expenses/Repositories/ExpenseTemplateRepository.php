<?php

namespace App\Services\Expenses\Repositories;

use App\Models\Expenses\ExpenseTemplate;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class ExpenseTemplateRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ExpenseTemplate $model
    ) {
    }

    public function firstForUser(int $id, int $userId, bool $onlyActive = false, array $columns = ['*']): ExpenseTemplate|null
    {
        return $this->model->query()
            ->where('id', $id)
            ->when($onlyActive, fn ($query) => $query->where('is_active', true))
            ->whereHas('templateGroup', fn ($query) => $query->where('user_id', $userId))
            ->first($columns);
    }
}

