<?php

namespace App\Services\Chat;

use App\Models\{
    Chat\Chat,
};
use App\Services\{
    Chat\Handler\CreateChatHandler,
    Chat\Repositories\ChatRepository,
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
        private readonly ChatRepository $chatRepository,
    )
    {
    }

    public function index(): Collection
    {
        $userId = Auth::id();

        return $this->chatRepository->getUserChats($userId);
    }

    public function create(int $userToId): Chat
    {
        return $this->createChatHandler->handle($userToId);
    }

    public function getChatWithDiscussionsAndMembers(int $chatId): Chat
    {
        $userId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $userId);

        return $this->chatRepository->getWithDiscussionsAndMembers($chatId);
    }
}
