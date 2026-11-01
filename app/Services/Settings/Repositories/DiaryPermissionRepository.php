<?php

namespace App\Services\Settings\Repositories;

use App\Models\Diary\DiaryPermission;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\DeleteContract;

class DiaryPermissionRepository extends AbstractRepository implements CreateContract, DeleteContract
{
    public function __construct(
        protected DiaryPermission $model
    ) {
    }
}
