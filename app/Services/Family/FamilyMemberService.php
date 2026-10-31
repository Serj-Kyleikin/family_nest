<?php

namespace App\Services\Family;

use App\Models\User;
use App\Services\{
    Auth\Repositories\UserRepository,
    User\Util\UserCheckUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class FamilyMemberService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function list(): Collection
    {
        $group      = Auth::user()->getGroup();

                    return $this->userRepository->getWhere(
                        ['group_id' => $group->id], 
                        ['id', 'name', 'group_id']
                    );
    }

    public function get(int $userId): User
    {
        $group      = Auth::user()->getGroup();

        $member     = $this->userRepository->firstWhere(
                        ['id' => $userId, 'group_id'  => $group->id], 
                        ['id', 'name', 'group_id']
                    );
                    UserCheckUtil::isExists($member);

                    return $member;
    }

    public function delete(int $userId): void
    {
                    $this->get($userId);
                    $this->userRepository->update(['id' => $userId], ['group_id' => null]);
    }
}
