<?php

namespace Tests\Unit\Services\User;

use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    Family\FamilyGroupLinkRequest,
    User,
};
use App\Services\{
    Family\Enums\FamilyGroupLinkRequestStatusEnum,
    User\UserService,
};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    public function test_positive_get_current_with_group(): void
    {
        $service        = $this->app->make(UserService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $result = $service->getCurrent();

        $this->assertEquals($user->id, $result->id);
        $this->assertTrue($result->relationLoaded('group'));
        $this->assertEquals($group->id, $result->group->id);
        $this->assertFalse($result->is_pending_request);
    }

    public function test_positive_get_current_with_pending_request(): void
    {
        $service        = $this->app->make(UserService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create(['link_id' => $link->id, 'user_id' => $user->id]);

        Auth::login($user);

        $result = $service->getCurrent();

        $this->assertNull($result->group);
        $this->assertTrue($result->is_pending_request);
    }

    public function test_positive_get_current_without_group_and_pending_request(): void
    {
        $service        = $this->app->make(UserService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $result = $service->getCurrent();

        $this->assertNull($result->group);
        $this->assertFalse($result->is_pending_request);
    }

    public function test_positive_get_current_ignores_done_request(): void
    {
        $service        = $this->app->make(UserService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create([
            'link_id'   => $link->id,
            'user_id'   => $user->id,
            'status'    => FamilyGroupLinkRequestStatusEnum::DONE->value,
        ]);

        Auth::login($user);

        $result = $service->getCurrent();

        $this->assertFalse($result->is_pending_request);
    }

    public function test_positive_get_current_ignores_other_users_requests(): void
    {
        $service        = $this->app->make(UserService::class);

        $link           = FamilyGroupLink::factory()->create();
        $user           = User::factory()->create(['group_id' => null]);
        $otherUser      = User::factory()->create(['group_id' => null]);

        FamilyGroupLinkRequest::factory()->create(['link_id' => $link->id, 'user_id' => $otherUser->id]);

        Auth::login($user);

        $result = $service->getCurrent();

        $this->assertFalse($result->is_pending_request);
    }

    public function test_positive_get_current_serialization(): void
    {
        $service        = $this->app->make(UserService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $data = $service->getCurrent()->toArray();

        $this->assertArrayHasKey('group', $data);
        $this->assertArrayHasKey('is_pending_request', $data);
        $this->assertFalse($data['is_pending_request']);
        $this->assertEquals($group->id, $data['group']['id']);
        $this->assertArrayNotHasKey('join_request', $data);
        $this->assertArrayNotHasKey('joinRequest', $data);
        $this->assertArrayNotHasKey('password', $data);
    }
}
