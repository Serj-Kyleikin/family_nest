<?php

namespace App\Swagger\Api\Chat\ChatController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Search extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/search',
            operationId:      'chatSearch',
            tags:             ['Chat'],

            summary:      'Поиск чатов по тексту сообщений',
            description:  'Возвращает чаты текущего пользователя, в сообщениях которых найден переданный текст.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'text',
                    in:           'query',
                    required:     true,
                    description:  'Текст для поиска в сообщениях',
                    schema:       new OA\Schema(type: 'string'),
                    example:      'привет'
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Чаты успешно найдены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(ref: '#/components/schemas/ChatWithLastDiscussionAndMembers')),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
