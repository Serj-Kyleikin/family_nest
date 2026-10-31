<?php

namespace App\Swagger\Api\Chat\ChatDiscussionController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}/discussion',
            operationId:      'chatDiscussionStore',
            tags:             ['Chat'],

            summary:      'Создать сообщение в чате',
            description:  'Создаёт сообщение в чате от имени текущего пользователя.',

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
                    response:     201,
                    description:  'Сообщение успешно создано',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Chat message has been created'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь не является участником чата'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
