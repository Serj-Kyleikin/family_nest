<?php

namespace App\Services\Auth;

use App\Services\{
    Auth\Repositories\UserRepository,
    Auth\DTO\UserDTO,
};

class AuthService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function signUp(UserDTO $userDTO): string
    {
        return $this->userRepository->signUp($userDTO);
    }

    public function signIn(string $email, string $password): string
    {
        return $this->userRepository->signIn(
            $email,
            $password
        );
    }
}