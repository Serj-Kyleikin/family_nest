<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\{
    Auth\Repositories\UserRepository,
};
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getCurrent(): User
    {
        return $this->userRepository->current(Auth::id());
    }
}
