<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatStoreResourse",
 *     description="Создать чат",
 *     @OA\Xml(
 *         name="ChatStoreResourse"
 *     )
 * )
 */
class ChatStoreResourse extends JsonResource
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
     *      @OA\Items(ref="#/components/schemas/ChatWithMembersAndDiscussions")
     * )
     */
    public string $data;

    /**
     * @OA\Property(
     *      title="message",
     *      description="",
     *      example="Chat data"
     * )
     */
    public string $message;
}
