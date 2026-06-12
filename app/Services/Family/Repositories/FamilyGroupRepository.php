<?php

namespace App\Services\Family\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\Family\FamilyGroup;

class FamilyGroupRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected FamilyGroup $model
    )
    {
    }
}