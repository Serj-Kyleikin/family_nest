<?php

namespace Tests\Unit\Services\Expenses;

use App\Exceptions\HandledException;
use App\Models\{
    Expenses\Expense,
    Expenses\ExpenseTemplate,
    Expenses\ExpenseTemplateGroup,
    User,
};
use App\Services\{
    Expenses\ExpenseService,
};
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
            'user_id'       => $user->id,
            'group_id'      => null,
            'template_id'   => null,
            'name'          => $name,
        ]);
        $this->assertEquals((float) $amount, (float) Expense::query()->first()->amount);
    }

    public function test_positive_create_by_template(): void
    {
        $amount     = '75';

        $service    = $this->app->make(ExpenseService::class);

        $user       = User::factory()->create();
        $group      = ExpenseTemplateGroup::factory()->create(['user_id' => $user->id]);
        $template   = ExpenseTemplate::factory()->create(['group_id' => $group->id]);

        Auth::login($user);

        $service->create($template->id, null, $amount);

        $this->assertDatabaseHas('expenses', [
            'user_id'       => $user->id,
            'group_id'      => $group->id,
            'template_id'   => $template->id,
            'name'          => $template->name,
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
        $template   = ExpenseTemplate::factory()->create(['group_id' => $group->id, 'is_active' => false]);

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

        $items = $service->list();

        $this->assertCount(1, $items);
        $this->assertEquals($expense->id, $items->first()->id);
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
        $expense    = Expense::factory()->create(['user_id' => $user->id, 'group_id' => $groupId]);

        Auth::login($user);

        $service->update($expense->id, null, 'Metro', '60');

        $this->assertDatabaseHas('expenses', [
            'id'        => $expense->id,
            'group_id'  => $groupId,
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
}
