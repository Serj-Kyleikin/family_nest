<?php

namespace App\Swagger\Api\Family\FamilyLinkController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Create extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/link',
            operationId:      'familyLinkCreate',
            tags:             ['Family Link'],

            summary:      'Создать ссылку-приглашение',
            description:  'Создаёт ссылку-приглашение в семейную группу текущего пользователя. Ранее созданная ссылка перестаёт действовать.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Ссылка-приглашение успешно создана',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyGroupLink'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Ссылка-приглашение успешно создана.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
