<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatDiscussionUpdateResourse",
 *     description="Обновить сообщение в чате",
 *     @OA\Xml(
 *         name="ChatDiscussionUpdateResourse"
 *     )
 * )
 */
class ChatDiscussionUpdateResourse extends JsonResource
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
     *      type="object",
     *      ref="#/components/schemas/ChatDiscussionModel"
     * )
     */
    public string $data;

    /**
     * @OA\Property(
     *      title="message",
     *      description="",
     *      example="Chat message has been updated"
     * )
     */
    public string $message;

}
