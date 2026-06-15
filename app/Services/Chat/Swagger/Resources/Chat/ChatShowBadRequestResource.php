<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

/**
 * @OA\Schema(
 *     title="ChatShowBadRequestResource",
 *     description="Chat Show not found resourse",
 *     @OA\Xml(
 *         name="ChatShowBadRequestResource"
 *     )
 * )
 */
class ChatShowBadRequestResource
{
    /**
     * @OA\Property(
     *     title="success",
     *     type="bool",
     *     description="status",
     *     example="false"
     * )
     */
    public bool $success;

    /**
     * @OA\Property(
     *     title="errorCategory",
     *     description="error category",
     *     example=""
     * )
     */
    public string $errorCategory;

    /**
     * @OA\Property(
     *     title="errorName",
     *     description="error name",
     *     example=""
     * )
     */
    public string $errorName;

    /**
     * @OA\Property(
     *     title="errorVars",
     *     type="array",
     *     @OA\Items(type="string"),
     *     description="",
     *     example="[]"
     * )
     */
    public string $errorVars;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"This user isn't member of this chat"}
     * )
     */
    public $data;
}
