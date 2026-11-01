<?php

namespace App\Services\Family;

use App\Models\Family\FamilyGroup;
use App\Services\{
    Auth\Repositories\UserRepository,
    Chat\Handler\DeleteUsersChatsHandler,
    Family\Repositories\FamilyGroupLinkRepository,
    Family\Repositories\FamilyGroupLinkRequestRepository,
    Family\Repositories\FamilyGroupRepository,
    Family\Repositories\FamilyUserRepository,
    Family\Util\GroupRulesUtil,
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FamilyGroupService
{
    public function __construct(
        private FamilyGroupRepository               $familyGroupRepository,
        private UserRepository                      $userRepository,
        private DeleteUsersChatsHandler             $deleteUsersChatsHandler,
        private FamilyGroupLinkRepository           $familyGroupLinkRepository,
        private FamilyGroupLinkRequestRepository    $familyGroupLinkRequestRepository,
        private FamilyUserRepository                $familyUserRepository,
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

    public function update(string $name): FamilyGroup
    {
        $group      = Auth::user()->getGroup();

                    $this->familyGroupRepository->update(
                        ['id' => $group->id], 
                        ['name' => $name]
                    );

        return $group->refresh();
    }

    public function delete(): void
    {
        $group      = Auth::user()->getGroup();
        $userIds    = $this->userRepository
                        ->getWhere(['group_id' => $group->id], ['id'])
                        ->pluck('id')
                        ->all();

                    $this->deleteUsersChatsHandler->handle($userIds);
                    $this->userRepository->update(
                        ['group_id' => $group->id],
                        ['group_id' => null]
                    );
                    $this->familyUserRepository->deleteByUserIds($userIds);
                    $this->familyGroupLinkRequestRepository->deleteByGroupId($group->id);
                    $this->familyGroupLinkRepository->delete(['group_id' => $group->id]);
                    $this->familyGroupRepository->delete(['id' => $group->id]);
    }
}
