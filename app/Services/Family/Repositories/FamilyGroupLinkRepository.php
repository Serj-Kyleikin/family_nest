<?php

namespace App\Services\Family\Repositories;

use App\Models\Family\FamilyGroupLink;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class FamilyGroupLinkRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected FamilyGroupLink $model
    )
    {
    }

    public function firstActiveByToken(string $token): FamilyGroupLink|null
    {
        return $this->model->query()
            ->where('token', $token)
            ->where('is_active', true)
            ->first();
    }
}
