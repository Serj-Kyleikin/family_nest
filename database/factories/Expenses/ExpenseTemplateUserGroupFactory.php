<?php

namespace Database\Factories\Expenses;

use App\Models\{
    Expenses\ExpenseTemplate,
    Expenses\ExpenseTemplateUserGroup,
    Family\FamilyGroup,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseTemplateUserGroup>
 */
class ExpenseTemplateUserGroupFactory extends Factory
{
    protected $model = ExpenseTemplateUserGroup::class;

    public function definition(): array
    {
        return [
            'group_id'              => FamilyGroup::factory(),
            'expense_template_id'   => ExpenseTemplate::factory(),
            'name'                  => $this->faker->word(),
            'price'                 => null,
            'is_active'             => true,
        ];
    }
}
