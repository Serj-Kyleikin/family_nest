<?php

namespace App\Swagger\Api\Family\FamilyLinkController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/link',
            operationId:      'familyLinkShow',
            tags:             ['Family Link'],

            summary:      'Получить свою ссылку-приглашение',
            description:  'Возвращает действующую ссылку-приглашение семейной группы текущего пользователя.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Ссылка-приглашение успешно получена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/FamilyGroupLink'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа или ссылка не найдены'),
            ]
        );
    }
}
