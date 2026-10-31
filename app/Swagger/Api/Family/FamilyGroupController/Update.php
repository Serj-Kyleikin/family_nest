<?php

namespace App\Swagger\Api\Family\FamilyGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group',
            operationId:      'familyGroupUpdate',
            tags:             ['Family Group'],

            summary:      'Изменить семейную группу',
            description:  'Изменяет название семейной группы текущего пользователя и возвращает обновлённую группу.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['name'],
                                properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Ивановы'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно обновлена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyGroup'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Группа успешно обновлена.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
