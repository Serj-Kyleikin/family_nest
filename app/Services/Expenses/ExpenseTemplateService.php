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
use Illuminate\Support\Facades\Auth;

class ExpenseTemplateService
{
    public function __construct(
        private readonly ExpenseTemplateRepository $expenseTemplateRepository,
    ) {
    }

    public function create(int $groupId, string $name, ?string $price = null, bool $isActive = true): void
    {
        $userId = Auth::id();

        ExpenseTemplateGroupRulesUtil::isExists($groupId, $userId);

        $this->expenseTemplateRepository->create([
            'group_id'  => $groupId,
            'name'      => $name,
            'price'     => $price,
            'is_active' => $isActive,
        ]);
    }

    public function get(int $id): ExpenseTemplate
    {
        $userId = Auth::id();

        $template = $this->expenseTemplateRepository->firstForUser($id, $userId, false, ['id', 'name']);
        throw_unless(
            $template, 
            HandledException::class, 
            'Not found', 
            404
        );

        return $template;
    }

    public function update(int $id, int $groupId, string $name, ?string $price, bool $isActive): void
    {
        $userId = Auth::id();

        ExpenseTemplateGroupRulesUtil::isExists($groupId, $userId);
        ExpenseTemplateRulesUtil::isExists($id, $userId);

        $this->expenseTemplateRepository->update(['id' => $id], [
            'group_id'  => $groupId,
            'name'      => $name,
            'price'     => $price,
            'is_active' => $isActive,
        ]);
    }

    public function delete(int $id): void
    {
        $userId = Auth::id();

        ExpenseTemplateRulesUtil::isExists($id, $userId);
        $this->expenseTemplateRepository->delete(['id' => $id]);
    }
}
