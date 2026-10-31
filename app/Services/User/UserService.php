<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\{
    Auth\Repositories\UserRepository,
    Family\Repositories\FamilyGroupLinkRepository,
};
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        private readonly UserRepository            $userRepository,
        private readonly FamilyGroupLinkRepository $familyGroupLinkRepository,
    ) {
    }

    public function getCurrent(): User
    {
        $groupId    = Auth::user()->group_id;
        $user       = $this->userRepository->current(Auth::id());
        $linkToken  = null;

        if ($groupId) {
            $link       = $this->familyGroupLinkRepository->firstWhere(
                            ['group_id' => $groupId, 'is_active' => true]
                        );
            $linkToken  = $link?->token;
        }

        $user->link_token = $linkToken;

        return $user;
    }
}
