<?php

namespace Database\Factories\Expenses;

use App\Models\{
    Expenses\ExpensePermission,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpensePermission>
 */
class ExpensePermissionFactory extends Factory
{
    protected $model = ExpensePermission::class;

    public function definition(): array
    {
        return [
            'owner_id'  => User::factory(),
            'viewer_id' => User::factory(),
        ];
    }
}
