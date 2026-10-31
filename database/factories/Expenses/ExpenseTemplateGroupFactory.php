<?php

namespace Database\Factories\Expenses;

use App\Models\{
    Expenses\ExpenseTemplateGroup,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseTemplateGroup>
 */
class ExpenseTemplateGroupFactory extends Factory
{
    protected $model = ExpenseTemplateGroup::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'name'      => $this->faker->word(),
        ];
    }
}
