<?php

namespace Database\Factories\Chat;

use App\Models\{
    Chat\Chat,
    Chat\ChatMembers,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chat>
 */
class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [];
    }

    public function withMembers(User ...$users): static
    {
        return $this->afterCreating(function (Chat $chat) use ($users) {
            foreach ($users as $user) {
                ChatMembers::factory()->create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                ]);
            }
        });
    }
}
