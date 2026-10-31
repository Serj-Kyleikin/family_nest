<?php

namespace App\SharedKernel\Traits\Swagger;

trait ErrorResponseTrait
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
}
