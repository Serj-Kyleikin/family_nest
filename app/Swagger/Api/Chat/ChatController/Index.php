<?php

namespace App\Swagger\Api\Chat\ChatController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat',
            operationId:      'chatList',
            tags:             ['Chat'],

            summary:      'Получить чаты пользователя с последним сообщением',
            description:  'Возвращает чаты текущего пользователя с последним сообщением и участниками.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Чаты успешно получены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(ref: '#/components/schemas/ChatWithLastDiscussionAndMembers')),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
