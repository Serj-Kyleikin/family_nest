<?php

namespace App\Swagger\Api\Family\FamilyMemberController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/members/{user_id}',
            operationId:      'familyMemberDestroy',
            tags:             ['Family'],

            summary:      'Исключить участника из семейной группы',
            description:  'Исключает участника из семейной группы текущего пользователя.',

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
                    description:  'Участник успешно исключён',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Участник успешно исключён.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа или участник не найдены'),
            ]
        );
    }
}
