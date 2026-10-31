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
        $userId = Auth::id();

        $group = $this->expenseTemplateGroupRepository->firstWhere(['id' => $id, 'user_id' => $userId]);

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
        $userId = Auth::id();

        ExpenseTemplateGroupRulesUtil::isExists($id, $userId);
        $this->expenseTemplateGroupRepository->update(['id' => $id, 'user_id' => $userId], ['name' => $name]);
    }

    public function delete(int $id): void
    {
        $userId = Auth::id();

        ExpenseTemplateGroupRulesUtil::isExists($id, $userId);
        $this->expenseTemplateGroupRepository->delete(['id' => $id, 'user_id' => $userId]);
    }
}
