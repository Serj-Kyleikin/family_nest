<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
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

    public function testGetMembersPositive(): void
    {
        $group = FamilyGroup::factory()->create();

        $user1 = User::factory()->create([
            'group_id' => $group->id,
        ]);
        $user2 = User::factory()->create([
            'group_id' => $group->id,
        ]);

        User::factory()->create();
        Auth::login($user1);

        $members = $this->app
            ->make(FamilyGroupService::class)
            ->getMembers();

        $this->assertCount(2, $members);

        $this->assertEqualsCanonicalizing(
            [$user1->id, $user2->id],
            $members->pluck('id')->toArray()
        );
    }

    public function testGetMembersGroupNotFound(): void
    {
        $user = User::factory()->create([
            'group_id' => null,
        ]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $this->app
            ->make(FamilyGroupService::class)
            ->getMembers();
    }

    public function testAddMemberPositive(): void
    {
        $group = FamilyGroup::factory()->create();

        $authUser = User::factory()->create([
            'group_id' => $group->id,
        ]);

        $newUser = User::factory()->create([
            'group_id' => null,
        ]);

        Auth::login($authUser);

        $this->app
            ->make(FamilyGroupService::class)
            ->addMember($newUser->id);

        $this->assertDatabaseHas('users', [
            'id'        => $newUser->id,
            'group_id'  => $group->id,
        ]);
    }


}
