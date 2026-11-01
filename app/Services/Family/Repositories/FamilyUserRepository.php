<?php

namespace App\Services\Family\Repositories;

use App\Models\Family\FamilyUser;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;

class FamilyUserRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected FamilyUser $model
    ) {
    }

    public function getAliasesByMemberIds(int $userId, array $memberIds): array
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->whereIn('member_id', $memberIds)
            ->whereNotNull('alias')
            ->pluck('alias', 'member_id')
            ->all();
    }

    public function getAliasesByUserIds(int $memberId, array $userIds): array
    {
        return $this->model->query()
            ->where('member_id', $memberId)
            ->whereIn('user_id', $userIds)
            ->whereNotNull('alias')
            ->pluck('alias', 'user_id')
            ->all();
    }

    public function updateOrCreateAlias(int $userId, int $memberId, string|null $alias): void
    {
        $this->model
            ->query()
            ->updateOrCreate(
            ['user_id' => $userId, 'member_id' => $memberId],
            ['alias' => $alias]
        );
    }

    public function deleteByUserIds(array $userIds): void
    {
        $this->model->query()
            ->whereIn('user_id', $userIds)
            ->orWhereIn('member_id', $userIds)
            ->delete();
    }
}
