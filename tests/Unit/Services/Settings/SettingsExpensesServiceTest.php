<?php

namespace Tests\Unit\Services\Settings;

use App\Exceptions\HandledException;
use App\Models\{
    Expenses\ExpensePermission,
    Family\FamilyGroup,
    User,
};
use App\Services\Settings\SettingsExpensesService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SettingsExpensesServiceTest extends TestCase
{
    public function test_positive_get_lists_members_without_self(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $allowedMember  = User::factory()->create(['group_id' => $group->id]);
        $closedMember   = User::factory()->create(['group_id' => $group->id]);
        $outsider       = User::factory()->create();

        ExpensePermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $allowedMember->id]);
        ExpensePermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $outsider->id]);

        Auth::login($user);

        $members = $service->get();

        $this->assertCount(2, $members);
        $this->assertTrue($members->firstWhere('id', $allowedMember->id)['is_allowed']);
        $this->assertFalse($members->firstWhere('id', $closedMember->id)['is_allowed']);
        $this->assertNull($members->firstWhere('id', $user->id));
        $this->assertNull($members->firstWhere('id', $outsider->id));
    }

    public function test_positive_get_ignores_permissions_of_other_owners(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        ExpensePermission::factory()->create(['owner_id' => $member->id, 'viewer_id' => $user->id]);

        Auth::login($user);

        $members = $service->get();

        $this->assertFalse($members->firstWhere('id', $member->id)['is_allowed']);
    }

    public function test_negative_get_without_group(): void
    {
        $service    = $this->app->make(SettingsExpensesService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->get();
    }

    public function test_positive_update_replaces_viewers(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $removedMember  = User::factory()->create(['group_id' => $group->id]);
        $addedMember    = User::factory()->create(['group_id' => $group->id]);

        ExpensePermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $removedMember->id]);

        Auth::login($user);

        $members = $service->update([$addedMember->id]);

        $this->assertDatabaseCount('expense_permissions', 1);
        $this->assertDatabaseHas('expense_permissions', [
            'owner_id'  => $user->id,
            'viewer_id' => $addedMember->id,
        ]);
        $this->assertDatabaseMissing('expense_permissions', [
            'owner_id'  => $user->id,
            'viewer_id' => $removedMember->id,
        ]);
        $this->assertTrue($members->firstWhere('id', $addedMember->id)['is_allowed']);
        $this->assertFalse($members->firstWhere('id', $removedMember->id)['is_allowed']);
    }

    public function test_positive_update_empty_list_closes_access(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        ExpensePermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $member->id]);

        Auth::login($user);

        $service->update([]);

        $this->assertDatabaseCount('expense_permissions', 0);
    }

    public function test_positive_update_keeps_permissions_of_other_owners(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);

        ExpensePermission::factory()->create(['owner_id' => $member->id, 'viewer_id' => $user->id]);

        Auth::login($user);

        $service->update([]);

        $this->assertDatabaseHas('expense_permissions', [
            'owner_id'  => $member->id,
            'viewer_id' => $user->id,
        ]);
    }

    public function test_negative_update_user_from_other_group(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $foreignGroup   = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $foreignUser    = User::factory()->create(['group_id' => $foreignGroup->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->update([$foreignUser->id]);
    }

    public function test_negative_update_self(): void
    {
        $service    = $this->app->make(SettingsExpensesService::class);

        $group      = FamilyGroup::factory()->create();
        $user       = User::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('User not found');

        $service->update([$user->id]);
    }

    public function test_negative_update_foreign_user_not_changed(): void
    {
        $service        = $this->app->make(SettingsExpensesService::class);

        $group          = FamilyGroup::factory()->create();
        $foreignGroup   = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $group->id]);
        $member         = User::factory()->create(['group_id' => $group->id]);
        $foreignUser    = User::factory()->create(['group_id' => $foreignGroup->id]);

        ExpensePermission::factory()->create(['owner_id' => $user->id, 'viewer_id' => $member->id]);

        Auth::login($user);

        try {
            $service->update([$foreignUser->id]);
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('expense_permissions', 1);
        $this->assertDatabaseHas('expense_permissions', [
            'owner_id'  => $user->id,
            'viewer_id' => $member->id,
        ]);
    }
}
