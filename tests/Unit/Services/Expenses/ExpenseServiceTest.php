<?php

namespace Tests\Unit\Services\Expenses;

use App\Exceptions\HandledException;
use App\Models\{
    Expenses\Expense,
    Expenses\ExpensePermission,
    Expenses\ExpenseTemplate,
    Expenses\ExpenseTemplateGroup,
    Expenses\ExpenseTemplateUserGroup,
    Family\FamilyGroup,
    User,
};
use App\Services\{
    Expenses\ExpenseService,
};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ExpenseServiceTest extends TestCase
{
    public function test_positive_create_by_name(): void
    {
        $name       = 'Taxi';
        $amount     = '120.50';

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $service->create(null, $name, $amount);

        $this->assertDatabaseCount('expenses', 1);
        $this->assertDatabaseHas('expenses', [
            'user_id'           => $user->id,
            'template_group_id' => null,
            'template_id'       => null,
            'name'              => $name,
        ]);
        $this->assertEquals((float) $amount, (float) Expense::query()->first()->amount);
    }

    public function test_positive_create_by_template(): void
    {
        $amount     = '75';

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id]);

        Auth::login($user);

        $service->create($template->id, null, $amount);

        $this->assertDatabaseHas('expenses', [
            'user_id'           => $user->id,
            'template_group_id' => $group->id,
            'template_id'       => $template->id,
            'name'              => $template->name,
        ]);
    }

    public function test_negative_create_foreign_template(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $template   = ExpenseTemplate::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Template not found');

        $service->create($template->id, null, '10');
    }

    public function test_negative_create_inactive_template(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id, 'is_active' => false]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Template not found');

        $service->create($template->id, null, '10');
    }

    public function test_positive_list_only_own(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id]);
        Expense::factory()->create();

        Auth::login($user);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($expense->id, $items->first()->id);
    }

    public function test_positive_list_orders_by_date_not_by_id(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $newest     = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-11-01 12:00:00']);
        $backdated  = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-01 12:00:00']);
        $middle     = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-15 12:00:00']);

        Auth::login($user);

        $items = $service->list(10);

        $this->assertSame([$newest->id, $middle->id, $backdated->id], $items->pluck('id')->all());
    }

    public function test_positive_list_last_id_follows_date_order(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-11-01 12:00:00']);
        $backdated  = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-01 12:00:00']);
        $middle     = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-15 12:00:00']);

        Auth::login($user);

        $items = $service->list(10, $middle->id);

        $this->assertSame([$backdated->id], $items->pluck('id')->all());
    }

    public function test_positive_update_own_expense(): void
    {
        $name       = 'Metro';
        $amount     = '60';

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $service->update($expense->id, null, $name, $amount);

        $this->assertDatabaseHas('expenses', [
            'id'            => $expense->id,
            'user_id'       => $user->id,
            'name'          => $name,
        ]);
        $this->assertEquals((float) $amount, (float) $expense->fresh()->amount);
    }

    public function test_positive_update_keeps_group_when_not_passed(): void
    {
        $groupId    = 5;

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id, 'template_group_id' => $groupId]);

        Auth::login($user);

        $service->update($expense->id, null, 'Metro', '60');

        $this->assertDatabaseHas('expenses', [
            'id'                => $expense->id,
            'template_group_id' => $groupId,
        ]);
    }

    public function test_negative_update_foreign_expense(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense is not found');

        $service->update($expense->id, null, 'Metro', '60');
    }

    public function test_negative_update_foreign_expense_not_changed(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['name' => 'Original']);

        Auth::login($user);

        try {
            $service->update($expense->id, null, 'Hacked', '1');
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expenses', [
            'id'    => $expense->id,
            'name'  => 'Original',
        ]);
    }

    public function test_positive_delete_own_expense(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $service->delete($expense->id);

        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
    }

    public function test_negative_delete_foreign_expense(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Expense is not found');

        $service->delete($expense->id);
    }

    public function test_positive_list_own_and_permitted(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $owner              = User::factory()->create(['group_id' => $group->id]);

        $ownExpense         = Expense::factory()->create(['user_id' => $viewer->id]);
        $permittedExpense   = Expense::factory()->create(['user_id' => $owner->id]);

        ExpensePermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertCount(2, $items);
        $this->assertEqualsCanonicalizing(
            [$ownExpense->id, $permittedExpense->id],
            $items->pluck('id')->all()
        );
        $this->assertEquals($owner->name, $items->firstWhere('id', $permittedExpense->id)->user_name);
    }

    public function test_negative_list_without_permission(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $group          = FamilyGroup::factory()->create();
        $viewer         = User::factory()->create(['group_id' => $group->id]);
        $owner          = User::factory()->create(['group_id' => $group->id]);

        $ownExpense     = Expense::factory()->create(['user_id' => $viewer->id]);
        Expense::factory()->create(['user_id' => $owner->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($ownExpense->id, $items->first()->id);
    }

    public function test_negative_list_reverse_permission(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $group          = FamilyGroup::factory()->create();
        $viewer         = User::factory()->create(['group_id' => $group->id]);
        $owner          = User::factory()->create(['group_id' => $group->id]);

        $ownExpense     = Expense::factory()->create(['user_id' => $viewer->id]);
        Expense::factory()->create(['user_id' => $owner->id]);

        ExpensePermission::factory()->create(['owner_id' => $viewer->id, 'viewer_id' => $owner->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($ownExpense->id, $items->first()->id);
    }

    public function test_negative_list_owner_left_group(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $group          = FamilyGroup::factory()->create();
        $viewer         = User::factory()->create(['group_id' => $group->id]);
        $formerMember   = User::factory()->create(['group_id' => null]);

        $ownExpense     = Expense::factory()->create(['user_id' => $viewer->id]);
        Expense::factory()->create(['user_id' => $formerMember->id]);

        ExpensePermission::factory()->create(['owner_id' => $formerMember->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($ownExpense->id, $items->first()->id);
    }

    public function test_negative_list_owner_in_other_group(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $group          = FamilyGroup::factory()->create();
        $foreignGroup   = FamilyGroup::factory()->create();
        $viewer         = User::factory()->create(['group_id' => $group->id]);
        $foreignOwner   = User::factory()->create(['group_id' => $foreignGroup->id]);

        $ownExpense     = Expense::factory()->create(['user_id' => $viewer->id]);
        Expense::factory()->create(['user_id' => $foreignOwner->id]);

        ExpensePermission::factory()->create(['owner_id' => $foreignOwner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($ownExpense->id, $items->first()->id);
    }

    public function test_positive_list_filter_by_user_ids(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $firstOwner         = User::factory()->create(['group_id' => $group->id]);
        $secondOwner        = User::factory()->create(['group_id' => $group->id]);

        $firstExpense       = Expense::factory()->create(['user_id' => $firstOwner->id]);
        Expense::factory()->create(['user_id' => $secondOwner->id]);
        Expense::factory()->create(['user_id' => $viewer->id]);

        ExpensePermission::factory()->create(['owner_id' => $firstOwner->id, 'viewer_id' => $viewer->id]);
        ExpensePermission::factory()->create(['owner_id' => $secondOwner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10, null, [$firstOwner->id]);

        $this->assertCount(1, $items);
        $this->assertEquals($firstExpense->id, $items->first()->id);
    }

    public function test_positive_list_filter_by_template_group_ids(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $owner              = User::factory()->create(['group_id' => $group->id]);

        $viewerTemplateGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $viewer->id]);
        $ownerTemplateGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $owner->id]);

        $viewerExpense      = Expense::factory()->create(['user_id' => $viewer->id, 'template_group_id' => $viewerTemplateGroup->id]);
        $ownerExpense       = Expense::factory()->create(['user_id' => $owner->id, 'template_group_id' => $ownerTemplateGroup->id]);
        Expense::factory()->create(['user_id' => $owner->id, 'template_group_id' => null]);

        ExpensePermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10, null, [], [$viewerTemplateGroup->id, $ownerTemplateGroup->id]);

        $this->assertCount(2, $items);
        $this->assertEqualsCanonicalizing([$viewerExpense->id, $ownerExpense->id], $items->pluck('id')->all());
    }

    public function test_negative_list_filter_by_template_group_ids_keeps_permissions(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $owner              = User::factory()->create(['group_id' => $group->id]);

        $ownerTemplateGroup = ExpenseTemplateGroup::factory()->create(['user_id' => $owner->id]);

        Expense::factory()->create(['user_id' => $owner->id, 'template_group_id' => $ownerTemplateGroup->id]);

        Auth::login($viewer);

        $items = $service->list(10, null, [], [$ownerTemplateGroup->id]);

        $this->assertCount(0, $items);
    }

    public function test_negative_list_hides_foreign_template_group_name(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $owner              = User::factory()->create(['group_id' => $group->id]);

        $viewerTemplateGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => $viewer->id]);
        $ownerTemplateGroup     = ExpenseTemplateGroup::factory()->create(['user_id' => $owner->id]);

        $foreignGroupExpense    = Expense::factory()->create(['user_id' => $owner->id, 'template_group_id' => $viewerTemplateGroup->id]);
        $ownGroupExpense        = Expense::factory()->create(['user_id' => $owner->id, 'template_group_id' => $ownerTemplateGroup->id]);

        ExpensePermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10);

        $this->assertNull($items->firstWhere('id', $foreignGroupExpense->id)->template_group_name);
        $this->assertEquals($ownerTemplateGroup->name, $items->firstWhere('id', $ownGroupExpense->id)->template_group_name);
    }

    public function test_positive_list_without_group_only_own(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $owner      = User::factory()->create();

        $ownExpense = Expense::factory()->create(['user_id' => $user->id]);
        Expense::factory()->create(['user_id' => $owner->id]);

        ExpensePermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $user->id]);

        Auth::login($user);

        $items = $service->list(10);

        $this->assertCount(1, $items);
        $this->assertEquals($ownExpense->id, $items->first()->id);
    }

    public function test_positive_list_limit_returns_newest_first(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $first      = Expense::factory()->create(['user_id' => $user->id]);
        $second     = Expense::factory()->create(['user_id' => $user->id]);
        $third      = Expense::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $items = $service->list(2);

        $this->assertSame([$third->id, $second->id], $items->pluck('id')->all());
    }

    public function test_positive_list_last_id_returns_older(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $first      = Expense::factory()->create(['user_id' => $user->id]);
        $second     = Expense::factory()->create(['user_id' => $user->id]);
        $third      = Expense::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $items = $service->list(10, $third->id);

        $this->assertSame([$second->id, $first->id], $items->pluck('id')->all());
    }

    public function test_positive_list_last_id_with_limit(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $first      = Expense::factory()->create(['user_id' => $user->id]);
        $second     = Expense::factory()->create(['user_id' => $user->id]);
        $third      = Expense::factory()->create(['user_id' => $user->id]);

        Auth::login($user);

        $items = $service->list(1, $third->id);

        $this->assertSame([$second->id], $items->pluck('id')->all());
    }

    public function test_positive_list_date_from_returns_only_newer(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateFrom   = Carbon::parse('2026-11-01 00:00:00');

        $user       = User::factory()->create();

        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateFrom->copy()->subSecond()]);
        $boundary   = Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateFrom]);
        $newer      = Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateFrom->copy()->addHours(5)]);

        Auth::login($user);

        $items = $service->list(10, null, [], [], $dateFrom);

        $this->assertSame([$newer->id, $boundary->id], $items->pluck('id')->all());
    }

    public function test_positive_list_date_to_is_exclusive(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateTo     = Carbon::parse('2026-11-01 00:00:00');

        $user       = User::factory()->create();

        $older      = Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateTo->copy()->subSecond()]);
        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateTo]);
        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateTo->copy()->addHour()]);

        Auth::login($user);

        $items = $service->list(10, null, [], [], null, $dateTo);

        $this->assertSame([$older->id], $items->pluck('id')->all());
    }

    public function test_positive_list_date_window_returns_only_inside(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateFrom   = Carbon::parse('2026-10-31 00:00:00');
        $dateTo     = Carbon::parse('2026-11-01 00:00:00');

        $user       = User::factory()->create();

        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateFrom->copy()->subSecond()]);
        $inside     = Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateFrom->copy()->addHours(3)]);
        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $dateTo]);

        Auth::login($user);

        $items = $service->list(10, null, [], [], $dateFrom, $dateTo);

        $this->assertSame([$inside->id], $items->pluck('id')->all());
    }

    public function test_positive_list_date_from_converts_timezone(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $older      = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-31 20:59:59']);
        $newer      = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-10-31 21:00:00']);

        Auth::login($user);

        $items = $service->list(10, null, [], [], Carbon::parse('2026-11-01 00:00:00+03:00'));

        $this->assertSame([$newer->id], $items->pluck('id')->all());
        $this->assertNotContains($older->id, $items->pluck('id')->all());
    }

    public function test_negative_list_date_from_later_than_date_to(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateFrom   = Carbon::parse('2026-11-02 00:00:00');
        $dateTo     = Carbon::parse('2026-11-01 00:00:00');

        $user       = User::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Дата начала не может быть позже даты окончания');

        $service->list(10, null, [], [], $dateFrom, $dateTo);
    }

    public function test_negative_list_date_from_later_than_date_to_in_other_timezone(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateFrom   = Carbon::parse('2026-11-01 00:00:00+03:00');
        $dateTo     = Carbon::parse('2026-10-31 20:00:00+00:00');

        $user       = User::factory()->create();

        Auth::login($user);

        $this->expectException(HandledException::class);

        $service->list(10, null, [], [], $dateFrom, $dateTo);
    }

    public function test_positive_list_date_from_equals_date_to(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $date       = Carbon::parse('2026-11-01 00:00:00');

        $user       = User::factory()->create();

        Expense::factory()->create(['user_id' => $user->id, 'created_at' => $date]);

        Auth::login($user);

        $items = $service->list(10, null, [], [], $date, $date);

        $this->assertCount(0, $items);
    }

    public function test_negative_list_date_from_keeps_permission_filter(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $dateFrom   = Carbon::parse('2026-11-01 00:00:00');

        $group      = FamilyGroup::factory()->create();
        $viewer     = User::factory()->create(['group_id' => $group->id]);
        $owner      = User::factory()->create(['group_id' => $group->id]);

        Expense::factory()->create(['user_id' => $owner->id, 'created_at' => $dateFrom->copy()->addHour()]);

        Auth::login($viewer);

        $items = $service->list(10, null, [], [], $dateFrom);

        $this->assertCount(0, $items);
    }

    public function test_negative_list_filter_by_user_ids_keeps_permissions(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $group      = FamilyGroup::factory()->create();
        $viewer     = User::factory()->create(['group_id' => $group->id]);
        $owner      = User::factory()->create(['group_id' => $group->id]);

        Expense::factory()->create(['user_id' => $owner->id]);

        Auth::login($viewer);

        $items = $service->list(10, null, [$owner->id]);

        $this->assertCount(0, $items);
    }

    public function test_negative_list_deleted_expense_not_returned(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        $deleted    = Expense::factory()->create(['user_id' => $user->id]);
        $alive      = Expense::factory()->create(['user_id' => $user->id]);

        $deleted->delete();

        Auth::login($user);

        $items = $service->list(10);

        $this->assertSame([$alive->id], $items->pluck('id')->all());
    }

    public function test_positive_create_by_global_template(): void
    {
        $amount         = '75';

        $service        = $this->app->make(ExpenseService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $service->create($template->id, null, $amount);

        $this->assertDatabaseHas('expenses', [
            'user_id'           => $user->id,
            'template_group_id' => $globalGroup->id,
            'template_id'       => $template->id,
            'name'              => $template->name,
        ]);
    }

    public function test_positive_create_by_global_template_uses_family_copy_name(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk']);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Our milk',
        ]);

        Auth::login($user);

        $service->create($template->id, null, '75');

        $this->assertDatabaseHas('expenses', [
            'user_id'           => $user->id,
            'template_id'       => $template->id,
            'template_group_id' => $globalGroup->id,
            'name'              => 'Our milk',
        ]);
    }

    public function test_negative_create_by_global_template_hidden_by_family_copy(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $family         = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'is_active' => true]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $family->id,
            'expense_template_id'   => $template->id,
            'is_active'             => false,
        ]);

        Auth::login($user);

        $this->expectException(HandledException::class);
        $this->expectExceptionMessage('Template not found');

        $service->create($template->id, null, '75');
    }

    public function test_positive_create_by_global_template_ignores_copy_of_other_family(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $family         = FamilyGroup::factory()->create();
        $otherFamily    = FamilyGroup::factory()->create();
        $user           = User::factory()->create(['group_id' => $family->id]);
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);
        $template       = ExpenseTemplate::factory()->create(['template_group_id' => $globalGroup->id, 'name' => 'Milk', 'is_active' => true]);

        ExpenseTemplateUserGroup::factory()->create([
            'group_id'              => $otherFamily->id,
            'expense_template_id'   => $template->id,
            'name'                  => 'Other family milk',
            'is_active'             => false,
        ]);

        Auth::login($user);

        $service->create($template->id, null, '75');

        $this->assertDatabaseHas('expenses', [
            'user_id'       => $user->id,
            'template_id'   => $template->id,
            'name'          => 'Milk',
        ]);
    }

    public function test_positive_list_shows_global_template_group_name(): void
    {
        $service        = $this->app->make(ExpenseService::class);

        $user           = User::factory()->create();
        $globalGroup    = ExpenseTemplateGroup::factory()->create(['user_id' => null]);

        $expense        = Expense::factory()->create(['user_id' => $user->id, 'template_group_id' => $globalGroup->id]);

        Auth::login($user);

        $items = $service->list(10);

        $this->assertEquals($globalGroup->name, $items->firstWhere('id', $expense->id)->template_group_name);
    }

    public function test_positive_create_with_date(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $service->create(null, 'Taxi', '10', null, $date);

        $this->assertDatabaseHas('expenses', [
            'user_id'       => $user->id,
            'created_at'    => '2026-10-15 12:00:00',
        ]);
    }

    public function test_positive_create_date_converts_timezone(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00+03:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $service->create(null, 'Taxi', '10', null, $date);

        $this->assertDatabaseHas('expenses', [
            'user_id'       => $user->id,
            'created_at'    => '2026-10-15 09:00:00',
        ]);
    }

    public function test_positive_create_without_date_uses_current_time(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();

        Auth::login($user);

        $service->create(null, 'Taxi', '10');

        $expense = Expense::query()->first();

        $this->assertNotNull($expense->created_at);
        $this->assertEqualsWithDelta(now()->timestamp, $expense->created_at->timestamp, 5);
    }

    public function test_positive_create_by_template_with_date(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['template_group_id' => $group->id]);

        Auth::login($user);

        $service->create($template->id, null, '75', null, $date);

        $this->assertDatabaseHas('expenses', [
            'user_id'           => $user->id,
            'template_id'       => $template->id,
            'template_group_id' => $group->id,
            'created_at'        => '2026-10-15 12:00:00',
        ]);
    }

    public function test_positive_update_with_date(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-01-01 08:00:00']);

        Auth::login($user);

        $service->update($expense->id, null, 'Metro', '60', null, $date);

        $this->assertDatabaseHas('expenses', [
            'id'            => $expense->id,
            'name'          => 'Metro',
            'created_at'    => '2026-10-15 12:00:00',
        ]);
    }

    public function test_positive_update_date_converts_timezone(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00+03:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-01-01 08:00:00']);

        Auth::login($user);

        $service->update($expense->id, null, 'Metro', '60', null, $date);

        $this->assertDatabaseHas('expenses', [
            'id'            => $expense->id,
            'created_at'    => '2026-10-15 09:00:00',
        ]);
    }

    public function test_positive_update_keeps_date_when_not_passed(): void
    {
        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['user_id' => $user->id, 'created_at' => '2026-01-01 08:00:00']);

        Auth::login($user);

        $service->update($expense->id, null, 'Metro', '60');

        $this->assertDatabaseHas('expenses', [
            'id'            => $expense->id,
            'name'          => 'Metro',
            'created_at'    => '2026-01-01 08:00:00',
        ]);
    }

    public function test_negative_update_foreign_expense_date_not_changed(): void
    {
        $date       = Carbon::parse('2026-10-15 12:00:00');

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $expense    = Expense::factory()->create(['created_at' => '2026-01-01 08:00:00']);

        Auth::login($user);

        try {
            $service->update($expense->id, null, 'Hacked', '1', null, $date);
        } catch (HandledException) {
        }

        $this->assertDatabaseHas('expenses', [
            'id'            => $expense->id,
            'created_at'    => '2026-01-01 08:00:00',
        ]);
    }

    public function test_positive_list_last_id_applies_to_shared_expenses(): void
    {
        $service            = $this->app->make(ExpenseService::class);

        $group              = FamilyGroup::factory()->create();
        $viewer             = User::factory()->create(['group_id' => $group->id]);
        $owner              = User::factory()->create(['group_id' => $group->id]);

        $olderOwnerExpense  = Expense::factory()->create(['user_id' => $owner->id]);
        $viewerExpense      = Expense::factory()->create(['user_id' => $viewer->id]);
        $newerOwnerExpense  = Expense::factory()->create(['user_id' => $owner->id]);

        ExpensePermission::factory()->create(['owner_id' => $owner->id, 'viewer_id' => $viewer->id]);

        Auth::login($viewer);

        $items = $service->list(10, $newerOwnerExpense->id);

        $this->assertSame([$viewerExpense->id, $olderOwnerExpense->id], $items->pluck('id')->all());
    }
}
