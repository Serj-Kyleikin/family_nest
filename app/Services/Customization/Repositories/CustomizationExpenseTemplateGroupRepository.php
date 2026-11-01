<?php

namespace App\Services\Customization\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Customization\CustomizationExpenseTemplateGroup;

class CustomizationExpenseTemplateGroupRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected CustomizationExpenseTemplateGroup $model
    ) {
    }

    public function save(int $userId, int $expenseTemplateGroupId, int $priority): CustomizationExpenseTemplateGroup
    {
        return $this->model->updateOrCreate(
            [
                'user_id'                   => $userId,
                'expense_template_group_id' => $expenseTemplateGroupId,
            ],
            [
                'priority'                  => $priority,
            ]
        );
    }
}
