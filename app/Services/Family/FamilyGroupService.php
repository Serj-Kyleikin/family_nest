<?php

namespace App\Services\Family;

use App\Services\{
    Auth\Repositories\UserRepository,
    Family\Repositories\FamilyGroupRepository,
};
use App\Exceptions\HandledException;
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class FamilyGroupService
{
    public function __construct(
        private FamilyGroupRepository   $familyGroupRepository,
        private UserRepository          $userRepository,
    ) {}

    public function create(): void
    {
        $user       = Auth::user();

        throw_unless(
            $user->group_id === null, 
            HandledException::class,
            'Group is already created',
            403
        );

        $group      = $this->familyGroupRepository->create(['name' => null]);
        $groupId    = $group->id;

        $this->userRepository->update(['id' => $user->id], ['group_id' => $groupId]);
    }

    public function getMembers(): Collection
    {
        $groupId = Auth::user()->group_id;

        throw_unless(
            $groupId, 
            HandledException::class,
            'Group not found',
            404
        );

        return $this->userRepository->getWhere([
            'group_id' => $groupId,
        ], ['id', 'name', 'group_id']);
    }

    public function addMember(int $userId): void
    {
        $authUser = Auth::user();

        throw_unless(
            $authUser->group_id,
            HandledException::class,
            'Group not found',
            404
        );

        $user = $this->userRepository->firstWhere([
            'id' => $userId,
        ], ['id', 'name']);

        throw_unless(
            $user,
            HandledException::class,
            'User not found',
            404
        );

        $this->userRepository->update([
            'id' => $userId,
        ], [
            'group_id' => $authUser->group_id,
        ]);
    }
}