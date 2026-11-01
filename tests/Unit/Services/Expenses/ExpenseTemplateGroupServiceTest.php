<?php

namespace Tests\Unit\Services\Expenses;

use App\Exceptions\HandledException;
use App\Models\{
    Customization\CustomizationExpenseTemplateGroup,
    Expenses\ExpenseTemplate,
    Expenses\ExpenseTemplateGroup,
    Expenses\ExpenseTemplateUserGroup,
    Family\FamilyGroup,
    User,
};
use App\Services\Expenses\ExpenseTemplateGroupService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ExpenseTemplateGroupServiceTest extends TestCase
{
    public function test_positive_list_own_and_global(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $ownGroup       = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        ExpenseTemplateGroup::factory()->create();

        Auth::login($user);

        $items = $service->list();

        $this->assertCount(2, $items);
        $this->assertEqualsCanonicalizing(
            [$ownGroup->id, $globalGroup->id],
            $items->pluck('id')->all()
        );
    }

    public function test_positive_list_returns_only_categories(): void
    {
        $service    = $this->app->make(ExpenseTemplateGroupService::class);

        $user       = User::factory()->create();

        ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $item = $service->list()->first();

        $this->assertFalse($item->relationLoaded('templates'));
        $this->assertSame(['id', 'user_id', 'name', 'priority'], array_keys($item->getAttributes()));
    }

    public function test_positive_list_sorted_by_priority(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $thirdGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $thirdGroup->id,
            'priority'                  => 1,
        ]);
        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 2,
        ]);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$thirdGroup->id, $secondGroup->id, $firstGroup->id], $items->pluck('id')->all());
        $this->assertSame([1, 2, null], $items->pluck('priority')->all());
    }

    public function test_positive_list_without_customization_by_id(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $thirdGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$firstGroup->id, $secondGroup->id, $thirdGroup->id], $items->pluck('id')->all());
        $this->assertSame([null, null, null], $items->pluck('priority')->all());
    }

    public function test_positive_list_same_priority_by_id(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 1,
        ]);
        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $firstGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$firstGroup->id, $secondGroup->id], $items->pluck('id')->all());
    }

    public function test_positive_list_ignores_other_users_priority(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $otherUser      = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $otherUser->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->list();

        $this->assertSame([$firstGroup->id, $secondGroup->id], $items->pluck('id')->all());
        $this->assertSame([null, null], $items->pluck('priority')->all());
    }

    public function test_negative_list_priority_of_foreign_group_not_returned(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $foreignGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->list();

        $this->assertCount(0, $items);
    }

    public function test_negative_list_foreign_not_returned(): void
    {
        $service    = $this->app->make(ExpenseTemplateGroupService::class);

        $user       = User::factory()->create();

        ExpenseTemplateGroup::factory()->create();

        Auth::login($user);

        $items = $service->list();

        $this->assertCount(0, $items);
    }

    public function test_positive_list_with_templates_own_and_global(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $ownGroup       = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        $ownTemplate        = ExpenseTemplate::factory()->create(['template_group_id' => $ownGroup->id]);
        $globalTemplate     = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);
        ExpenseTemplate::factory()->create(['template_group_id' => $foreignGroup->id]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$ownGroup->id, $globalGroup->id], $items->pluck('id')->all());
        $this->assertSame([$ownTemplate->id], $items->firstWhere('id', $ownGroup->id)->templates->pluck('id')->all());
        $this->assertSame([$globalTemplate->id], $items->firstWhere('id', $globalGroup->id)->templates->pluck('id')->all());
    }

    public function test_positive_list_with_templates_only_group_templates(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        $firstTemplate  = ExpenseTemplate::factory()->create(['template_group_id' => $firstGroup->id]);
        $secondTemplate = ExpenseTemplate::factory()->create(['template_group_id' => $secondGroup->id]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$firstTemplate->id], $items->firstWhere('id', $firstGroup->id)->templates->pluck('id')->all());
        $this->assertSame([$secondTemplate->id], $items->firstWhere('id', $secondGroup->id)->templates->pluck('id')->all());
    }

    public function test_positive_list_with_templates_empty_group(): void
    {
        $service    = $this->app->make(ExpenseTemplateGroupService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertCount(1, $items);
        $this->assertCount(0, $items->firstWhere('id', $group->id)->templates);
    }

    public function test_positive_list_with_templates_includes_inactive(): void
    {
        $service    = $this->app->make(ExpenseTemplateGroupService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        $active     = ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'is_active' => true]);
        $inactive   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'is_active' => false]);

        Auth::login($user);

        $templates = $service->listWithTemplates()->first()->templates;

        $this->assertSame([$active->id, $inactive->id], $templates->pluck('id')->all());
        $this->assertSame(
            ['id', 'template_group_id', 'name', 'price', 'is_active', 'is_customized'],
            array_keys($templates->first()->getAttributes())
        );
    }

    public function test_positive_list_with_templates_sorted_by_priority(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $thirdGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $thirdGroup->id,
            'priority'                  => 1,
        ]);
        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 2,
        ]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$thirdGroup->id, $secondGroup->id, $firstGroup->id], $items->pluck('id')->all());
        $this->assertSame([1, 2, null], $items->pluck('priority')->all());
    }

    public function test_positive_list_with_templates_without_priority_by_id(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$firstGroup->id, $secondGroup->id], $items->pluck('id')->all());
        $this->assertSame([null, null], $items->pluck('priority')->all());
    }

    public function test_positive_list_with_templates_same_priority_by_id(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 1,
        ]);
        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $firstGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$firstGroup->id, $secondGroup->id], $items->pluck('id')->all());
    }

    public function test_positive_list_with_templates_ignores_other_users_priority(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $otherUser      = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $otherUser->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$firstGroup->id, $secondGroup->id], $items->pluck('id')->all());
        $this->assertSame([null, null], $items->pluck('priority')->all());
    }

    public function test_positive_list_with_templates_keeps_templates_when_sorted(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $firstGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $secondGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        $firstTemplate  = ExpenseTemplate::factory()->create(['template_group_id' => $firstGroup->id]);
        $secondTemplate = ExpenseTemplate::factory()->create(['template_group_id' => $secondGroup->id]);

        CustomizationExpenseTemplateGroup::factory()->create([
            'user_id'                   => $user->id,
            'expense_template_group_id' => $secondGroup->id,
            'priority'                  => 1,
        ]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertSame([$secondGroup->id, $firstGroup->id], $items->pluck('id')->all());
        $this->assertSame([$secondTemplate->id], $items->first()->templates->pluck('id')->all());
        $this->assertSame([$firstTemplate->id], $items->last()->templates->pluck('id')->all());
    }

    public function test_positive_list_with_templates_replaces_data_with_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $family         = FamilyGroup::factory()->create();
        $otherFamily    = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        $customTemplate = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk', 'price' => '100.00']);
        $plainTemplate  = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Bread', 'price' => '200.00']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $customTemplate->id,
            'name'                  => 'Our milk',
            'price'                 => '55.50',
            'is_active'             => false,
        ]);
        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $otherFamily->id,
            'expense_template_id'   => $plainTemplate->id,
            'name'                  => 'Other family bread',
            'price'                 => '1.00',
        ]);

        Auth::login($user);

        $templates  = $service->listWithTemplates()->first()->templates;
        $custom     = $templates->firstWhere('id', $customTemplate->id);
        $plain      = $templates->firstWhere('id', $plainTemplate->id);

        $this->assertSame('Our milk', $custom->name);
        $this->assertEquals(55.5, (float) $custom->price);
        $this->assertFalse($custom->is_active);
        $this->assertTrue($custom->is_customized);
        $this->assertSame('Bread', $plain->name);
        $this->assertEquals(200.0, (float) $plain->price);
        $this->assertFalse($plain->is_customized);
    }

    public function test_positive_list_with_templates_user_without_family_ignores_copies(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Family milk',
        ]);

        Auth::login($user);

        $item = $service->listWithTemplates()->first()->templates->first();

        $this->assertSame('Milk', $item->name);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_list_with_templates_price_is_always_present(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $ownGroup       = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        $ownTemplate    = ExpenseTemplate::factory()->create(['template_group_id' => $ownGroup->id, 'price' => '30.00']);
        $emptyTemplate  = ExpenseTemplate::factory()->create(['template_group_id' => $ownGroup->id, 'price' => null]);
        $globalTemplate = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'price' => null]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $own    = $items->firstWhere('id', $ownGroup->id)->templates;
        $global = $items->firstWhere('id', $globalGroup->id)->templates;

        $this->assertEquals(30.0, (float) $own->firstWhere('id', $ownTemplate->id)->price);
        $this->assertEquals(0.0, (float) $own->firstWhere('id', $emptyTemplate->id)->price);
        $this->assertEquals(0.0, (float) $global->firstWhere('id', $globalTemplate->id)->price);
        $this->assertNotNull($global->firstWhere('id', $globalTemplate->id)->price);
    }

    public function test_negative_list_with_templates_foreign_not_returned(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        ExpenseTemplate::factory()->create(['template_group_id' => $foreignGroup->id]);

        Auth::login($user);

        $items = $service->listWithTemplates();

        $this->assertCount(0, $items);
    }

    public function test_positive_get_global(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        Auth::login($user);

        $group = $service->get($globalGroup->id);

        $this->assertEquals($globalGroup->id, $group->id);
    }

    public function test_negative_get_foreign(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Not found');

        $service->get($foreignGroup->id);
    }

    public function test_negative_update_global(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null, 'name' => 'Original']);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense template group is not found');

        $service->update($globalGroup->id, 'Hacked');
    }

    public function test_negative_update_global_not_changed(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null, 'name' => 'Original']);

        Auth::login($user);

        try {
            $service->update($globalGroup->id, 'Hacked');
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expense_template_groups', [
            'id'    => $globalGroup->id,
            'name'  => 'Original',
        ]);
    }

    public function test_negative_delete_global(): void
    {
        $service        = $this->app->make(ExpenseTemplateGroupService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        Auth::login($user);

        try {
            $service->delete($globalGroup->id);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expense_template_groups', ['id' => $globalGroup->id]);
    }
}
