<?php

namespace App\Swagger\Api\Family\FamilyMemberController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/members/{user_id}',
            operationId:      'familyMemberShow',
            tags:             ['Family'],

            summary:      'Получить участника семейной группы',
            description:  'Возвращает участника семейной группы текущего пользователя.',

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

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Участник успешно получен',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/FamilyMember'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа или участник не найдены'),
            ]
        );
    }
}
