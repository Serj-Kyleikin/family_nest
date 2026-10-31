<?php

namespace App\Swagger\Api\Chat\ChatDiscussionController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}/discussion/{discussion_id}',
            operationId:      'chatDiscussionDelete',
            tags:             ['Chat'],

            summary:      'Удалить сообщение в чате',
            description:  'Удаляет сообщение в чате.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'chat_id',
                    in:           'path',
                    required:     true,
                    description:  'ID чата',
                    schema:       new OA\Schema(type: 'integer', example: 5)
                ),
                new OA\Parameter(
                    name:         'discussion_id',
                    in:           'path',
                    required:     true,
                    description:  'ID сообщения',
                    schema:       new OA\Schema(type: 'integer', example: 15)
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Сообщение успешно удалено',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Chat message has been deleted'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь не является участником чата'),
                new OA\Response(response: 404, description: 'Сообщение не найдено'),
            ]
        );
    }
}
