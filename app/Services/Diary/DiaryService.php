<?php

namespace App\Services\Diary;

use App\Services\Diary\{
    Repositories\DiaryEntryRepository,
    Util\DiaryRulesUtil,
};
use App\SharedKernel\Checkers\DateChecker;
use Illuminate\{
    Database\Eloquent\Model,
    Support\Carbon,
    Support\Collection,
    Support\Facades\Auth,
};

class DiaryService
{
    public function __construct(
        private readonly DiaryEntryRepository $diaryEntryRepository,
    ) {
    }

    public function list(
        array       $userIds = [],
        Carbon|null $dateFrom = null,
        Carbon|null $dateTo = null
    ): Collection {
        DateChecker::isFromNotAfterTo($dateFrom, $dateTo);

        $user = Auth::user();

        return $this->diaryEntryRepository->getForUser(
            $user->id,
            $user->group_id,
            $userIds,
            $dateFrom,
            $dateTo
        );
    }

    public function create(string $text, Carbon $date, Carbon|null $dateEnd = null): Model
    {
        DiaryRulesUtil::isDateAllowed($date);
        DiaryRulesUtil::isEndAfterStart($date, $dateEnd);

        return $this->diaryEntryRepository->create([
            'user_id'   => Auth::id(),
            'text'      => $text,
            'date'      => $date->toDateTimeString(),
            'date_end'  => $dateEnd?->toDateTimeString(),
        ]);
    }

    public function update(int $id, string $text, Carbon $date, Carbon|null $dateEnd = null): Model
    {
        $userId = Auth::id();
        $entry  = $this->diaryEntryRepository->firstWhere(['id' => $id, 'user_id' => $userId]);

        DiaryRulesUtil::isExists($entry);

        $isDateChanged = ! $entry->date->equalTo($date);

        if ($isDateChanged) {
            DiaryRulesUtil::isDateAllowed($date);
        }

        DiaryRulesUtil::isEndAfterStart($date, $dateEnd);

        $this->diaryEntryRepository->update(
            ['id' => $id, 'user_id' => $userId],
            [
                'text'      => $text,
                'date'      => $date->toDateTimeString(),
                'date_end'  => $dateEnd?->toDateTimeString(),
            ]
        );

        return $entry->refresh();
    }

    public function delete(int $id): void
    {
        $userId = Auth::id();
        $entry  = $this->diaryEntryRepository->firstWhere(['id' => $id, 'user_id' => $userId]);

        DiaryRulesUtil::isExists($entry);

        $this->diaryEntryRepository->delete(['id' => $id, 'user_id' => $userId]);
    }
}
