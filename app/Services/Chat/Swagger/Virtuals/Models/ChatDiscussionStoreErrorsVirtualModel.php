<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatDiscussionStoreErrorsVirtualModel",
 *     description="Chat Discussion Store Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatDiscussionStoreErrorsVirtualModel"
 *     )
 * )
 */
class ChatDiscussionStoreErrorsVirtualModel
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
