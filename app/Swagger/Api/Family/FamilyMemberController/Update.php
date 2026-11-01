<?php

namespace App\Swagger\Api\Family\FamilyMemberController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/members/{user_id}',
            operationId:      'familyMemberUpdate',
            tags:             ['Family'],

            summary:      'Изменить имя участника семейной группы',
            description:  'Задаёт alias участника, который видит только текущий пользователь. Пустое значение (null) сбрасывает alias, и участник отображается под своим именем из профиля. Изменить имя себе нельзя.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'user_id',
                    in:           'path',
                    required:     true,
                    description:  'ID участника группы',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      5
                ),
            ],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['name'],
                                properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Сын', nullable: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Имя участника успешно обновлено',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyMember'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Имя участника успешно обновлено.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 400, description: 'Нельзя изменить имя самому себе'),
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа или участник не найдены'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
