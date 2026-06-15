<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatDiscussionSearchErrorsVirtualModel",
 *     description="Chat Discussion Search Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatDiscussionSearchErrorsVirtualModel"
 *     )
 * )
 */
class ChatDiscussionSearchErrorsVirtualModel
{
    /**
     * @OA\Property(
     *      property="text",
     *      type="array",
     *      @OA\Items(example="The field is required")
     * )
     */
    public string $text;
}
