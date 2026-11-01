<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    Family\FamilyUser,
    User,
    UserData,
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

    public function test_positive_list_members_with_alias(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id, 'name' => 'Vladimir']);
        $otherMember    = User::factory()->create(['group_id' => $group->id, 'name' => 'Olga']);

        FamilyUser::factory()->create(['user_id' => $authUser->id, 'member_id' => $member->id, 'alias' => 'Son']);
        FamilyUser::factory()->create(['user_id' => $otherMember->id, 'member_id' => $member->id, 'alias' => 'Brother']);

        Auth::login($authUser);

        $members = $service->list()->keyBy('id');

        $this->assertEquals('Son', $members[$member->id]->name);
        $this->assertEquals('Son', $members[$member->id]->alias);
        $this->assertEquals('Olga', $members[$otherMember->id]->name);
        $this->assertNull($members[$otherMember->id]->alias);
    }

    public function test_positive_get_member_with_alias_and_user_data(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id, 'name' => 'Vladimir']);

        FamilyUser::factory()->create(['user_id' => $authUser->id, 'member_id' => $member->id, 'alias' => 'Son']);
        UserData::factory()->create(['user_id' => $member->id, 'family_status' => 'Son']);

        Auth::login($authUser);

        $result = $service->get($member->id);

        $this->assertEquals('Son', $result->name);
        $this->assertEquals('Son', $result->userData->family_status);
    }

    public function test_positive_update_member_alias(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id, 'name' => 'Vladimir']);

        Auth::login($authUser);

        $result = $service->update($member->id, 'Son');

        $this->assertEquals('Son', $result->name);
        $this->assertEquals('Son', $result->alias);
        $this->assertDatabaseHas('family_users', [
            'user_id'   => $authUser->id,
            'member_id' => $member->id,
            'alias'     => 'Son',
        ]);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'name' => 'Vladimir']);
    }

    public function test_positive_update_member_alias_twice_keeps_one_row(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $service->update($member->id, 'Son');
        $service->update($member->id, 'Junior');

        $this->assertDatabaseCount('family_users', 1);
        $this->assertDatabaseHas('family_users', [
            'user_id'   => $authUser->id,
            'member_id' => $member->id,
            'alias'     => 'Junior',
        ]);
    }

    public function test_positive_update_member_reset_alias(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id, 'name' => 'Vladimir']);

        FamilyUser::factory()->create(['user_id' => $authUser->id, 'member_id' => $member->id, 'alias' => 'Son']);

        Auth::login($authUser);

        $result = $service->update($member->id, null);

        $this->assertEquals('Vladimir', $result->name);
        $this->assertNull($result->alias);
    }

    public function test_positive_update_member_alias_is_personal(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $otherUser      = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id, 'name' => 'Vladimir']);

        Auth::login($authUser);
        $service->update($member->id, 'Son');

        Auth::login($otherUser);
        $result = $service->get($member->id);

        $this->assertEquals('Vladimir', $result->name);
        $this->assertNull($result->alias);
    }

    public function test_negative_update_himself(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $authUser       = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Cannot rename yourself');

        $service->update($authUser->id, 'Me');
    }

    public function test_negative_update_member_from_other_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($authUser);

        try {
            $service->update($outsider->id, 'Stranger');
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('family_users', 0);
    }

    public function test_negative_update_member_user_without_group(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $authUser       = User::factory()->create(['group_id' => null]);
        $member         = User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->update($member->id, 'Son');
    }

    public function test_positive_delete_member_removes_aliases(): void
    {
        $service        = $this->app->make(FamilyMemberService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);
        $thirdMember    = User::factory()->create(['group_id' => $group->id]);

        FamilyUser::factory()->create(['user_id' => $authUser->id, 'member_id' => $member->id]);
        FamilyUser::factory()->create(['user_id' => $member->id, 'member_id' => $authUser->id]);
        FamilyUser::factory()->create(['user_id' => $authUser->id, 'member_id' => $thirdMember->id]);

        Auth::login($authUser);

        $service->delete($member->id);

        $this->assertDatabaseCount('family_users', 1);
        $this->assertDatabaseHas('family_users', [
            'user_id'   => $authUser->id,
            'member_id' => $thirdMember->id,
        ]);
    }
}
