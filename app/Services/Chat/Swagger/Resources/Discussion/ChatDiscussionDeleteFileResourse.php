<?php

namespace App\Services\Chat\Swagger\Resources\Discussion;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatDiscussionDeleteFileResourse",
 *     description="Удалить файл в сообщении в чате",
 *     @OA\Xml(
 *         name="ChatDiscussionDeleteFileResourse"
 *     )
 * )
 */
class ChatDiscussionDeleteFileResourse extends JsonResource
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
     *      example="Chat message file has been deleted"
     * )
     */
    public string $message;

}
