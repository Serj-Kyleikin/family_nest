<?php

namespace App\SharedKernel\Checkers;

use App\Exceptions\HandledException;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DateChecker
{
    /**
     * @throws \Throwable
     */
    public static function isFromNotAfterTo(Carbon|null $dateFrom, Carbon|null $dateTo): void
    {
        $isRangeAllowed = $dateFrom === null
            || $dateTo === null
            || $dateFrom->lessThanOrEqualTo($dateTo);

        throw_unless(
            $isRangeAllowed,
            HandledException::class,
            'Дата начала не может быть позже даты окончания',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
