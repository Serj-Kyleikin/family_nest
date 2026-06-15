<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatStoreBadRequestResource",
 *     description="Попытка создать чат с заблокированным пользователем",
 *     @OA\Xml(
 *         name="ChatStoreBadRequestResource"
 *     )
 * )
 */
class ChatStoreBadRequestResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"Chat can't be created with blocked user"}
     * )
     */
    public $data;
}
