<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatDiscussionIsReadErrorsVirtualModel",
 *     description="Chat Discussion Is Read Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatDiscussionIsReadErrorsVirtualModel"
 *     )
 * )
 */
class ChatDiscussionIsReadErrorsVirtualModel
{
    /**
     * @OA\Property(
     *      property="ids",
     *      type="array",
     *      @OA\Items(example="The field must be required")
     * )
     */
    public string $ids;
}
