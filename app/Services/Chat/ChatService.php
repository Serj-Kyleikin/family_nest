<?php

namespace App\Services\Chat;

use App\Models\{
    Chat\Chat,
};
use App\Services\{
    Chat\Handler\CreateChatHandler,
    Chat\Repositories\ChatRepository,
    Chat\Util\ChatRulesUtil,
    Family\Handler\FamilyMemberNameHandler,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ChatService
{
    public function __construct(
        private readonly CreateChatHandler          $createChatHandler,
        private readonly ChatRepository             $chatRepository,
        private readonly FamilyMemberNameHandler    $familyMemberNameHandler,
    )
    {
    }

    public function index(): Collection
    {
        $userId = Auth::id();
        $chats  = $this->chatRepository->getUserChats($userId);

        $this->familyMemberNameHandler->apply($chats->pluck('member'));

        return $chats;
    }

    public function search(string $text): Collection
    {
        $userId = Auth::id();
        $chats  = $this->chatRepository->getUserChats($userId, $text);

        $this->familyMemberNameHandler->apply($chats->pluck('member'));

        return $chats;
    }

    public function create(int $userToId): Chat
    {
        $chat = $this->createChatHandler->handle($userToId);

        $this->familyMemberNameHandler->apply($chat->users);

        return $chat;
    }

    public function getChatWithDiscussionsAndMembers(int $chatId, ?int $beforeId = null): Chat
    {
        $userId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $userId);

        $chat = $this->chatRepository->getWithDiscussionsAndMembers($chatId, $beforeId);

        $this->familyMemberNameHandler->apply($chat->members);

        return $chat;
    }
}
