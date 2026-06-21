<?php

namespace App\Services\Expenses\Repositories;

use App\Models\Expenses\ExpenseTemplate;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class ExpenseTemplateRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ExpenseTemplate $model
    ) {
    }
}

