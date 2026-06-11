<?php

namespace App\Services\Auth\DTO\Factory;

use App\Http\Requests\Api\Auth\SignUpRequest;
use App\Services\Auth\DTO\UserDTO;

final class SignUpDTOFactory
{
    public function fromRequest(SignUpRequest $request): UserDTO
    {
        $validated = $request->validated();

        $userDTO = new UserDTO;

        $userDTO->setName($validated['name']);
        $userDTO->setEmail(mb_strtolower($validated['email']));
        $userDTO->setPassword($validated['password']);

        return $userDTO;
    }
}