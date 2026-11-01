<?php

namespace App\Services\User\Repositories;

use App\Models\UserData;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class UserDataRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected UserData $model
    ) {
    }

    public function firstOrNewByUserId(int $userId): UserData
    {
        return $this->model
            ->query()
            ->firstOrNew(
            ['user_id' => $userId],
            [
                'status'        => null,
                'family_status' => null,
                'avatar'        => null,
                'birthday'      => null,
                'address'       => null,
                'phone'         => null,
            ]
        );
    }

    public function updateOrCreateByUserId(int $userId, array $data): void
    {
        $this->model
            ->query()
            ->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
}
