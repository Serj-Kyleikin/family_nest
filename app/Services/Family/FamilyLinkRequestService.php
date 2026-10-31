<?php

namespace App\Services\Family;

use App\Models\Family\FamilyGroupLinkRequest;
use App\Services\{
    Family\Repositories\FamilyGroupLinkRepository,
    Family\Repositories\FamilyGroupLinkRequestRepository,
    Family\Util\GroupRulesUtil,
    Family\Util\LinksRulesUtil,
    Family\Enums\FamilyGroupLinkRequestStatusEnum,
    Auth\Repositories\UserRepository,
    User\Util\UserCheckUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class FamilyLinkRequestService
{
    public function __construct(
        private readonly FamilyGroupLinkRepository          $familyGroupLinkRepository,
        private readonly FamilyGroupLinkRequestRepository   $familyGroupLinkRequestRepository,
        private readonly UserRepository                     $userRepository,
    ) {
    }

    public function getPendingRequests(): Collection
    {
        $group      = Auth::user()->getGroup();

                    return $this->familyGroupLinkRequestRepository
                                ->getPendingByGroupId($group->id);
    }

    public function createRequestToJoinGroup(string $token): FamilyGroupLinkRequest
    {
        $user       = Auth::user();
                    GroupRulesUtil::isOnlyOneGroup($user->group_id);

        $link       = $this->familyGroupLinkRepository
                           ->firstActiveByToken($token);
                    LinksRulesUtil::isLinkExists($link);

        $request    = $this->familyGroupLinkRequestRepository
                           ->firstPendingByUserId($user->id);
                    LinksRulesUtil::isRequestAlreadyCreated($request);

                    return $this->familyGroupLinkRequestRepository->create([
                        'link_id'   => $link->id,
                        'user_id'   => $user->id,
                    ]);
    }

    public function addByUserId(int $userId): void
    {
        $group      = Auth::user()->getGroup();
        $user       = $this->userRepository->firstWhere(
                        ['id' => $userId],
                        ['id', 'name', 'group_id']
                    );
                    UserCheckUtil::isExists($user);
                    GroupRulesUtil::isOnlyOneGroup($user->group_id);

        $request    = $this->familyGroupLinkRequestRepository
                           ->firstPendingByGroupIdAndUserId($group->id, $userId);
                    LinksRulesUtil::isRequestNotExists($request);

                    $this->userRepository->update(
                        ['id' => $userId],
                        ['group_id' => $group->id]
                    );
                    $this->familyGroupLinkRequestRepository->update(
                        ['id' => $request->id],
                        ['status' => FamilyGroupLinkRequestStatusEnum::DONE->value]
                    );
    }
}
