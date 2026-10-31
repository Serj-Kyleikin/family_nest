<?php

namespace App\Services\Chat\Handler;

use App\Services\{
    Chat\Repositories\ChatDiscussionRepository,
    Chat\Repositories\ChatMemberRepository,
    Chat\Repositories\ChatRepository,
};

class DeleteUsersChatsHandler
{
    public function __construct(
        private readonly ChatRepository             $chatRepository,
        private readonly ChatDiscussionRepository   $chatDiscussionRepository,
        private readonly ChatMemberRepository       $chatMemberRepository,
    )
    {
    }

    public function handle(array $userIds): void
    {
        $chatIds = $this->chatRepository->getIdsWhereAllMembersIn($userIds);

        $this->chatDiscussionRepository->deleteByChatIds($chatIds);
        $this->chatMemberRepository->deleteByChatIds($chatIds);
        $this->chatRepository->deleteByIds($chatIds);
    }
}
