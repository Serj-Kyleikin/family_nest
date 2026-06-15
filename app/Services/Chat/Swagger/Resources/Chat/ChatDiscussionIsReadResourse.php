<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatDiscussionIsReadResourse",
 *     description="Изменение статуса сообщений в чате на прочитано",
 *     @OA\Xml(
 *         name="ChatDiscussionIsReadResourse"
 *     )
 * )
 */
class ChatDiscussionIsReadResourse extends JsonResource
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
     *      property="data",
     *      description="",
     *      type="integer",
     *      example=null
     * )
     */
    public string $data;

    /**
     * @OA\Property(
     *      title="message",
     *      description="",
     *      example="Discussions is_read status was updated"
     * )
     */
    public string $message;
}
