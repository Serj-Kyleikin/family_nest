<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatDiscussionDeleteFileNotAcceptableResource",
 *     description="Chat Message file delete Not Acceptablt resourse",
 *     @OA\Xml(
 *         name="ChatDiscussionDeleteFileNotAcceptableResource"
 *     )
 * )
 */
class ChatDiscussionDeleteFileNotAcceptableResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"This user doesn't have such filenames related with this discussion"}
     * )
     */
    public $data;
}
