<?php

namespace App\Swagger\Api\Family\FamilyLinkRequestController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/requests',
            operationId:      'familyLinkRequestIndex',
            tags:             ['Family Link'],

            summary:      'Получить обращения о принятии в группу',
            description:  'Возвращает необработанные обращения пользователей, перешедших по ссылке-приглашению семейной группы текущего пользователя.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Обращения успешно получены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'array',   items: new OA\Items(ref: '#/components/schemas/FamilyGroupLinkRequest')),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
