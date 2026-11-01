<?php

namespace App\Swagger\Api\User\UserDataController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/data',
            operationId:      'userDataUpdate',
            tags:             ['User'],

            summary:      'Изменить данные пользователя',
            description:  'Обновляет только переданные поля публикуемых данных текущего пользователя и возвращает актуальные данные. Значение null очищает поле.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                properties: [
                                                new OA\Property(property: 'status',        type: 'string', example: 'На даче до воскресенья', nullable: true),
                                                new OA\Property(property: 'family_status', type: 'string', example: 'Мама',                    nullable: true),
                                                new OA\Property(property: 'avatar',        type: 'string', example: '/storage/avatars/3.jpg',  nullable: true),
                                                new OA\Property(property: 'birthday',      type: 'string', example: '1990-05-17',              nullable: true, format: 'date'),
                                                new OA\Property(property: 'address',       type: 'string', example: 'Москва, ул. Ленина, 1',   nullable: true),
                                                new OA\Property(property: 'phone',         type: 'string', example: '+7 900 123-45-67',        nullable: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Данные успешно обновлены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'data',    type: 'object',  ref: '#/components/schemas/UserData'),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Данные пользователя успешно обновлены.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
