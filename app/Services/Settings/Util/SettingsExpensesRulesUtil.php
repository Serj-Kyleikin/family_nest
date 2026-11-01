<?php

namespace App\Services\Settings\Util;

use App\Exceptions\HandledException;
use Symfony\Component\HttpFoundation\Response;

class SettingsExpensesRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isMembers(array $userIds, array $memberIds): void
    {
        $isMembers = array_diff($userIds, $memberIds) === [];

        throw_unless(
            $isMembers,
            HandledException::class,
            'User not found',
            Response::HTTP_NOT_FOUND
        );
    }
}
