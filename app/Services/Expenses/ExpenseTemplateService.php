<?php

namespace App\Services\Expenses;

use App\Models\{
    Expenses\ExpenseTemplate,
};
use App\Services\{
    Expenses\Repositories\ExpenseTemplateRepository,
    Expenses\Repositories\ExpenseTemplateUserGroupRepository,
    Expenses\Util\ExpenseTemplateGroupRulesUtil,
    Expenses\Util\ExpenseTemplateRulesUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ExpenseTemplateService
{
    public function __construct(
        private readonly ExpenseTemplateRepository          $expenseTemplateRepository,
        private readonly ExpenseTemplateUserGroupRepository $expenseTemplateUserGroupRepository,
    ) {
    }

    public function create(int $templateGroupId, string $name, ?string $price = null, bool $isActive = true): void
    {
        $userId     = Auth::id();
                    ExpenseTemplateGroupRulesUtil::isExists($templateGroupId, $userId);

                    $this->expenseTemplateRepository->create([
                        'template_group_id' => $templateGroupId,
                        'name'              => $name,
                        'price'             => $price,
                        'is_active'         => $isActive,
                    ]);
    }

    public function list(int $templateGroupId): Collection
    {
        $user       = Auth::user();
                    ExpenseTemplateGroupRulesUtil::isAvailable($templateGroupId, $user->id);

        return $this->expenseTemplateRepository->getByGroup($templateGroupId, $user->group_id);
    }

    public function show(int $id): ExpenseTemplate
    {
        $user       = Auth::user();

        $template   = $this->expenseTemplateRepository
                            ->firstForUser($id, $user->id, $user->group_id);
                    ExpenseTemplateRulesUtil::isNotNull($template);

                    return $template;
    }

    public function update(int $id, int $templateGroupId, string $name, ?string $price, bool $isActive): void
    {
        $user           = Auth::user();
        $userId         = $user->id;
        $globalTemplate = $this->expenseTemplateRepository->firstGlobal($id);

        if ($globalTemplate) {

            ExpenseTemplateRulesUtil::isSameGroup($globalTemplate, $templateGroupId);

            $this->expenseTemplateUserGroupRepository->save($user->getGroup()->id, $globalTemplate->id, $name, $price, $isActive);

            return;
        }

                    ExpenseTemplateGroupRulesUtil::isExists($templateGroupId, $userId);
                    ExpenseTemplateRulesUtil::isExists($id, $userId);

                    $this->expenseTemplateRepository->update(['id' => $id], [
                        'template_group_id' => $templateGroupId,
                        'name'              => $name,
                        'price'             => $price,
                        'is_active'         => $isActive,
                    ]);
    }

    public function delete(int $id): void
    {
        $user           = Auth::user();
        $userId         = $user->id;
        $globalTemplate = $this->expenseTemplateRepository->firstGlobal($id);

        if ($globalTemplate) {

            $this->expenseTemplateUserGroupRepository->delete([
                'group_id'              => $user->getGroup()->id,
                'expense_template_id'   => $globalTemplate->id,
            ]);

            return;
        }

                    ExpenseTemplateRulesUtil::isExists($id, $userId);

                    $this->expenseTemplateRepository->delete(['id' => $id]);
    }
}
