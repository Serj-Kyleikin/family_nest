<?php

namespace App\Services\Family\Util;

use App\Exceptions\HandledException;
use App\Models\Family\{
    FamilyGroupLink,
    FamilyGroupLinkRequest,
};
use Symfony\Component\HttpFoundation\Response;

class LinksRulesUtil
{
    /**
     * @throws \Throwable
     */
    public static function isLinkExists(FamilyGroupLink|null $link): void
    {
        throw_unless(
            $link,
            HandledException::class,
            'Link not found',
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isRequestAlreadyCreated(FamilyGroupLinkRequest|null $request): void
    {
        throw_if(
            $request,
            HandledException::class,
            'Request is already created',
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * @throws \Throwable
     */
    public static function isRequestNotExists(FamilyGroupLinkRequest|null $request): void
    {
        throw_unless(
            $request,
            HandledException::class,
            'Invitation not found',
            Response::HTTP_NOT_FOUND
        );
    }
}
