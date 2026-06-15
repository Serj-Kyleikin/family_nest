<?php

namespace Database\Factories\Chat;

use App\Models\{
    Chat\Chat,
    Chat\ChatDiscussion,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatDiscussion>
 */
class ChatDiscussionFactory extends Factory
{
    protected $model = ChatDiscussion::class;

    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'user_id' => User::factory(),
            'text'    => fake()->sentence(),
            'is_read' => 0,
        ];
    }
}
