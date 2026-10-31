<?php

namespace App\Swagger\Api\Chat\ChatDiscussionController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}/discussion/{discussion_id}',
            operationId:      'chatDiscussionUpdate',
            tags:             ['Chat'],

            summary:      'Обновить сообщение в чате',
            description:  'Изменяет текст сообщения в чате.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'chat_id',
                    in:           'path',
                    required:     true,
                    description:  'ID чата',
                    schema:       new OA\Schema(type: 'integer', example: 1)
                ),
                new OA\Parameter(
                    name:         'discussion_id',
                    in:           'path',
                    required:     true,
                    description:  'ID сообщения',
                    schema:       new OA\Schema(type: 'integer', example: 15)
                ),
            ],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\MediaType(
                                mediaType:  'multipart/form-data',
                                schema:     new OA\Schema(
                                                properties: [
                                                                new OA\Property(property: 'text', type: 'string', example: 'Hello'),
                                                            ]
                                            )
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Сообщение успешно обновлено',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Chat message has been updated'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь не является участником чата'),
                new OA\Response(response: 404, description: 'Сообщение не найдено'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
