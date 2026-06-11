<?php

namespace App\Services\Auth\Repositories;

use App\Models\User;
use App\Services\Auth\DTO\UserDTO;
use App\Exceptions\HandledException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function signIn(string $email, string $password): string 
    {
        $user = User::query()
            ->where('email', mb_strtolower($email))
            ->first();

        throw_if(
            !$user || !Hash::check($password, $user->password), 
            HandledException::class,
            'Invalid credentials.',
            401
        );

        return $this->createToken($user);
    }

    public function signUp(UserDTO $userDTO): string
    {
        return DB::transaction(function () use ($userDTO) {

            $user = $this->createUser($userDTO);
            return  $this->createToken($user);
        });
    }

    public function createUser(UserDTO $userDTO): User
    {
        return User::query()->create([
            'name'      => $userDTO->getName(),
            'email'     => mb_strtolower($userDTO->getEmail()),
            'password'  => Hash::make($userDTO->getPassword()),
        ]);
    }

    public function createToken(User $user): string
    {
        $token = $user->createToken('family-nest-api')->plainTextToken;

        return $token;
    }
}