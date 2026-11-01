<?php

namespace App\Swagger\Api\User\UserDataController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/data',
            operationId:      'userDataShow',
            tags:             ['User'],

            summary:      'Получить данные пользователя',
            description:  'Возвращает публикуемые данные текущего пользователя: статус, статус в семье, аватар, день рождения, адрес и телефон. Если данные ещё не заполнялись, все поля равны null.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Данные успешно получены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',   type: 'object',  ref: '#/components/schemas/UserData'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
