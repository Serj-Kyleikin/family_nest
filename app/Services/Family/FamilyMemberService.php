<?php

namespace App\Services\Family;

use App\Models\User;
use App\Services\{
    Auth\Repositories\UserRepository,
    Family\Repositories\FamilyUserRepository,
    Family\Util\MemberRulesUtil,
    User\Util\UserCheckUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class FamilyMemberService
{
    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly FamilyUserRepository   $familyUserRepository,
    ) {
    }

    public function list(): Collection
    {
        $group      = Auth::user()->getGroup();

        $members    = $this->userRepository->getWhere(
                        ['group_id' => $group->id], 
                        ['id', 'name', 'group_id']
                    );

                    return $this->withAlias($members);
    }

    public function get(int $userId): User
    {
        $group      = Auth::user()->getGroup();

        $member     = $this->userRepository->firstWhere(
                        ['id' => $userId, 'group_id'  => $group->id], 
                        ['id', 'name', 'group_id']
                    );
                    UserCheckUtil::isExists($member);

                    $member->load('userData');

                    return $this->withAlias(collect([$member]))
                                ->first();
    }

    public function update(int $userId, string|null $alias): User
    {
        $user       = Auth::user();
                    MemberRulesUtil::isAnotherUser($user->id, $userId);
                    $this->get($userId);

                    $this->familyUserRepository->updateOrCreateAlias($user->id, $userId, $alias);

                    return $this->get($userId);
    }

    public function delete(int $userId): void
    {
                    $this->get($userId);
                    $this->userRepository->update(['id' => $userId], ['group_id' => null]);
                    $this->familyUserRepository->deleteByUserIds([$userId]);
    }

    private function withAlias(Collection $members): Collection
    {
        $aliases    = $this->familyUserRepository->getAliasesByMemberIds(
                        intval(Auth::id()),
                        $members->pluck('id')->all()
                    );

                    return $members->each(function (User $member) use ($aliases): void {
                        $alias          = $aliases[$member->id] ?? null;

                        $member->setAttribute('alias', $alias);
                        $member->name   = $alias ?? $member->name;
                    });
    }
}
