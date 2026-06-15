<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatDiscussionStoreErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatDiscussionStoreUnprocessableContentResource",
 *     description="Chat discussion store unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatDiscussionStoreUnprocessableContentResource"
 *     )
 * )
 */
class ChatDiscussionStoreUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatDiscussionStoreErrorsVirtualModel $errors;
}
