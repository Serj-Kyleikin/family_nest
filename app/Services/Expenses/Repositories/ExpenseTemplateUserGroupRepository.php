<?php

namespace App\Services\Expenses\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\DeleteContract;
use App\Models\Expenses\ExpenseTemplateUserGroup;

class ExpenseTemplateUserGroupRepository extends AbstractRepository implements CreateContract, DeleteContract
{
    public function __construct(
        protected ExpenseTemplateUserGroup $model
    ) {
    }

    public function save(int $groupId, int $expenseTemplateId, string $name, ?string $price, bool $isActive): ExpenseTemplateUserGroup
    {
        return $this->model->updateOrCreate(
            [
                'group_id'              => $groupId,
                'expense_template_id'   => $expenseTemplateId,
            ],
            [
                'name'                  => $name,
                'price'                 => $price,
                'is_active'             => $isActive,
            ]
        );
    }
}
