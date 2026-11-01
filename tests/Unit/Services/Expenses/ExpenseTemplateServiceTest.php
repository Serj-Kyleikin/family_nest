<?php

namespace Tests\Unit\Services\Expenses;

use App\Exceptions\HandledException;
use App\Models\{
    Expenses\ExpenseTemplate,
    Expenses\ExpenseTemplateGroup,
    Expenses\ExpenseTemplateUserGroup,
    Family\FamilyGroup,
    User,
};
use App\Services\Expenses\ExpenseTemplateService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ExpenseTemplateServiceTest extends TestCase
{
    public function test_positive_list_by_own_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $ownGroup       = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $otherGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $ownGroup->id]);
        ExpenseTemplate::factory()->create(['template_group_id' => $otherGroup->id]);

        Auth::login($user);

        $items = $service->list($ownGroup->id);

        $this->assertSame([$template->id], $items->pluck('id')->all());
    }

    public function test_positive_list_by_global_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $items = $service->list($globalGroup->id);

        $this->assertSame([$template->id], $items->pluck('id')->all());
    }

    public function test_positive_list_returns_template_fields(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        ExpenseTemplate::factory()->create(['template_group_id' => $group->id]);

        Auth::login($user);

        $item = $service->list($group->id)->first();

        $this->assertSame(
            ['id', 'template_group_id', 'name', 'price', 'is_active', 'is_customized'],
            array_keys($item->getAttributes())
        );
    }

    public function test_positive_list_replaces_data_with_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        $customized     = ExpenseTemplate::factory()->create([
            'template_group_id' => $globalGroup->id,
            'name'              => 'Milk',
            'price'             => '100.00',
            'is_active'         => true,
        ]);
        $plain          = ExpenseTemplate::factory()->create([
            'template_group_id' => $globalGroup->id,
            'name'              => 'Bread',
            'price'             => '200.00',
        ]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $customized->id,
            'name'                  => 'Our milk',
            'price'                 => '55.50',
            'is_active'             => false,
        ]);

        Auth::login($user);

        $items          = $service->list($globalGroup->id);
        $customizedItem = $items->firstWhere('id', $customized->id);
        $plainItem      = $items->firstWhere('id', $plain->id);

        $this->assertSame('Our milk', $customizedItem->name);
        $this->assertEquals(55.5, (float) $customizedItem->price);
        $this->assertFalse($customizedItem->is_active);
        $this->assertTrue($customizedItem->is_customized);
        $this->assertSame('Bread', $plainItem->name);
        $this->assertEquals(200.0, (float) $plainItem->price);
        $this->assertFalse($plainItem->is_customized);
    }

    public function test_positive_list_family_copy_without_price_returns_zero(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'price' => '100.00']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'price'                 => null,
        ]);

        Auth::login($user);

        $item = $service->list($globalGroup->id)->first();

        $this->assertEquals(0.0, (float) $item->price);
        $this->assertTrue($item->is_customized);
    }

    public function test_positive_list_family_members_share_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $author         = User::factory()->create(['group_id' => $family->id]);
        $member         = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Family name',
        ]);

        Auth::login($member);

        $item = $service->list($globalGroup->id)->first();

        $this->assertSame('Family name', $item->name);
        $this->assertTrue($item->is_customized);
    }

    public function test_positive_list_ignores_copy_of_other_family(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $otherFamily    = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk', 'price' => '100.00']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $otherFamily->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Other family milk',
            'price'                 => '1.00',
        ]);

        Auth::login($user);

        $item = $service->list($globalGroup->id)->first();

        $this->assertSame('Milk', $item->name);
        $this->assertEquals(100.0, (float) $item->price);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_list_user_without_family_ignores_copies(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

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

        $item = $service->list($globalGroup->id)->first();

        $this->assertSame('Milk', $item->name);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_list_without_prices_returns_zero(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'price' => null]);

        Auth::login($user);

        $item = $service->list($globalGroup->id)->first();

        $this->assertNotNull($item->price);
        $this->assertEquals(0.0, (float) $item->price);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_list_own_group_keeps_own_price(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'price' => '30.00']);

        Auth::login($user);

        $item = $service->list($group->id)->first();

        $this->assertEquals(30.0, (float) $item->price);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_list_own_group_without_price_returns_zero(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);

        ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'price' => null]);

        Auth::login($user);

        $item = $service->list($group->id)->first();

        $this->assertEquals(0.0, (float) $item->price);
        $this->assertFalse($item->is_customized);
    }

    public function test_negative_list_by_foreign_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $foreignGroup   = ExpenseTemplateGroup::factory()->create();

        ExpenseTemplate::factory()->create(['template_group_id' => $foreignGroup->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense template group is not found');

        $service->list($foreignGroup->id);
    }

    public function test_positive_show_global_template(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $item = $service->show($template->id);

        $this->assertEquals($template->id, $item->id);
    }

    public function test_positive_show_returns_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk', 'price' => '100.00']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Our milk',
            'price'                 => '55.50',
        ]);

        Auth::login($user);

        $item = $service->show($template->id);

        $this->assertEquals($template->id, $item->id);
        $this->assertSame('Our milk', $item->name);
        $this->assertEquals(55.5, (float) $item->price);
        $this->assertTrue($item->is_customized);
    }

    public function test_positive_show_without_any_price_returns_zero(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'price' => null]);

        Auth::login($user);

        $item = $service->show($template->id);

        $this->assertNotNull($item->price);
        $this->assertEquals(0.0, (float) $item->price);
        $this->assertFalse($item->is_customized);
    }

    public function test_negative_show_foreign_template(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $template   = ExpenseTemplate::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense template is not found');

        $service->show($template->id);
    }

    public function test_negative_create_in_global_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense template group is not found');

        $service->create($globalGroup->id, 'Milk');
    }

    public function test_positive_update_global_template_saves_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create([
            'template_group_id' => $globalGroup->id,
            'name'              => 'Original',
            'price'             => '100.00',
            'is_active'         => true,
        ]);

        Auth::login($user);

        $service->update($template->id, $globalGroup->id, 'Our name', '55.50', false);

        $copy = ExpenseTemplateUserGroup::query()->first();

        $this->assertDatabaseCount('expense_templates_user_groups', 1);
        $this->assertEquals($family->id, $copy->group_id);
        $this->assertEquals($template->id, $copy->expense_template_id);
        $this->assertSame('Our name', $copy->name);
        $this->assertEquals(55.5, (float) $copy->price);
        $this->assertFalse($copy->is_active);

        $this->assertDatabaseHas('expense_templates', [
            'id'        => $template->id,
            'name'      => 'Original',
            'is_active' => true,
        ]);
        $this->assertEquals(100.0, (float) $template->fresh()->price);
    }

    public function test_positive_update_global_template_replaces_copy_without_duplicate(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Old name',
            'price'                 => '10.00',
        ]);

        Auth::login($user);

        $service->update($template->id, $globalGroup->id, 'New name', null, true);

        $copy = ExpenseTemplateUserGroup::query()->first();

        $this->assertDatabaseCount('expense_templates_user_groups', 1);
        $this->assertSame('New name', $copy->name);
        $this->assertNull($copy->price);
    }

    public function test_positive_update_global_template_does_not_affect_other_family(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $otherFamily    = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $otherUser      = User::factory()->create(['group_id' => $otherFamily->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Original']);

        Auth::login($user);

        $service->update($template->id, $globalGroup->id, 'Our name', '5.00', true);

        Auth::login($otherUser);

        $item = $service->list($globalGroup->id)->first();

        $this->assertSame('Original', $item->name);
        $this->assertFalse($item->is_customized);
    }

    public function test_negative_update_global_template_with_other_group_id(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $ownGroup       = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        try {
            $service->update($template->id, $ownGroup->id, 'Moved', '1.00', true);
        } catch (HandledException $exception) {
            $this->assertSame('Expense template group does not match', $exception->getMessage());
        }

        $this->assertDatabaseCount('expense_templates_user_groups', 0);
        $this->assertDatabaseHas('expense_templates', [
            'id'                => $template->id,
            'template_group_id' => $globalGroup->id,
        ]);
    }

    public function test_negative_update_global_template_without_family_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->update($template->id, $globalGroup->id, 'Name', '1.00', true);
    }

    public function test_positive_update_own_template(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'name' => 'Original']);

        Auth::login($user);

        $service->update($template->id, $group->id, 'Changed', '12.00', false);

        $this->assertDatabaseCount('expense_templates_user_groups', 0);
        $this->assertDatabaseHas('expense_templates', [
            'id'        => $template->id,
            'name'      => 'Changed',
            'is_active' => false,
        ]);
    }

    public function test_negative_update_foreign_template(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $ownGroup   = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['name' => 'Original']);

        Auth::login($user);

        try {
            $service->update($template->id, $ownGroup->id, 'Hacked', null, true);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expense_templates', [
            'id'    => $template->id,
            'name'  => 'Original',
        ]);
        $this->assertDatabaseCount('expense_templates_user_groups', 0);
    }

    public function test_positive_delete_global_template_removes_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Original']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
        ]);

        Auth::login($user);

        $service->delete($template->id);

        $item = $service->list($globalGroup->id)->first();

        $this->assertDatabaseCount('expense_templates_user_groups', 0);
        $this->assertDatabaseHas('expense_templates', ['id' => $template->id]);
        $this->assertSame('Original', $item->name);
        $this->assertFalse($item->is_customized);
    }

    public function test_positive_delete_global_template_keeps_other_family_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $otherFamily    = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
        ]);
        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $otherFamily->id,
            'expense_template_id'   => $template->id,
        ]);

        Auth::login($user);

        $service->delete($template->id);

        $this->assertDatabaseCount('expense_templates_user_groups', 1);
        $this->assertDatabaseHas('expense_templates_user_groups', [
            'group_id'              => $otherFamily->id,
            'expense_template_id'   => $template->id,
        ]);
    }

    public function test_positive_delete_global_template_without_copy(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $service->delete($template->id);

        $this->assertDatabaseHas('expense_templates', ['id' => $template->id]);
        $this->assertDatabaseCount('expense_templates_user_groups', 0);
    }

    public function test_negative_delete_global_template_without_family_group(): void
    {
        $service        = $this->app->make(ExpenseTemplateService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Group not found');

        $service->delete($template->id);
    }

    public function test_positive_delete_own_template(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id]);

        Auth::login($user);

        $service->delete($template->id);

        $this->assertDatabaseMissing('expense_templates', ['id' => $template->id]);
    }

    public function test_negative_delete_foreign_template(): void
    {
        $service    = $this->app->make(ExpenseTemplateService::class);

        $user       = User::factory()->create();
        $template   = ExpenseTemplate::factory()->create();

        Auth::login($user);

        try {
            $service->delete($template->id);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expense_templates', ['id' => $template->id]);
    }
}
