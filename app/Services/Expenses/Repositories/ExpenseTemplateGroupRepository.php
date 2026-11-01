<?php

namespace App\Services\Expenses\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Expenses\ExpenseTemplateGroup;
use Illuminate\Support\Collection;

class ExpenseTemplateGroupRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ExpenseTemplateGroup $model
    ) {
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->query()
            ->withUserPriority($userId)
            ->where(function ($query) use ($userId) {
                $query->where('expense_template_groups.user_id', $userId)
                    ->orWhereNull('expense_template_groups.user_id');
            })
            ->get();
    }

    public function getWithTemplatesForUser(int $userId, int|null $familyGroupId): Collection
    {
        return $this->model->query()
            ->withUserPriority($userId)
            ->where(function ($query) use ($userId) {
                $query->where('expense_template_groups.user_id', $userId)
                    ->orWhereNull('expense_template_groups.user_id');
            })
            ->with([
                'templates' => fn ($query) => $query
                    ->withGroupCopy($familyGroupId)
                    ->orderBy('expense_templates.id'),
            ])
            ->get();
    }

    public function firstForUser(int $id, int $userId): ExpenseTemplateGroup|null
    {
        return $this->model->query()
            ->where('id', $id)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereNull('user_id');
            })
            ->first();
    }
}
