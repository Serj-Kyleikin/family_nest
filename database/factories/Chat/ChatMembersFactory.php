<?php

namespace Database\Factories\Chat;

use App\Models\{
    Chat\Chat,
    Chat\ChatMembers,
    User,
};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMembers>
 */
class ChatMembersFactory extends Factory
{
    protected $model = ChatMembers::class;

    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'user_id' => User::factory(),
        ];
    }
}
