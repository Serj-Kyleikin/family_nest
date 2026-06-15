<?php

namespace App\Services\Chat\Swagger\Resources;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatForbiddenResource",
 *     description="Чат не принадлежит пользователю",
 *     @OA\Xml(
 *         name="ChatForbiddenResource"
 *     )
 * )
 */
class ChatForbiddenResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"This user isn't member of this chat"}
     * )
     */
    public $data;
}
