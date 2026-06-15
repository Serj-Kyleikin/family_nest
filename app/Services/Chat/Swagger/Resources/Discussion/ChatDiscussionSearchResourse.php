<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatDiscussionSearchResourse",
 *     description="Поиск по сообщениям в чате",
 *     @OA\Xml(
 *         name="ChatDiscussionSearchResourse"
 *     )
 * )
 */
class ChatDiscussionSearchResourse extends JsonResource
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
     *      type="array",
     *      @OA\Items(ref="#/components/schemas/ChatDiscussionWithChatAndUserShort")
     * )
     */
    public string $data;

    /**
     * @OA\Property(
     *      title="message",
     *      description="",
     *      example=null
     * )
     */
    public string $message;
}
