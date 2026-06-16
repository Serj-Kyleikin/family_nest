<?php

namespace App\Services\Chat\Repositories;

use App\SharedKernel\{
    Repository\AbstractRepository,
    Repository\Contracts\CreateContract,
    Repository\Contracts\UpdateContract,
};
use App\Models\Chat\Chat;
use Illuminate\Support\Collection;

class ChatRepository extends AbstractRepository implements CreateContract, UpdateContract
{
    public function __construct(
        protected Chat $model
    )
    {
    }

    public function getUserChats(int $userId): Collection
    {
        $chats = $this->model->query()
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

    public function getWithDiscussionsAndMembers(int $chatId): Chat
    {
        $chat = $this->model->query()
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

    public function firstByUsersIds(int $userFromId, int $userToId): ?Chat
    {
        $chat = Chat::query()
            ->whereHas('users', fn ($query) => $query->whereKey($userFromId))
            ->whereHas('users', fn ($query) => $query->whereKey($userToId))
            ->has('users', '=', 2)
            ->with([
                'users:id,name',
                'discussions',
            ])
            ->first();

        $chat->users->makeHidden('pivot');

        return $chat;
    }
}
