<?php

namespace App\Swagger\Api\Family\FamilyGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Create extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group',
            operationId:      'createFamilyGroup',
            tags:             ['Family Group'],

            summary:      'Создать семейную группу',
            description:  'Создаёт новую семейную группу, привязывает к ней текущего пользователя и возвращает созданную группу.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Группа успешно создана',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyGroup'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Группа успешно создана.'),
                                                  ]
                                  )
                ),

                new OA\Response(
                    response:     401,
                    description:  'Пользователь не авторизован',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 403, description: 'Группа уже создана'),
            ]
        );
    }
}
