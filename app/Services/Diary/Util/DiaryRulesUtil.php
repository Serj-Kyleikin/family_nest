<?php

namespace App\Services\Diary\Util;

use App\Exceptions\HandledException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class DiaryRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isExists(Model|null $entry): void
    {
        throw_unless(
            $entry,
            HandledException::class,
            'Запись не найдена',
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isDateAllowed(Carbon $date): void
    {
        $isDateAllowed = $date->greaterThanOrEqualTo(Carbon::today());

        throw_unless(
            $isDateAllowed,
            HandledException::class,
            'Дата записи не может быть в прошлом',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isEndAfterStart(Carbon $date, Carbon|null $dateEnd): void
    {
        $isEndAllowed = $dateEnd === null || $dateEnd->greaterThan($date);

        throw_unless(
            $isEndAllowed,
            HandledException::class,
            'Время окончания должно быть позже начала',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
