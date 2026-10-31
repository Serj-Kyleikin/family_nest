<?php

namespace Database\Factories\Expenses;

use App\Models\{
    Expenses\Expense,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'group_id'      => null,
            'template_id'   => null,
            'name'          => $this->faker->word(),
            'amount'        => 100,
        ];
    }
}
