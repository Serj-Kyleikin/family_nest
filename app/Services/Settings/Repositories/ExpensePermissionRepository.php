<?php

namespace App\Services\Settings\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\DeleteContract;
use App\Models\Expenses\ExpensePermission;

class ExpensePermissionRepository extends AbstractRepository implements CreateContract, DeleteContract
{
    public function __construct(
        protected ExpensePermission $model
    ) {
    }
}
