<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatSearchResourse",
 *     description="Поиск по чатам",
 *     @OA\Xml(
 *         name="ChatSearchResourse"
 *     )
 * )
 */
class ChatSearchResourse extends JsonResource
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
     *      @OA\Items(ref="#/components/schemas/ChatWithLastMessageAndMembers")
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
