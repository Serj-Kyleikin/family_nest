<?php

namespace Tests\Unit\Services\Auth;

use App\Exceptions\HandledException;
use App\Models\User;
use App\Services\{
    Auth\AuthService,
    Auth\DTO\UserDTO,
};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    public function testPositive(): void
    {
        $name = 'John Doe';
        $email = 'john.doe@example.com';
        $password = 'password123';

        $dto = new UserDTO();

        $dto->setName($name);
        $dto->setEmail($email);
        $dto->setPassword($password);

        $result = $this->app->make(AuthService::class)->signUp($dto);

        $this->assertIsString($result);
        $this->assertDatabaseHas('users', [
            'name'  => $name,
            'email' => $email,
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function testSignInPositive(): void
    {
        User::query()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->app
            ->make(AuthService::class)
            ->signIn(
                'john.doe@example.com',
                'password123'
            );

        $this->assertIsString($result);
        $this->assertDatabaseCount(
            'personal_access_tokens',
            1
        );
    }

    public function testSignInWrongPassword(): void
    {
        User::query()->create([
            'name'      => 'John Doe',
            'email'     => 'john.doe@example.com',
            'password'  => Hash::make('password123'),
        ]);

        $this->expectException(HandledException::class);

        $this->app
            ->make(AuthService::class)
            ->signIn(
                'john.doe@example.com',
                'wrong-password'
            );
    }

    public function testSignInUserNotFound(): void
    {
        $this->expectException(HandledException::class);

        $this->app
            ->make(AuthService::class)
            ->signIn(
                'john.doe@example.com',
                'password123'
            );
    }
}