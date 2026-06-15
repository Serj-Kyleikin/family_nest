<?php

namespace App\Services\Family\Util;

use Exception;
use Illuminate\{
    Http\Response,
};

class FamilyRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isGroupCreated(int|null $group): void
    {
        throw_unless(
            $group,
            Exception::class,
            "User group not found",
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isUserInOneGroup(int|null $userFromGroup, int|null $userToGroup): void
    {
        throw_if(
            $userFromGroup != $userToGroup,
            Exception::class,
            "User is not exists",
            Response::HTTP_BAD_REQUEST
        );
    }
}
