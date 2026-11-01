<?php

namespace Database\Factories;

use App\Models\{
    User,
    UserData,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserData>
 */
class UserDataFactory extends Factory
{
    protected $model = UserData::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'status'        => fake()->sentence(3),
            'family_status' => fake()->randomElement(['Мама', 'Папа', 'Сын', 'Дочь']),
            'avatar'        => null,
            'birthday'      => fake()->date(),
            'address'       => fake()->address(),
            'phone'         => '+79001234567',
        ];
    }
}
