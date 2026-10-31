<?php

namespace App\Swagger\Api\Chat\ChatController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/chat/{chat_id}',
            operationId:      'chatShow',
            tags:             ['Chat'],

            summary:      'Получить чат',
            description:  'Возвращает чат с сообщениями и участниками, если текущий пользователь является его участником.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'chat_id',
                    in:           'path',
                    required:     true,
                    description:  'ID чата',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      5
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Чат успешно получен',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/ChatWithDiscussionsAndMembers'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь не является участником чата'),
            ]
        );
    }
}
