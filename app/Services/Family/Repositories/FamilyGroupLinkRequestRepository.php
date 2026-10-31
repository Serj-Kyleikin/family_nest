<?php

namespace App\Services\Family\Repositories;

use App\Models\Family\FamilyGroupLinkRequest;
use App\Services\Family\Enums\FamilyGroupLinkRequestStatusEnum;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use Illuminate\Support\Collection;

class FamilyGroupLinkRequestRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected FamilyGroupLinkRequest $model
    )
    {
    }

    public function getPendingByGroupId(int $groupId): Collection
    {
        return $this->model->query()
            ->whereHas('link', fn ($query) => $query->where('group_id', $groupId))
            ->where('status', FamilyGroupLinkRequestStatusEnum::PENDING->value)
            ->with(['user' => fn ($query) => $query->select(['id', 'name'])->without('group')])
            ->orderByDesc('id')
            ->get();
    }

    public function firstPendingByGroupIdAndUserId(int $groupId, int $userId): FamilyGroupLinkRequest|null
    {
        return $this->model->query()
            ->whereHas('link', fn ($query) => $query->where('group_id', $groupId))
            ->where('user_id', $userId)
            ->where('status', FamilyGroupLinkRequestStatusEnum::PENDING->value)
            ->first();
    }

    public function firstPendingByUserId(int $userId): FamilyGroupLinkRequest|null
    {
        return $this->model->query()
            ->where('user_id', $userId)
            ->where('status', FamilyGroupLinkRequestStatusEnum::PENDING->value)
            ->first();
    }

    public function deleteByGroupId(int $groupId): void
    {
        $this->model->query()
            ->whereHas('link', fn ($query) => $query->where('group_id', $groupId))
            ->delete();
    }
}
