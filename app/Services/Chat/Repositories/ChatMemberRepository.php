<?php

namespace App\Services\Chat\Repositories;

use App\Models\Chat\ChatMembers;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class ChatMemberRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected ChatMembers $model
    )
    {
    }
}
