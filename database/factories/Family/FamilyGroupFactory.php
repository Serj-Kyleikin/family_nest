<?php

namespace Database\Factories\Family;

use App\Models\Family\FamilyGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class FamilyGroupFactory extends Factory
{
    protected $model = FamilyGroup::class;

    public function definition()
    {
        return [
            'name' => $this->faker->text
        ];
    }
}
