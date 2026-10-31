<?php

namespace App\Swagger\Api\Chat\ChatSocketController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Config extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/socket',
            operationId:      'chatSocketConfig',
            tags:             ['Chat'],

            summary:      'Получить ключ WebSocket-сервера',
            description:  'Возвращает публичный ключ приложения для подключения к WebSocket-серверу по адресу /app/{app_key}.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Ключ успешно получен',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property:   'data',
                                                          type:       'object',
                                                          properties: [
                                                                          new OA\Property(property: 'app_key', type: 'string', example: 'a1b2c3d4e5f6a7b8c9d0'),
                                                                      ]
                                                      ),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
