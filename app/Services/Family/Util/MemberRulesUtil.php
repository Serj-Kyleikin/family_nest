<?php

namespace App\Services\Family\Util;

use App\Exceptions\HandledException;
use Symfony\Component\HttpFoundation\Response;

class MemberRulesUtil
{
    public static function isAnotherUser(int $userId, int $memberId): void
    {
        throw_if(
            $userId === $memberId,
            HandledException::class,
            'Cannot rename yourself',
            Response::HTTP_BAD_REQUEST
        );
    }
}
