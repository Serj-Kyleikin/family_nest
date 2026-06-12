<?php

namespace App\Services\Auth\Repositories;

use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use App\Models\User;
use App\Services\Auth\DTO\UserDTO;
use App\Exceptions\HandledException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected User $model
    )
    {
    }

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

            $user = $this->create([
                'name'      => $userDTO->getName(),
                'email'     => mb_strtolower($userDTO->getEmail()),
                'password'  => Hash::make($userDTO->getPassword()),
            ]);

            return  $this->createToken($user);
        });
    }

    public function createToken(User $user): string
    {
        $token = $user->createToken('family-nest-api')->plainTextToken;

        return $token;
    }
}