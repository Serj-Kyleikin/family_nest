<?php

namespace App\Services\Chat\Handler;

use App\Models\{
    Chat\Chat,
};
use App\Services\{
    Auth\Util\UserRulesUtil,
    Chat\Util\ChatRulesUtil,
    Chat\Repositories\ChatRepository,
    Chat\Repositories\ChatMemberRepository,
    Family\Util\FamilyRulesUtil,
};
use Illuminate\{
    Support\Facades\Auth,
};

class CreateChatHandler
{
    public function __construct(
        private readonly ChatRepository         $chatRepository,
        private readonly ChatMemberRepository   $chatMemberRepository,
    )
    {
    }

    public function handle(int $userToId): Chat
    {
        $userFrom           = Auth::user();
        $userFromId         = $userFrom->id;
        $userFromGroup      = $userFrom->group_id;

        $userTo = UserRulesUtil::isUserExists($userToId);
        FamilyRulesUtil::isGroupCreated($userFromGroup);
        FamilyRulesUtil::isUserInOneGroup($userFromGroup, $userTo->group_id);
        ChatRulesUtil::isSentToSelf($userFromId, $userToId);

        $chat = $this->chatRepository->firstByUsersIds($userFrom->id, $userToId);

        if ($chat !== null) {
            return $chat;
        }

        $chat = $this->chatRepository->create([]);
        $this->chatMemberRepository->insert([
            ['chat_id' => $chat->id, 'user_id' => $userFrom->id],
            ['chat_id' => $chat->id, 'user_id' => $userToId],
        ]);

        $chat->load([
            'users:id,name',
            'discussions',
        ]);

        $chat->users->makeHidden('pivot');

        return $chat;
    }
}
