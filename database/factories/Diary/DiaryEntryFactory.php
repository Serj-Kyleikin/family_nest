<?php

namespace Database\Factories\Diary;

use App\Models\{
    Diary\DiaryEntry,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiaryEntry>
 */
class DiaryEntryFactory extends Factory
{
    protected $model = DiaryEntry::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'text'      => $this->faker->sentence(),
            'date'      => now()->addDay()->toDateTimeString(),
        ];
    }
}
