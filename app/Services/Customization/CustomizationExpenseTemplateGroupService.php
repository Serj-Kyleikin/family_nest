<?php

namespace App\Services\Customization;

use App\Models\Customization\CustomizationExpenseTemplateGroup;
use App\Services\{
    Customization\Repositories\CustomizationExpenseTemplateGroupRepository,
    Expenses\Util\ExpenseTemplateGroupRulesUtil,
};
use Illuminate\Support\Facades\Auth;

class CustomizationExpenseTemplateGroupService
{
    public function __construct(
        private readonly CustomizationExpenseTemplateGroupRepository $customizationExpenseTemplateGroupRepository,
    ) {
    }

    public function save(int $expenseTemplateGroupId, int $priority): CustomizationExpenseTemplateGroup
    {
        $userId     = Auth::id();
                    ExpenseTemplateGroupRulesUtil::isAvailable($expenseTemplateGroupId, $userId);

                    return $this->customizationExpenseTemplateGroupRepository
                                ->save($userId, $expenseTemplateGroupId, $priority);
    }
}
