<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatDiscussionStoreResourse",
 *     description="Создать сообщение в чате",
 *     @OA\Xml(
 *         name="ChatDiscussionStoreResourse"
 *     )
 * )
 */
class ChatDiscussionStoreResourse extends JsonResource
{
    /**
     * @OA\Property(
     *      title="success",
     *      type="bool",
     *      description="",
     *      example=true
     * )
     */
    public array $success;

    /**
     * @OA\Property(
     *      title="data",
     *      description="",
     *      example=null
     * )
     */
    public string $data;

    /**
     * @OA\Property(
     *      title="message",
     *      description="",
     *      example="Discussion has been created"
     * )
     */
    public string $message;
}
