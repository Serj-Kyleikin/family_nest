<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Chat\Chat,
    Chat\ChatDiscussion,
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    Family\FamilyGroupLinkRequest,
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

    public function test_positive_update_group(): void
    {
        $newName        = 'Petrovs';

        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create(['name' => 'Ivanovs']);
        $otherGroup     = FamilyGroup::factory()->create(['name' => 'Sidorovs']);
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $service->update($newName);

        $this->assertDatabaseHas('family_groups', [
            'id'    => $group->id,
            'name'  => $newName,
        ]);
        $this->assertDatabaseHas('family_groups', [
            'id'    => $otherGroup->id,
            'name'  => 'Sidorovs',
        ]);
    }

    public function test_positive_update_group_returns_updated_group(): void
    {
        $newName        = 'Petrovs';

        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create(['name' => 'Ivanovs']);
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $result = $service->update($newName);

        $this->assertInstanceOf(FamilyGroup::class, $result);
        $this->assertEquals($group->id, $result->id);
        $this->assertEquals($newName, $result->name);
    }

    public function test_negative_update_group_user_without_group(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->update('Petrovs');
    }

    public function test_positive_delete_group_detaches_members(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($authUser);

        $service->delete();

        $this->assertDatabaseMissing('family_groups', ['id' => $group->id]);
        $this->assertDatabaseHas('family_groups', ['id' => $otherGroup->id]);
        $this->assertDatabaseHas('users', ['id' => $authUser->id, 'group_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $member->id, 'group_id' => null]);
        $this->assertDatabaseHas('users', ['id' => $outsider->id, 'group_id' => $otherGroup->id]);
    }

    public function test_positive_delete_group_deletes_chats_between_members(): void
    {
        $service            = $this->app->make(FamilyGroupService::class);

        $group              = FamilyGroup::factory()->create();
        $otherGroup         = FamilyGroup::factory()->create();

        $authUser           = User::factory()->create(['group_id' => $group->id]);
        $member             = User::factory()->create(['group_id' => $group->id]);
        $outsider           = User::factory()->create(['group_id' => $otherGroup->id]);
        $otherOutsider      = User::factory()->create(['group_id' => $otherGroup->id]);

        $groupChat          = Chat::factory()
            ->withMembers($authUser, $member)
            ->create();
        $mixedChat          = Chat::factory()
            ->withMembers($authUser, $outsider)
            ->create();
        $outsidersChat      = Chat::factory()
            ->withMembers($outsider, $otherOutsider)
            ->create();

        $groupDiscussion    = ChatDiscussion::factory()->create(['chat_id' => $groupChat->id, 'user_id' => $authUser->id]);
        $mixedDiscussion    = ChatDiscussion::factory()->create(['chat_id' => $mixedChat->id, 'user_id' => $authUser->id]);

        Auth::login($authUser);

        $service->delete();

        $this->assertSoftDeleted('chats', ['id' => $groupChat->id]);
        $this->assertSoftDeleted('chats_discussions', ['id' => $groupDiscussion->id]);
        $this->assertDatabaseMissing('chats_members', ['chat_id' => $groupChat->id]);

        $this->assertDatabaseHas('chats', ['id' => $mixedChat->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('chats', ['id' => $outsidersChat->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('chats_discussions', ['id' => $mixedDiscussion->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('chats_members', ['chat_id' => $mixedChat->id, 'user_id' => $authUser->id]);
        $this->assertDatabaseHas('chats_members', ['chat_id' => $outsidersChat->id, 'user_id' => $outsider->id]);
    }

    public function test_positive_delete_group_deletes_links_and_requests(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $applicant      = User::factory()->create(['group_id' => null]);

        $groupLink      = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $otherLink      = FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);

        $groupRequest   = FamilyGroupLinkRequest::factory()->create(['link_id' => $groupLink->id, 'user_id' => $applicant->id]);
        $otherRequest   = FamilyGroupLinkRequest::factory()->create(['link_id' => $otherLink->id]);

        Auth::login($authUser);

        $service->delete();

        $this->assertDatabaseMissing('family_group_links', ['id' => $groupLink->id]);
        $this->assertDatabaseMissing('family_group_link_requests', ['id' => $groupRequest->id]);
        $this->assertDatabaseHas('family_group_links', ['id' => $otherLink->id]);
        $this->assertDatabaseHas('family_group_link_requests', ['id' => $otherRequest->id]);
    }

    public function test_negative_delete_group_user_without_group(): void
    {
        $service        = $this->app->make(FamilyGroupService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->delete();
    }
}
