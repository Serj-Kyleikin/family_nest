<?php

namespace App\Swagger\Api\Family\FamilyGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group',
            operationId:      'familyGroupShow',
            tags:             ['Family Group'],

            summary:      'Получить семейную группу',
            description:  'Возвращает семейную группу текущего пользователя.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно получена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/FamilyGroup'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
