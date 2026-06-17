<?php

namespace App\Services\Expenses;

use App\Models\{
    Expenses\ExpenseTemplate,
};
use App\Services\{
    Expenses\Repositories\ExpenseTemplateRepository,
    Expenses\Util\ExpenseTemplateGroupRulesUtil,
    Expenses\Util\ExpenseTemplateRulesUtil,
};
use App\Exceptions\HandledException;

class ExpenseTemplateService
{
    public function __construct(
        private readonly ExpenseTemplateRepository $expenseTemplateRepository,
    ) {
    }

    public function create(int $groupId, string $name, bool $isActive = true): void
    {
        ExpenseTemplateGroupRulesUtil::isExists($groupId);

        $this->expenseTemplateRepository->create([
            'group_id'  => $groupId,
            'name'      => $name,
            'is_active' => $isActive,
        ]);
    }

    public function get(int $id): ExpenseTemplate
    {
        $template = $this->expenseTemplateRepository->firstWhere(['id' => $id], ['id', 'name']);
        throw_unless(
            $template, 
            HandledException::class, 
            'Not found', 
            404
        );

        return $template;
    }

    public function update(int $id, int $groupId, string $name, bool $isActive): void
    {
        ExpenseTemplateGroupRulesUtil::isExists($groupId);
        ExpenseTemplateRulesUtil::isExists($id);

        $this->expenseTemplateRepository->update(['id' => $id], [
            'group_id'  => $groupId,
            'name'      => $name,
            'is_active' => $isActive,
        ]);
    }

    public function delete(int $id): void
    {
        ExpenseTemplateRulesUtil::isExists($id);
        $this->expenseTemplateRepository->delete(['id' => $id]);
    }
}
