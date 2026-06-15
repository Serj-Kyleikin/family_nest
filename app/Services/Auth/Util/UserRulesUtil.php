<?php

namespace App\Services\Auth\Util;

use App\Models\{
    User,
};
use Exception;
use Illuminate\{
    Http\Response,
};

class UserRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isUserExists(int $userToId): User
    {
        $userTo = User::where('id', $userToId)->first();

        throw_unless(
            $userTo,
            Exception::class,
            "User is not exists",
            Response::HTTP_BAD_REQUEST
        );

        return $userTo;
    }
}
