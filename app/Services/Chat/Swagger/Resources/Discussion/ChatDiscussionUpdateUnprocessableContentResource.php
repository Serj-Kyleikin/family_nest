<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatDiscussionUpdateErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatDiscussionUpdateUnprocessableContentResource",
 *     description="Chat update unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatDiscussionUpdateUnprocessableContentResource"
 *     )
 * )
 */
class ChatDiscussionUpdateUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatDiscussionUpdateErrorsVirtualModel $errors;
}
