<?php

namespace App\Swagger\Api\Family\FamilyGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group',
            operationId:      'familyGroupDestroy',
            tags:             ['Family Group'],

            summary:      'Удалить семейную группу',
            description:  'Удаляет семейную группу текущего пользователя, отвязывает от неё всех участников и удаляет чаты, в которых состоят только участники этой группы.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно удалена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Группа успешно удалена.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
