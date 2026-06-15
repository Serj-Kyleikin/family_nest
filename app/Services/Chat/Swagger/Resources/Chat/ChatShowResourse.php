<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatShowResourse",
 *     description="Получить чат",
 *     @OA\Xml(
 *         name="ChatShowResourse"
 *     )
 * )
 */
class ChatShowResourse extends JsonResource
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
     *      ref="#/components/schemas/ChatWithMembersAndDiscussions"
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
