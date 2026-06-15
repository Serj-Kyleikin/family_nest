<?php

namespace App\Services\Chat\Swagger\Resources\Chat;

use App\SharedKernel\Traits\Swagger\ErrorResponseTrait;

/**
 * @OA\Schema(
 *     title="ChatDiscussionIsReadBadRequestResource",
 *     description="Попытка обновить статус сообщений в чата, которые не принадлежат этому чату или принадлежат самому пользователю",
 *     @OA\Xml(
 *         name="ChatDiscussionIsReadBadRequestResource"
 *     )
 * )
 */
class ChatDiscussionIsReadBadRequestResource
{
    use ErrorResponseTrait;

    /**
     * @OA\Property(
     *     title="data",
     *     description="",
     *     example={"You can't update is_read status for this messages"}
     * )
     */
    public $data;
}
