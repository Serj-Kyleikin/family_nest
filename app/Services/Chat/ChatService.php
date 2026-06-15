<?php

namespace App\Services\Chat;

use App\Models\{
    Chat\Chat,
};
use App\Services\{
    Chat\Handler\CreateChatHandler,
    Chat\Util\ChatRulesUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ChatService
{
    public function __construct(
        private readonly CreateChatHandler $createChatHandler,
    )
    {
    }

    public function index(): Collection
    {
        $userId = Auth::id();

        $chats = Chat::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->has('discussions')
            ->with([
                'lastDiscussion:id,chat_id,user_id,text,is_read,updated_at',
                'members' => function ($query) use ($userId) {
                    $query
                        ->where('user_id', '!=', $userId)
                        ->with('user:id,name');
                },
            ])
            ->get();

        $chats->each(function (Chat $chat) {
            $chat->setRelation(
                'member',
                optional($chat->members->first())->user
            );

            $chat->unsetRelation('members');
        });

        return $chats->sortByDesc(function (Chat $chat) {
            return optional($chat->lastDiscussion)->updated_at;
        })->values();
    }

    public function create(int $userToId): Chat
    {
        return $this->createChatHandler->handle($userToId);
    }

    public function getChatWithDiscussionsAndMembers(int $chatId): Chat
    {
        $userId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $userId);

        $chat = Chat::query()
            ->where(['id' => $chatId])
            ->with([
            'discussions' => function ($query) {
                $query
                    ->orderBy('id', 'asc')
                    ->limit(20);
            },
            'members.user:id,name'
        ])
        ->first();

        $chat->setRelation(
            'members',
            $chat->members
                ->pluck('user')
                ->filter()
                ->values()
        );

        return $chat;
    }
}
