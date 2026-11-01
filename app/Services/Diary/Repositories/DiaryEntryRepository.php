<?php

namespace App\Services\Diary\Repositories;

use App\Models\Diary\DiaryEntry;
use App\Models\Diary\DiaryPermission;
use App\SharedKernel\Repository\AbstractRepository;
use App\SharedKernel\Repository\Contracts\CreateContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DiaryEntryRepository extends AbstractRepository implements CreateContract
{
    public function __construct(
        protected DiaryEntry $model
    ) {
    }

    public function getForUser(
        int         $userId,
        int|null    $familyGroupId,
        array       $ownerIds,
        Carbon|null $dateFrom = null,
        Carbon|null $dateTo = null
    ): Collection {
        $permittedOwners = DiaryPermission::query()
            ->select('owner_id')
            ->where('viewer_id', $userId);

        return $this->model->query()
            ->join('users', 'users.id', '=', 'diary_entries.user_id')
            ->where(function ($query) use ($userId, $familyGroupId, $permittedOwners) {
                $query->where('diary_entries.user_id', $userId)
                    ->when($familyGroupId !== null, function ($query) use ($familyGroupId, $permittedOwners) {
                        $query->orWhere(function ($query) use ($familyGroupId, $permittedOwners) {
                            $query->where('users.group_id', $familyGroupId)
                                ->whereIn('diary_entries.user_id', $permittedOwners);
                        });
                    });
            })
            ->when($ownerIds !== [], fn ($query) => $query->whereIn('diary_entries.user_id', $ownerIds))
            ->when($dateFrom !== null, fn ($query) => $query->whereDate('diary_entries.date', '>=', $dateFrom->toDateString()))
            ->when($dateTo !== null, fn ($query) => $query->whereDate('diary_entries.date', '<=', $dateTo->toDateString()))
            ->orderBy('diary_entries.date')
            ->orderBy('diary_entries.id')
            ->get([
                'diary_entries.id',
                'diary_entries.user_id',
                'users.name as user_name',
                'diary_entries.text',
                'diary_entries.date',
                'diary_entries.date_end',
            ]);
    }
}
