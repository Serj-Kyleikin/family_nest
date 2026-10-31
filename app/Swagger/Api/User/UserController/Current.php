<?php

namespace App\Swagger\Api\User\UserController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Current extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/current',
            operationId:      'userCurrent',
            tags:             ['User'],

            summary:      'Получить текущего пользователя',
            description:  'Возвращает авторизованного пользователя с его семейной группой и признаком is_pending_request: true, если у пользователя есть необработанное обращение о принятии в группу. Если группы нет, поле group равно null.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Пользователь успешно получен',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/CurrentUser'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
