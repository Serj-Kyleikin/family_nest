<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    Family\FamilyGroupLinkRequest,
    User,
};
use App\Services\{
    Family\Enums\FamilyGroupLinkRequestStatusEnum,
    Family\FamilyLinkRequestService,
};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FamilyLinkRequestServiceTest extends TestCase
{
    public function test_positive_create_request_to_join_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $request = $service->createRequestToJoinGroup($link->token);

        $this->assertInstanceOf(FamilyGroupLinkRequest::class, $request);
        $this->assertEquals($link->id, $request->link_id);
        $this->assertEquals($user->id, $request->user_id);
        $this->assertEquals(FamilyGroupLinkRequestStatusEnum::PENDING, $request->status);
        $this->assertDatabaseHas('family_group_link_requests', ['id' => $request->id, 'user_id' => $user->id, 'link_id' => $link->id]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'group_id' => null]);
    }

    public function test_negative_create_request_to_join_group_user_in_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();
        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $user           = User::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User is already in a group');

        $service->createRequestToJoinGroup($link->token);
    }

    public function test_negative_create_request_to_join_group_link_not_found(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Link not found');

        $service->createRequestToJoinGroup('absentToken');
    }

    public function test_negative_create_request_to_join_group_inactive_link(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $link           = FamilyGroupLink::factory()->create(['is_active' => false]);
        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Link not found');

        $service->createRequestToJoinGroup($link->token);
    }

    public function test_negative_create_request_to_join_group_already_created(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $service->createRequestToJoinGroup($link->token);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Request is already created');

        $service->createRequestToJoinGroup($link->token);
    }

    public function test_negative_create_request_to_join_group_already_created_by_previous_link(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $oldLink        = FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => false]);
        $newLink        = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $user           = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create(['link_id' => $oldLink->id, 'user_id' => $user->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Request is already created');

        $service->createRequestToJoinGroup($newLink->token);
    }

    public function test_negative_create_request_to_join_group_already_created_in_other_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();
        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $otherLink      = FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);
        $user           = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create(['link_id' => $otherLink->id, 'user_id' => $user->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Request is already created');

        $service->createRequestToJoinGroup($link->token);
    }

    public function test_negative_create_request_to_join_group_already_created_not_duplicated(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $service->createRequestToJoinGroup($link->token);

        try {
            $service->createRequestToJoinGroup($link->token);
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('family_group_link_requests', 1);
    }

    public function test_positive_create_request_to_join_group_after_done(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create([
            'link_id'   => $link->id,
            'user_id'   => $user->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);

        Auth::login($user);

        $request = $service->createRequestToJoinGroup($link->token);

        $this->assertEquals(FamilyGroupLinkRequestStatusEnum::PENDING, $request->status);
        $this->assertDatabaseCount('family_group_link_requests', 2);
    }

    public function test_positive_list_requests(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $firstUser      = User::factory()->create(['group_id' => null]);
        $secondUser     = User::factory()->create(['group_id' => null]);
        $doneUser       = User::factory()->create(['group_id' => null]);
        $outsider       = User::factory()->create(['group_id' => null]);

        $oldLink        = FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => false]);
        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $otherLink      = FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);

        $firstRequest   = FamilyGroupLinkRequest::factory()->create(['link_id' => $oldLink->id, 'user_id' => $firstUser->id]);
        $secondRequest  = FamilyGroupLinkRequest::factory()->create(['link_id' => $link->id, 'user_id' => $secondUser->id]);
        FamilyGroupLinkRequest::factory()->create([
            'link_id'   => $link->id,
            'user_id'   => $doneUser->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);
        FamilyGroupLinkRequest::factory()->create(['link_id' => $otherLink->id, 'user_id' => $outsider->id]);

        Auth::login($authUser);

        $requests = $service->getPendingRequests();

        $this->assertCount(2, $requests);
        $this->assertEquals([$secondRequest->id, $firstRequest->id], $requests->pluck('id')->all());
        $this->assertEquals($secondUser->id, $requests->first()->user->id);
        $this->assertEquals($secondUser->name, $requests->first()->user->name);
        $this->assertFalse($requests->first()->user->relationLoaded('group'));
    }

    public function test_negative_list_requests_user_without_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->getPendingRequests();
    }

    public function test_positive_add_member_by_invitation(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $invitation     = FamilyGroupLinkRequest::factory()->create(['link_id' => $link->id, 'user_id' => $newUser->id]);

        Auth::login($authUser);

        $service->addByUserId($newUser->id);

        $this->assertDatabaseHas('users', ['id' => $newUser->id, 'group_id' => $group->id]);
        $this->assertDatabaseHas('family_group_link_requests', [
            'id'        => $invitation->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);
    }

    public function test_positive_add_member_by_invitation_of_inactive_link(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        $oldLink        = FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => false]);
        FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $invitation     = FamilyGroupLinkRequest::factory()->create(['link_id' => $oldLink->id, 'user_id' => $newUser->id]);

        Auth::login($authUser);

        $service->addByUserId($newUser->id);

        $this->assertDatabaseHas('users', ['id' => $newUser->id, 'group_id' => $group->id]);
        $this->assertDatabaseHas('family_group_link_requests', [
            'id'        => $invitation->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);
    }

    public function test_negative_add_member_without_invitation(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Invitation not found');

        $service->addByUserId($newUser->id);
    }

    public function test_negative_add_member_without_invitation_not_changed(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        try {
            $service->addByUserId($newUser->id);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('users', ['id' => $newUser->id, 'group_id' => null]);
    }

    public function test_negative_add_member_invitation_to_other_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        $otherLink      = FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);
        FamilyGroupLinkRequest::factory()->create(['link_id' => $otherLink->id, 'user_id' => $newUser->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Invitation not found');

        $service->addByUserId($newUser->id);
    }

    public function test_negative_add_member_invitation_already_done(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $newUser        = User::factory()->create(['group_id' => null]);

        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        FamilyGroupLinkRequest::factory()->create([
            'link_id'   => $link->id,
            'user_id'   => $newUser->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Invitation not found');

        $service->addByUserId($newUser->id);
    }

    public function test_negative_add_member_already_in_other_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();

        $authUser       = User::factory()->create(['group_id' => $group->id]);
        $otherUser      = User::factory()->create(['group_id' => $otherGroup->id]);

        $link           = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        FamilyGroupLinkRequest::factory()->create(['link_id' => $link->id, 'user_id' => $otherUser->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User is already in a group');

        $service->addByUserId($otherUser->id);
    }

    public function test_negative_add_member_user_not_found(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $group          = FamilyGroup::factory()->create();
        $authUser       = User::factory()->create(['group_id' => $group->id]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->addByUserId(999999);
    }

    public function test_negative_add_member_auth_user_without_group(): void
    {
        $service        = $this->app->make(FamilyLinkRequestService::class);

        $authUser       = User::factory()->create(['group_id' => null]);
        $newUser        = User::factory()->create(['group_id' => null]);

        Auth::login($authUser);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->addByUserId($newUser->id);
    }
}
