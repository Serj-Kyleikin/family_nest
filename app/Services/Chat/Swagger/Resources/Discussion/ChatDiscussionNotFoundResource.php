<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatDiscussionNotFoundResource",
 *     description="Chat Discussion not found resourse",
 *     @OA\Xml(
 *         name="ChatDiscussionNotFoundResource"
 *     )
 * )
 */
class ChatDiscussionNotFoundResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"Chat message is not found"}
     * )
     */
    public $data;
}
