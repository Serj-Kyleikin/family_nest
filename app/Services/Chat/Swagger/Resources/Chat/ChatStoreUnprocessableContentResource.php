<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatStoreErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatStoreUnprocessableContentResource",
 *     description="Chat store unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatStoreUnprocessableContentResource"
 *     )
 * )
 */
class ChatStoreUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatStoreErrorsVirtualModel $errors;
}
