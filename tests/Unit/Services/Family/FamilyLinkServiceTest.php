<?php

namespace Tests\Unit\Services\Family;

use App\Exceptions\HandledException;
use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
    User,
};
use App\Services\{
    Family\FamilyLinkService,
};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FamilyLinkServiceTest extends TestCase
{
    public function test_positive_create_link(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $link = $service->create();

        $this->assertInstanceOf(FamilyGroupLink::class, $link);
        $this->assertEquals($group->id, $link->group_id);
        $this->assertEquals(40, strlen($link->token));
        $this->assertTrue($link->is_active);
        $this->assertDatabaseHas('family_group_links', ['id' => $link->id, 'token' => $link->token, 'is_active' => true]);
    }

    public function test_positive_create_link_deactivates_previous(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        $previousLink   = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        $otherLink      = FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($user);

        $link = $service->create();

        $this->assertNotEquals($previousLink->token, $link->token);
        $this->assertDatabaseHas('family_group_links', ['id' => $previousLink->id, 'is_active' => false]);
        $this->assertDatabaseHas('family_group_links', ['id' => $link->id, 'is_active' => true]);
        $this->assertDatabaseHas('family_group_links', ['id' => $otherLink->id, 'is_active' => true]);
    }

    public function test_negative_create_link_user_without_group(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->create();
    }

    public function test_positive_get_link(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $group          = FamilyGroup::factory()->create();
        $otherGroup     = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => false]);
        $activeLink     = FamilyGroupLink::factory()->create(['group_id' => $group->id]);
        FamilyGroupLink::factory()->create(['group_id' => $otherGroup->id]);

        Auth::login($user);

        $link = $service->get();

        $this->assertEquals($activeLink->id, $link->id);
        $this->assertEquals($activeLink->token, $link->token);
    }

    public function test_negative_get_link_not_found(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);

        FamilyGroupLink::factory()->create(['group_id' => $group->id, 'is_active' => false]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Link not found');

        $service->get();
    }

    public function test_negative_get_link_user_without_group(): void
    {
        $service        = $this->app->make(FamilyLinkService::class);

        $user           = User::factory()->create(['group_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->get();
    }
}
