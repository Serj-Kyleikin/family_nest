<?php

namespace App\Swagger\Api\Chat\ChatDiscussionController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Search extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}/discussion/search',
            operationId:      'chatDiscussionSearch',
            tags:             ['Chat'],

            summary:      'Поиск сообщений в чате',
            description:  'Возвращает сообщения чата, в которых найден переданный текст или имя автора.',

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
                    name:         'text',
                    in:           'query',
                    required:     true,
                    description:  'Сообщение или имя для поиска',
                    schema:       new OA\Schema(type: 'string', example: 'Test')
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Сообщения успешно найдены',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(ref: '#/components/schemas/ChatDiscussionWithUser')),
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
