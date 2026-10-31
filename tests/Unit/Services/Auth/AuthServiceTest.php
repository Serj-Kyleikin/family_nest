<?php

namespace Tests\Unit\Services\Auth;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    User,
};
use App\Services\{
    Auth\AuthService,
    Auth\DTO\UserDTO,
};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AuthServiceTest extends TestCase
{
    public function testSignUpPositive(): void
    {
        Role::create([
            'name' => 'client',
            'guard_name' => 'web',
        ]);

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
        $this->assertDatabaseCount('model_has_roles', 1);
    }

    public function test_positive_sign_up_with_join_token_adds_user_to_group(): void
    {
        Role::create(['name' => 'client', 'guard_name' => 'web']);

        $service        = $this->app->make(AuthService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();
        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => true]);

        FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id, 'is_active' => true]);

        $dto            = new UserDTO();

        $dto->setName('John Doe');
        $dto->setEmail('john.doe@example.com');
        $dto->setPassword('password123');
        $dto->setJoinToken($link->token);

        $service->signUp($dto);

        $this->assertDatabaseHas('users', [
            'email'     => 'john.doe@example.com',
            'group_id'  => $group->id,
        ]);
        $this->assertDatabaseCount('family_group_link_requests', 0);
    }

    public function test_positive_sign_up_without_join_token_has_no_group(): void
    {
        Role::create(['name' => 'client', 'guard_name' => 'web']);

        $service        = $this->app->make(AuthService::class);

        FamilyGroupLink::factory()->create(['is_active' => true]);

        $dto            = new UserDTO();

        $dto->setName('John Doe');
        $dto->setEmail('john.doe@example.com');
        $dto->setPassword('password123');

        $service->signUp($dto);

        $this->assertDatabaseHas('users', [
            'email'     => 'john.doe@example.com',
            'group_id'  => null,
        ]);
    }

    public function test_positive_sign_up_with_inactive_join_token_ignores_token(): void
    {
        Role::create(['name' => 'client', 'guard_name' => 'web']);

        $service        = $this->app->make(AuthService::class);

        $link           = FamilyGroupLink::factory()->create(['is_active' => false]);

        $dto            = new UserDTO();

        $dto->setName('John Doe');
        $dto->setEmail('john.doe@example.com');
        $dto->setPassword('password123');
        $dto->setJoinToken($link->token);

        $service->signUp($dto);

        $this->assertDatabaseHas('users', [
            'email'     => 'john.doe@example.com',
            'group_id'  => null,
        ]);
    }

    public function test_positive_sign_up_with_unknown_join_token_ignores_token(): void
    {
        Role::create(['name' => 'client', 'guard_name' => 'web']);

        $service        = $this->app->make(AuthService::class);

        $dto            = new UserDTO();

        $dto->setName('John Doe');
        $dto->setEmail('john.doe@example.com');
        $dto->setPassword('password123');
        $dto->setJoinToken('unknown-token');

        $service->signUp($dto);

        $this->assertDatabaseHas('users', [
            'email'     => 'john.doe@example.com',
            'group_id'  => null,
        ]);
    }

    public function testSignInPositive(): void
    {
        User::factory()->create([
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
        User::factory()->create([
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