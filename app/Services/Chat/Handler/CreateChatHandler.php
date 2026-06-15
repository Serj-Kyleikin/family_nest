<?php

namespace App\Services\Chat\Handler;

use App\Models\{
    Chat\Chat,
    Chat\ChatMembers,
};
use App\Services\{
    Auth\Util\UserRulesUtil,
    Chat\Util\ChatRulesUtil,
    Family\Util\FamilyRulesUtil,
};
use Illuminate\{
    Support\Facades\Auth,
};

class CreateChatHandler
{
    public function handle(int $userToId): Chat
    {
        $userFrom           = Auth::user();
        $userFromId         = $userFrom->id;
        $userFromGroup      = $userFrom->group_id;

        $userTo = UserRulesUtil::isUserExists($userToId);
        FamilyRulesUtil::isGroupCreated($userFromGroup);
        FamilyRulesUtil::isUserInOneGroup($userFromGroup, $userTo->group_id);
        ChatRulesUtil::isSentToSelf($userFromId, $userToId);

        $chat = Chat::query()
            ->whereHas('members', function($membersQuery) use($userFromId, $userToId) {
                $membersQuery->whereIn('user_id', [$userFromId, $userToId]);
            })
            ->with([
                'members.user:id,name',
                'discussions'
            ])
            ->first();

        if($chat == null) {

            $chat = Chat::create();
            ChatMembers::insert([
                ['chat_id' => $chat->id, 'user_id' => $userFromId],
                ['chat_id' => $chat->id, 'user_id' => $userToId]
            ]);

            $chat->load([
                'members.user:id,name', 
                'discussions'
            ]);
        }

        $chat->setRelation(
            'members',
            $chat->members
                ->pluck('user')
                ->values()
        );

        return $chat;
    }
}
