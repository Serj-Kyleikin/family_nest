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
    public const DISCUSSIONS_LIMIT = 20;

    public function __construct(
        protected Chat $model
    )
    {
    }

    public function getUserChats(int $userId, string|null $text = null): Collection
    {
        $chats = $this->model->query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->has('discussions')
            ->when(filled($text), function ($query) use ($text) {
                $query->whereHas('discussions', function ($query) use ($text) {
                    $query->where('text', 'LIKE', '%' . $text . '%');
                });
            })
            ->withCount([
                'discussions as unread_count' => function ($query) use ($userId) {
                    $query
                        ->where('user_id', '!=', $userId)
                        ->where(function ($query) {
                            $query
                                ->whereNull('is_read')
                                ->orWhere('is_read', 0);
                        });
                },
            ])
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

    public function getWithDiscussionsAndMembers(int $chatId, ?int $beforeId = null): Chat
    {
        $chat = $this->model->query()
            ->where(['id' => $chatId])
            ->with([
                'discussions' => function ($query) use ($beforeId) {
                    $query
                        ->when($beforeId !== null, function ($query) use ($beforeId) {
                            $query->where('id', '<', $beforeId);
                        })
                        ->orderBy('id', 'desc')
                        ->limit(self::DISCUSSIONS_LIMIT + 1);
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

        $hasMore = $chat->discussions->count() > self::DISCUSSIONS_LIMIT;

        $chat->setRelation(
            'discussions',
            $chat->discussions
                ->take(self::DISCUSSIONS_LIMIT)
                ->reverse()
                ->values()
        );

        $chat->setAttribute('has_more', $hasMore);

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

        if($chat instanceof Chat) {
            $chat->users->makeHidden('pivot');
        }

        return $chat;
    }

    public function getIdsWhereAllMembersIn(array $userIds): array
    {
        return $this->model->query()
            ->whereHas('members', fn ($query) => $query->whereIn('user_id', $userIds))
            ->whereDoesntHave('members', fn ($query) => $query->whereNotIn('user_id', $userIds))
            ->pluck('id')
            ->all();
    }

    public function deleteByIds(array $chatIds): void
    {
        $this->model->query()
            ->whereIn('id', $chatIds)
            ->delete();
    }
}
