<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatDiscussionUpdateBadRequestResource",
 *     description="Chat Message update bad request resourse",
 *     @OA\Xml(
 *         name="ChatDiscussionUpdateBadRequestResource"
 *     )
 * )
 */
class ChatDiscussionUpdateBadRequestResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"This message cannot be updated because the date has expired"}
     * )
     */
    public $data;
}
