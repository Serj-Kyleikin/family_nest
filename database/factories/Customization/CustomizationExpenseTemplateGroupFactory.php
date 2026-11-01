<?php

namespace Database\Factories\Customization;

use App\Models\{
    Customization\CustomizationExpenseTemplateGroup,
    Expenses\ExpenseTemplateGroup,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomizationExpenseTemplateGroup>
 */
class CustomizationExpenseTemplateGroupFactory extends Factory
{
    protected $model = CustomizationExpenseTemplateGroup::class;

    public function definition(): array
    {
        return [
            'user_id'                   => User::factory(),
            'expense_template_group_id' => ExpenseTemplateGroup::factory(),
            'priority'                  => $this->faker->numberBetween(1, 100),
        ];
    }
}
