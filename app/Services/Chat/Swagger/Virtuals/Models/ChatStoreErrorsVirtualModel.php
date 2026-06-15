<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatStoreErrorsVirtualModel",
 *     description="Chat Store Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatStoreErrorsVirtualModel"
 *     )
 * )
 */
class ChatStoreErrorsVirtualModel
{
    /**
     * @OA\Property(
     *      property="medical_id",
     *      type="array",
     *      @OA\Items(example="The field must be required")
     * )
     */
    public string $medical_id;
}
