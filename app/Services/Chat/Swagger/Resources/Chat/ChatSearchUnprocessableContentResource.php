<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\UnprocessableContentTrait;
use App\Services\Chat\Swagger\Virtuals\Models\ChatSearchErrorsVirtualModel;

/**
 * @OA\Schema(
 *     title="ChatSearchUnprocessableContentResource",
 *     description="Chat search unprocessable content resource",
 *     @OA\Xml(
 *         name="ChatSearchUnprocessableContentResource"
 *     )
 * )
 */
class ChatSearchUnprocessableContentResource
{
    use UnprocessableContentTrait;

    /**
     * @OA\Property(
     *     title="errors",
     *     description="errors",
     * )
     */
    public ChatSearchErrorsVirtualModel $errors;
}
