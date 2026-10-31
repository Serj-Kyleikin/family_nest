<?php

namespace App\Swagger\Api\Chat\ChatController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat',
            operationId:      'chatStore',
            tags:             ['Chat'],

            summary:      'Создать чат или получить ранее созданный чат',
            description:  'Создаёт чат текущего пользователя с выбранным пользователем или возвращает ранее созданный чат.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['user_to_id'],
                                properties: [
                                                new OA\Property(property: 'user_to_id', type: 'integer', example: 1, description: 'Id пользователя с кем создаётся чат'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Чат успешно создан',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/ChatWithDiscussionsAndMembers'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Попытка создать чат с самим собой'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
