<?php

namespace Database\Factories\Diary;

use App\Models\{
    Diary\DiaryPermission,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiaryPermission>
 */
class DiaryPermissionFactory extends Factory
{
    protected $model = DiaryPermission::class;

    public function definition(): array
    {
        return [
            'owner_id'  => User::factory(),
            'viewer_id' => User::factory(),
        ];
    }
}
