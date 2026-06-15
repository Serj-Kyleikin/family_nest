<?php

namespace App\Services\Chat\Util;

use App\Models\{
    Chat\ChatMembers,
    Chat\ChatDiscussion,
};
use Exception;
use Illuminate\{
    Http\Response,
};

class ChatRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isSentToSelf(int $userFromId, int $userToId): void
    {
        throw_if(
            $userFromId == $userToId,
            Exception::class,
            "Chat can't be created with yourself",
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isUserMemberOfThisChat(int $chatId, int $memberId): void
    {
        $chatMember = ChatMembers::where(['chat_id' => $chatId, 'user_id' => $memberId])->first();

        throw_unless(
            $chatMember,
            Exception::class,
            "This user isn't member of this chat",
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isDiscussionBelongsToChatAndUser(int $chatId, int $discussionId, int $userId): void
    {
        $chatDiscussion = ChatDiscussion::where(['id' => $discussionId, 'chat_id' => $chatId, 'user_id' => $userId])->first();

        throw_unless(
            $chatDiscussion,
            Exception::class,
            "Chat message is not found",
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @throws \Throwable
     */
    public static function areDiscussionsBelongsToChat(int $chatId, int $senderId, array $lookedDiscussionsIds): void
    {
        $countResult = ChatDiscussion::whereIn('id', $lookedDiscussionsIds)
            ->where([
                ['user_id', '!=', $senderId],
                ['chat_id', '=', $chatId]
            ])
            ->count();

        throw_if(
            $countResult != count($lookedDiscussionsIds),
            Exception::class,
            "Can't update is_read status for this messages",
            Response::HTTP_BAD_REQUEST
        );
    }
}
