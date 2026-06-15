<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatDiscussionDeleteFileErrorsVirtualModel",
 *     description="Chat Discussion Is Read Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatDiscussionDeleteFileErrorsVirtualModel"
 *     )
 * )
 */
class ChatDiscussionDeleteFileErrorsVirtualModel
{
    /**
     * @OA\Property(
     *      property="file",
     *      type="array",
     *      @OA\Items(example="The field must be required")
     * )
     */
    public string $file;
}
