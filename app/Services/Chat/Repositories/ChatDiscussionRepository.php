<?php

namespace App\Services\Chat\Repositories;

use App\Models\Chat\ChatDiscussion;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use App\SharedKernel\Repository\Contracts\DeleteContract;
use App\SharedKernel\Repository\Contracts\UpdateContract;
use Illuminate\Support\Collection;

class ChatDiscussionRepository extends AbstractRepository implements CreateContract, UpdateContract, DeleteContract
{
    public function __construct(
        protected ChatDiscussion $model
    )
    {
    }

    public function searchByText(int $chatId, string $text): Collection
    {
        return $this->model->query()
            ->whereHas('chat', function ($query) use ($chatId) {
                $query->where(['id' => $chatId]);
            })
            ->where('text', 'LIKE', '%' . $text . '%')
            ->with(['user:id,name'])
            ->get();
    }
}
