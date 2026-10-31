<?php

namespace App\Swagger\Api\Family\FamilyMemberController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/members',
            operationId:      'getFamilyGroupMembers',
            tags:             ['Family'],

            summary:      'Получить участников семейной группы',
            description:  'Возвращает всех участников семейной группы текущего пользователя.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Участники успешно получены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(ref: '#/components/schemas/FamilyMember')),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
