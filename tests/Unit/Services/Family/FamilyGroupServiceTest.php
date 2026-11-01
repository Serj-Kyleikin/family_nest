<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    User,
};
use App\Services\{
    Family\FamilyGroupService,
};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FamilyGroupServiceTest extends TestCase
{
    public function testCreatePositive(): void
    {
        $user = User::query()->create([
            'name'      => 'John Doe',
            'email'     => '[john.doe@example.com](mailto:john.doe@example.com)',
            'password'  => bcrypt('password123')
        ]);

        Auth::login($user);

        $this->app
            ->make(FamilyGroupService::class)
            ->create();

        $this->assertDatabaseCount('family_groups', 1);
        $this->assertDatabaseMissing('users', [
            'id'        => $user->id,
            'group_id'  => null,
        ]);
    }

    public function test_positive_create_returns_group(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $group = $service->create();

        $this->assertInstanceOf(FamilyGroup::class, $group);
        $this->assertNotNull($group->name);
        $this->assertDatabaseHas('family_groups', ['id' => $group->id, 'name' => $group->name]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'group_id' => $group->id]);
    }

    public function test_positive_create_creates_active_link(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $group = $service->create();

        $this->assertDatabaseCount('family_group_links', 1);
        $this->assertDatabaseHas('family_group_links', [
            'group_id'  => $group->id,
            'is_active' => true,
        ]);
        $this->assertEquals(
            40,
            strlen(FamilyGroupLink::query()->where('group_id', $group->id)->value('token'))
        );
    }

    public function test_negative_create_does_not_create_link_when_group_exists(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        try {
            $service->create();
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('family_group_links', 0);
    }

    public function testCreateGroupAlreadyExists(): void
    {
        $group = FamilyGroup::factory()->create();

        $user = User::factory()->create([
            'group_id' => $group->id,
        ]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group is already created');

        $this->app
            ->make(FamilyGroupService::class)
            ->create();

        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'group_id'  => $group->id,
        ]);

        $this->assertDatabaseCount('family_groups', 1);
    }

    public function test_positive_get_group(): void
    {
        $groupName      = 'Ivanovs';

        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create(['name' => $groupName]);
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $result = $service->get();

        $this->assertEquals($group->id, $result->id);
        $this->assertEquals($groupName, $result->name);
    }

    public function test_negative_get_group_user_without_group(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->get();
    }
}
