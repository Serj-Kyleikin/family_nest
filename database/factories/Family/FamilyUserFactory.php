<?php

namespace Database\Factories\Family;

use App\Models\{
    Family\FamilyUser,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyUser>
 */
class FamilyUserFactory extends Factory
{
    protected $model = FamilyUser::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'member_id' => User::factory(),
            'alias'     => fake()->firstName(),
        ];
    }
}
