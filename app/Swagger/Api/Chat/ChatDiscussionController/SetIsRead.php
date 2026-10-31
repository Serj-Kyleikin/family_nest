<?php

namespace App\Swagger\Api\Chat\ChatDiscussionController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class SetIsRead extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}/discussion/mark_as_read',
            operationId:      'chatDiscussionMarkAsRead',
            tags:             ['Chat'],

            summary:      'Изменение статуса сообщений в чате на прочитано',
            description:  'Помечает переданные сообщения чата как прочитанные.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'chat_id',
                    in:           'path',
                    required:     true,
                    description:  'ID чата',
                    schema:       new OA\Schema(type: 'integer', example: 1)
                ),
            ],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['ids'],
                                properties: [
                                                new OA\Property(property: 'ids', type: 'array', description: 'ID сообщений', items: new OA\Items(type: 'integer', example: 1)),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Статус сообщений успешно изменён',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Discussions is_read status was updated'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 400, description: 'Нельзя изменить статус этих сообщений'),
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь не является участником чата'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
