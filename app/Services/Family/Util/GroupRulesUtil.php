<?php

namespace App\Services\Family\Util;

use App\Exceptions\HandledException;

class GroupRulesUtil
{
    public static function isGroupCreated(int|null $groupId): void
    {
        self::isTrue($groupId, 'Group is already created');
    }

    public static function isOnlyOneGroup(int|null $groupId): void
    {
        self::isTrue($groupId, 'User is already in a group');
    }

    /**
     * @throws \Throwable
     */
    public static function isTrue(int|null $groupId, string $message, int $code = 403): void
    {
        throw_if(
            $groupId,
            HandledException::class,
            $message,
            $code
        );
    }
}
