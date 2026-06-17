<?php

namespace App\Services\Expenses;

use App\Models\{
    Expenses\ExpenseTemplateGroup,
};
use App\Services\{
    Expenses\Repositories\ExpenseTemplateGroupRepository,
    Expenses\Util\ExpenseTemplateGroupRulesUtil,
};
use App\Exceptions\HandledException;
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ExpenseTemplateGroupService
{
    public function __construct(
        private readonly ExpenseTemplateGroupRepository $expenseTemplateGroupRepository,
    ) 
    {
    }

    public function listWithTemplates(): Collection
    {
        $userId = Auth::id();

        return $this->expenseTemplateGroupRepository->getForUser($userId);
    }

    public function create(string $name): void
    {
        $userId = Auth::id();

        $this->expenseTemplateGroupRepository->create([
            'user_id'   => $userId,
            'name'      => $name,
        ]);
    }

    public function get(int $id): ExpenseTemplateGroup
    {
        $group = $this->expenseTemplateGroupRepository->firstWhere(['id' => $id]);

        throw_unless(
            $group, 
            HandledException::class, 
            'Not found', 
            404
        );

        return $group;
    }

    public function update(int $id, string $name): void
    {
        ExpenseTemplateGroupRulesUtil::isExists($id);
        $this->expenseTemplateGroupRepository->update(['id' => $id], ['name' => $name]);
    }

    public function delete(int $id): void
    {
        ExpenseTemplateGroupRulesUtil::isExists($id);
        $this->expenseTemplateGroupRepository->delete(['id' => $id]);
    }
}
