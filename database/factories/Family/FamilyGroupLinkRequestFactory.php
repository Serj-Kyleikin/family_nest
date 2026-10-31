<?php

namespace Database\Factories\Family;

use App\Models\{
    Family\FamilyGroupLink,
    Family\FamilyGroupLinkRequest,
    User,
};
use App\Services\Family\Enums\FamilyGroupLinkRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FamilyGroupLinkRequest>
 */
class FamilyGroupLinkRequestFactory extends Factory
{
    protected $model = FamilyGroupLinkRequest::class;

    public function definition(): array
    {
        return [
            'link_id'   => FamilyGroupLink::factory(),
            'user_id'   => User::factory(),
            'status'    => FamilyGroupLinkRequestStatusEnum::PENDING->value,
        ];
    }
}
