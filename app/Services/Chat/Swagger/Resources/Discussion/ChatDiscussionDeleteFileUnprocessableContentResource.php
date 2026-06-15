<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatDiscussionDeleteFileErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatDiscussionDeleteFileUnprocessableContentResource",
 *     description="Chat discussion file delete unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatDiscussionDeleteFileUnprocessableContentResource"
 *     )
 * )
 */
class ChatDiscussionDeleteFileUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatDiscussionDeleteFileErrorsVirtualModel $errors;
}
