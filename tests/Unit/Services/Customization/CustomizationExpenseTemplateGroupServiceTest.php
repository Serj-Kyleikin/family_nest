<?php

namespace Tests\Unit\Services\Customization;

use App\Exceptions\HandledException;
use App\Models\{
    Customization\CustomizationExpenseTemplateGroup,
    Expenses\ExpenseTemplateGroup,
    User,
};
use App\Services\Customization\CustomizationExpenseTemplateGroupService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomizationExpenseTemplateGroupServiceTest extends TestCase
{
    public function test_positive_save_creates_customization(): void
    {
        $service    = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $customization = $service->save($group->id, 3);

        $this->assertDatabaseCount('customization_expense_template_groups', 1);
        $this->assertDatabaseHas('customization_expense_template_groups', [
            'id'                        => $customization->id,
            'user_id'                   => $user->id,
            'expense_template_group_id' => $group->id,
            'priority'                  => 3,
        ]);
    }

    public function test_positive_save_global_group(): void
    {
        $service        = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        Auth::login($user);

        $service->save($globalGroup->id, 2);

        $this->assertDatabaseHas('customization_expense_template_groups', [
            'user_id'                   => $user->id,
            'expense_template_group_id' => $globalGroup->id,
            'priority'                  => 2,
        ]);
    }

    public function test_positive_save_replaces_priority_without_duplicate(): void
    {
        $service    = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $group->id,
            'priority'                  => 5,
        ]);

        Auth::login($user);

        $service->save($group->id, 1);

        $this->assertDatabaseCount('customization_expense_template_groups', 1);
        $this->assertDatabaseHas('customization_expense_template_groups', [
            'user_id'                   => $user->id,
            'expense_template_group_id' => $group->id,
            'priority'                  => 1,
        ]);
    }

    public function test_positive_save_does_not_change_other_users(): void
    {
        $service        = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $otherUser      = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $otherUser->id,
            'expense_template_group_id' => $globalGroup->id,
            'priority'                  => 7,
        ]);

        Auth::login($user);

        $service->save($globalGroup->id, 1);

        $this->assertDatabaseCount('customization_expense_template_groups', 2);
        $this->assertDatabaseHas('customization_expense_template_groups', [
            'user_id'                   => $otherUser->id,
            'expense_template_group_id' => $globalGroup->id,
            'priority'                  => 7,
        ]);
    }

    public function test_negative_save_foreign_group(): void
    {
        $service        = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense template group is not found');

        $service->save($foreignGroup->id, 1);
    }

    public function test_negative_save_foreign_group_not_saved(): void
    {
        $service        = $this->app->make(CustomizationExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        Auth::login($user);

        try {
            $service->save($foreignGroup->id, 1);
        } catch (HandledException) {
        }

        $this->assertDatabaseCount('customization_expense_template_groups', 0);
    }
}
