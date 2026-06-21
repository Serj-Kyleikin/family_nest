<?php

namespace App\Services\Expenses\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Expenses\ExpenseTemplateGroup;

class ExpenseTemplateGroupRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ExpenseTemplateGroup $model
    ) {
    }

    public function getForUser(int $userId)
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->with(['templates'])
            ->get(['id', 'name']);
    }
}

