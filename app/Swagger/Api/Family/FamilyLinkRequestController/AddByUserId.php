<?php

namespace App\Swagger\Api\Family\FamilyLinkRequestController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class AddByUserId extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/requests/{user_id}/add',
            operationId:      'familyLinkRequestAddByUserId',
            tags:             ['Family Link'],

            summary:      'Принять пользователя в семейную группу',
            description:  'Принимает в семейную группу текущего пользователя пользователя, который перешёл по ссылке-приглашению и создал обращение. Обращение получает статус done.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'user_id',
                    in:           'path',
                    required:     true,
                    description:  'ID пользователя, создавшего обращение',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      5
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Пользователь успешно принят в группу',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Пользователь успешно принят в группу.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь уже состоит в группе'),
                new OA\Response(response: 404, description: 'Пользователь, обращение или группа не найдены'),
            ]
        );
    }
}
