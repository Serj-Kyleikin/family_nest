<?php

namespace App\Services\Chat\Swagger\Virtuals\Models;

/**
 * @OA\Schema(
 *     title="ChatSearchErrorsVirtualModel",
 *     description="Chat Search Errors Virtual Model",
 *     @OA\Xml(
 *         name="ChatSearchErrorsVirtualModel"
 *     )
 * )
 */
class ChatSearchErrorsVirtualModel
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
