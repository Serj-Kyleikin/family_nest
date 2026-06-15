<?php

namespace App\Services\Chat;

use App\Models\{
    Chat\ChatDiscussion,
};
use App\Services\{
    Chat\Util\ChatRulesUtil,
};
use Illuminate\{
    Support\Collection,
    Support\Facades\Auth,
};

class ChatDiscussionService
{
    public function create(int $chatId, string|null $text = null): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);

        ChatDiscussion::create([
            'chat_id'   => $chatId,
            'user_id'   => $senderId,
            'text'      => $text
        ]);
    }

    public function update(int $chatId, int $discussionId, string|null $text = null): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::isDiscussionBelongsToChatAndUser($chatId, $discussionId, $senderId);

        ChatDiscussion::where(['id' => $discussionId])
            ->update([
                'text' => $text
            ]);
    }

    public function delete(int $chatId, int $discussionId): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::isDiscussionBelongsToChatAndUser($chatId, $discussionId, $senderId);

        ChatDiscussion::where(['id' => $discussionId])->delete();
    }

    public function searchByText(int $chatId, string $text): Collection
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);

        return ChatDiscussion::query()
            ->whereHas('chat', function($query) use ($chatId) {
                $query->where(['id' => $chatId]);
            })
            ->where('text', 'LIKE', '%' . $text . '%')
            ->with(['user:id,name'])
            ->get();
    }

    public function setIsRead(int $chatId, array $lookedDiscussionsIds): void
    {
        $senderId = Auth::id();
        ChatRulesUtil::isUserMemberOfThisChat($chatId, $senderId);
        ChatRulesUtil::areDiscussionsBelongsToChat($chatId, $senderId, $lookedDiscussionsIds);

        ChatDiscussion::whereIn('id', $lookedDiscussionsIds)->update(['is_read' => 1]);
    }
}
