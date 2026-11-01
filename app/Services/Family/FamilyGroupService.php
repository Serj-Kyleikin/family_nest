<?php

namespace App\Services\Family;

use App\Models\Family\FamilyGroup;
use App\Services\{
    Auth\Repositories\UserRepository,
    Family\Repositories\FamilyGroupLinkRepository,
    Family\Repositories\FamilyGroupRepository,
    Family\Util\GroupRulesUtil,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FamilyGroupService
{
    public function __construct(
        private FamilyGroupRepository       $familyGroupRepository,
        private UserRepository              $userRepository,
        private FamilyGroupLinkRepository   $familyGroupLinkRepository,
    ) {}

    public function create(): FamilyGroup
    {
        $user       = Auth::user();
                    GroupRulesUtil::isGroupCreated($user->group_id);
        
        $group      = $this->familyGroupRepository->create(['name' => 'Семья']);
        $groupId    = $group->id;

                    $this->userRepository->update(
                        ['id' => $user->id], 
                        ['group_id' => $groupId]
                    );

                    $this->familyGroupLinkRepository->create([
                        'group_id'  => $groupId,
                        'token'     => Str::random(40),
                        'is_active' => true,
                    ]);

        return $group;
    }

    public function get(): FamilyGroup
    {
        return Auth::user()->getGroup();
    }
}
