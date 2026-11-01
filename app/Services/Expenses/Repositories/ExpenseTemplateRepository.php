<?php

namespace App\Services\Expenses\Repositories;

use App\Models\Expenses\ExpenseTemplate;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use Illuminate\Support\Collection;

class ExpenseTemplateRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ExpenseTemplate $model
    ) {
    }

    public function firstForUser(int $id, int $userId, int|null $familyGroupId, bool $onlyActive = false): ExpenseTemplate|null
    {
        return $this->model->query()
            ->withGroupCopy($familyGroupId)
            ->where('expense_templates.id', $id)
            ->when($onlyActive, fn ($query) => $query->whereRaw('coalesce(expense_templates_user_groups.is_active, expense_templates.is_active) = 1'))
            ->whereHas('templateGroup', fn ($query) => $query->where(function ($query) use ($userId) {
                $query->where('expense_template_groups.user_id', $userId)
                    ->orWhereNull('expense_template_groups.user_id');
            }))
            ->first();
    }

    public function firstGlobal(int $id): ExpenseTemplate|null
    {
        return $this->model->query()
            ->where('id', $id)
            ->whereHas('templateGroup', fn ($query) => $query->whereNull('user_id'))
            ->first();
    }

    public function getByGroup(int $templateGroupId, int|null $familyGroupId): Collection
    {
        return $this->model->query()
            ->withGroupCopy($familyGroupId)
            ->where('expense_templates.template_group_id', $templateGroupId)
            ->orderBy('expense_templates.id')
            ->get();
    }
}

