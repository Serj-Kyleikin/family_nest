<?php

namespace App\Services\Expenses\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Expenses\Expense;
use Illuminate\Support\Collection;

class ExpenseRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected Expense $model
    ) {
    }

    public function getForUser(int $userId, int|null $groupId): Collection
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->when($groupId !== null, fn ($query) => $query->where('group_id', $groupId))
            ->orderBy('id', 'desc')
            ->get(['id', 'group_id', 'name', 'amount', 'created_at']);
    }
}
