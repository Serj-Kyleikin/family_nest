<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatDiscussionUpdateErrorsVirtualModel",
 *     description="Chat Discussion Update Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatDiscussionUpdateErrorsVirtualModel"
 *     )
 * )
 */
class ChatDiscussionUpdateErrorsVirtualModel
{
    /**
     * @OA\Property(
     *      property="file",
     *      type="array",
     *      @OA\Items(example="Invalid data type")
     * )
     */
    public string $file;
}
