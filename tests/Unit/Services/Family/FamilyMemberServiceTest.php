<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    User,
};
use App\Services\{
    Family\FamilyMemberService,
};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FamilyMemberServiceTest extends TestCase
{
    public function test_positive_list_members(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);
        User::factory()->create(['group_id' => $otherGroup->id]);
        User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        $members = $service->list();

        $this->assertCount(2, $members);
        $this->assertEqualsCanonicalizing(
            [$authUser->id, $member->id],
            $members->pluck('id')->toArray()
        );
    }

    public function test_negative_list_members_user_without_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->list();
    }

    public function test_positive_get_member(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $result = $service->get($member->id);

        $this->assertEquals($member->id, $result->id);
        $this->assertEquals($member->name, $result->name);
        $this->assertEquals($group->id, $result->group_id);
    }

    public function test_negative_get_member_from_other_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->get($outsider->id);
    }

    public function test_negative_get_member_not_exists(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $authUser       = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->get(999999);
    }

    public function test_positive_delete_member(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $service->delete($member->id);

        $this->assertDatabaseHas('users', ['id' => $member->id, 'group_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $authUser->id, 'group_id' => $group->id]);
        $this->assertDatabaseHas('family_groups', ['id' => $group->id]);
    }

    public function test_positive_delete_member_himself(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $service->delete($authUser->id);

        $this->assertDatabaseHas('users', ['id' => $authUser->id, 'group_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'group_id' => $group->id]);
    }

    public function test_negative_delete_member_from_other_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($authUser);

        try {
            $service->delete($outsider->id);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('users', [
            'id'        => $outsider->id,
            'group_id'  => $otherGroup->id,
        ]);
    }

    public function test_negative_delete_member_from_other_group_message(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->delete($outsider->id);
    }

    public function test_negative_delete_member_user_without_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $authUser       = User::factory()->create(['group_id' => null]);
        $member         = User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->delete($member->id);
    }
}
