<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatDiscussionSearchErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatDiscussionSearchUnprocessableContentResource",
 *     description="Chat discussion search unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatDiscussionSearchUnprocessableContentResource"
 *     )
 * )
 */
class ChatDiscussionSearchUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatDiscussionSearchErrorsVirtualModel $errors;
}
