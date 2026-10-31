<?php

namespace Database\Factories\Family;

use App\Models\{
    Family\FamilyGroup,
    Family\FamilyGroupLink,
};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FamilyGroupLink>
 */
class FamilyGroupLinkFactory extends Factory
{
    protected $model = FamilyGroupLink::class;

    public function definition(): array
    {
        return [
            'group_id'  => FamilyGroup::factory(),
            'token'     => Str::random(40),
            'is_active' => true,
        ];
    }
}
