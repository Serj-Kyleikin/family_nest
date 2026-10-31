<?php

namespace App\Services\Chat;

use App\Services\{
    Chat\Handler\PublishChatEventHandler,
    Chat\Repositories\ChatDiscussionRepository,
    Chat\Util\ChatRulesUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ChatDiscussionService
{
    public function __construct(
        private readonly ChatDiscussionRepository   $chatDiscussionRepository,
        private readonly PublishChatEventHandler    $publishChatEventHandler,
    )
    {
    }

    public function create(int $chatId, string|null $text = null): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);

        $this->chatDiscussionRepository->create([
            'chat_id'   => $chatId,
            'user_id'   => $senderId,
            'text'      => $text
        ]);

        $this->publishChatEventHandler->handleUpdated($chatId);
        $this->publishChatEventHandler->handleMessage($chatId, $senderId, strval(Auth::user()->name), $text);
    }

    public function update(int $chatId, int $discussionId, string|null $text = null): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::isDiscussionBelongsToChatAndUser($chatId, $discussionId, $senderId);

        $this->chatDiscussionRepository->update(
            ['id' => $discussionId],
            ['text' => $text]
        );

        $this->publishChatEventHandler->handleUpdated($chatId);
    }

    public function delete(int $chatId, int $discussionId): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::isDiscussionBelongsToChatAndUser($chatId, $discussionId, $senderId);

        $this->chatDiscussionRepository->delete(['id' => $discussionId]);

        $this->publishChatEventHandler->handleUpdated($chatId);
    }

    public function searchByText(int $chatId, string $text): Collection
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);

        return $this->chatDiscussionRepository->searchByText($chatId, $text);
    }

    public function setIsRead(int $chatId, array $lookedDiscussionsIds): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::areDiscussionsBelongsToChat($chatId, $senderId, $lookedDiscussionsIds);

        $this->chatDiscussionRepository->updateWhereIn('id', $lookedDiscussionsIds, ['is_read' => 1]);

        $this->publishChatEventHandler->handleUpdated($chatId);
    }
}
