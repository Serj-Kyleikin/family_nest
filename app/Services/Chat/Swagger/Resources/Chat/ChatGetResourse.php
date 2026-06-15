<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ChatGetResourse",
 *     description="Получить чат",
 *     @OA\Xml(
 *         name="ChatGetResourse"
 *     )
 * )
 */
class ChatGetResourse extends JsonResource
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
     *      @OA\Items(ref="#/components/schemas/ChatWithLastMessageAndMembersAndCountNotReaden")
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
