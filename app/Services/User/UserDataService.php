<?php

namespace App\Services\User;

use App\Models\UserData;
use App\Services\User\Repositories\UserDataRepository;
use Illuminate\Support\Facades\Auth;

class UserDataService
{
    public function __construct(
        private readonly UserDataRepository $userDataRepository,
    ) {
    }

    public function get(): UserData
    {
        return $this->userDataRepository->firstOrNewByUserId(intval(Auth::id()));
    }

    public function update(array $data): UserData
    {
        $this->userDataRepository->updateOrCreateByUserId(intval(Auth::id()), $data);

        return $this->get();
    }
}
