<?php

namespace App\Services\Auth;

use App\Services\{
    Auth\Repositories\UserRepository,
    Auth\DTO\UserDTO,
    Family\Repositories\FamilyGroupLinkRepository,
};

class AuthService
{
    public function __construct(
        private UserRepository              $userRepository,
        private FamilyGroupLinkRepository   $familyGroupLinkRepository,
    ) {}

    public function signUp(UserDTO $userDTO): string
    {
        $groupId    = null;
        $joinToken  = $userDTO->getJoinToken();

        if ($joinToken) {
            $link       = $this->familyGroupLinkRepository->firstActiveByToken($joinToken);
            $groupId    = $link?->group_id;
        }

        return $this->userRepository->signUp($userDTO, $groupId);
    }

    public function signIn(string $email, string $password): string
    {
        return $this->userRepository->signIn(
            $email,
            $password
        );
    }
}