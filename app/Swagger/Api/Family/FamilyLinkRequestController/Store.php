<?php

namespace App\Swagger\Api\Family\FamilyLinkRequestController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/family/group/join',
            operationId:      'familyLinkRequestStore',
            tags:             ['Family Link'],

            summary:      'Создать обращение о принятии в группу',
            description:  'Создаёт обращение о принятии в семейную группу по токену ссылки-приглашения от текущего пользователя, не состоящего в группе. Пока у пользователя есть необработанное обращение, создать новое нельзя.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['token'],
                                properties: [
                                                new OA\Property(property: 'token', type: 'string', example: 'k3J9xQ2mV8aLpR5tZ1wY7cBn4dFhGs6uEoXiAjKq'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Обращение успешно создано',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/FamilyGroupLinkRequest'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Обращение успешно создано.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 403, description: 'Пользователь уже состоит в группе или у него уже есть созданное необработанное обращение'),
                new OA\Response(response: 404, description: 'Ссылка не найдена'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
