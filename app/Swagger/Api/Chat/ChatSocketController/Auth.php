<?php

namespace App\Swagger\Api\Chat\ChatSocketController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Auth extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/socket/auth',
            operationId:      'chatSocketAuth',
            tags:             ['Chat'],

            summary:      'Авторизовать подписку на приватный канал WebSocket',
            description:  'Возвращает подпись для подписки на канал private-user.{id} текущего пользователя или private-chat.{id} чата, в котором он состоит, в формате Pusher: app_key:signature.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['socket_id', 'channel_name'],
                                properties: [
                                                new OA\Property(property: 'socket_id',    type: 'string', example: '123456.654321', description: 'Идентификатор соединения из pusher:connection_established'),
                                                new OA\Property(property: 'channel_name', type: 'string', example: 'private-chat.1', description: 'Имя канала: private-user.{id} или private-chat.{id}'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Подпись успешно получена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property:   'data',
                                                          type:       'object',
                                                          properties: [
                                                                          new OA\Property(property: 'auth', type: 'string', example: 'a1b2c3d4e5f6a7b8c9d0:9f86d081884c7d659a2feaa0c55ad015'),
                                                                      ]
                                                      ),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Канал недоступен пользователю или пользователь не участник чата'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
