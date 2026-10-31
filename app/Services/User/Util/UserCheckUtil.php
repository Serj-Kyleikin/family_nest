<?php

namespace App\Services\User\Util;

use App\Exceptions\HandledException;
use App\Models\User;

class UserCheckUtil
{
    public static function hasGroup(int|null $groupId): bool
    {
        return $groupId != null;
    }

    public static function isExists(User|null $user): void
    {
        throw_unless(
            $user,
            HandledException::class,
            'User not found',
            404
        );
    }
}
