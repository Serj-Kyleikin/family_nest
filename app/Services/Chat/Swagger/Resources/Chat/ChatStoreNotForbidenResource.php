<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatStoreNotForbidenResource",
 *     description="Попытка создать чат с самим собой",
 *     @OA\Xml(
 *         name="ChatStoreNotForbidenResource"
 *     )
 * )
 */
class ChatStoreNotForbidenResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"Chat can't be created with yourself"}
     * )
     */
    public $data;
}
