<?php

namespace App\SharedKernel\Traits\Swagger;

trait UnprocessableContentTrait
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
     *     example="bad_request"
     * )
     */
    public string $errorName;
}
