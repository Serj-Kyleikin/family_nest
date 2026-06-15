<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatDiscussionIsReadErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatDiscussionIsReadUnprocessableContentResource",
 *     description="Chat Discussion Is Read unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatDiscussionIsReadUnprocessableContentResource"
 *     )
 * )
 */
class ChatDiscussionIsReadUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatDiscussionIsReadErrorsVirtualModel $errors;
}
