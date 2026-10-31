<?php

namespace Database\Factories\Expenses;

use App\Models\Expenses\{
    ExpenseTemplate,
    ExpenseTemplateGroup,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseTemplate>
 */
class ExpenseTemplateFactory extends Factory
{
    protected $model = ExpenseTemplate::class;

    public function definition(): array
    {
        return [
            'group_id'  => ExpenseTemplateGroup::factory(),
            'name'      => $this->faker->word(),
            'is_active' => true,
        ];
    }
}
