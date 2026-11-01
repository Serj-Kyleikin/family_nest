<?php

namespace App\Swagger\Api\Settings\SettingsDiaryController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/settings/diary',
            operationId:      'settingsDiaryUpdate',
            tags:             ['Settings'],

            summary:      'Изменить настройки доступа к ежедневнику',
            description:  'Полностью заменяет список участников семейной группы, которые видят ежедневник текущего пользователя. Пустой список закрывает доступ всем.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['user_ids'],
                                properties: [
                                                new OA\Property(property: 'user_ids', type: 'array', items: new OA\Items(type: 'integer'), example: [5, 7]),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Настройки успешно сохранены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property: 'data',
                                                          type:     'array',
                                                          items:    new OA\Items(
                                                                        type:       'object',
                                                                        properties: [
                                                                                        new OA\Property(property: 'id',         type: 'integer', example: 5),
                                                                                        new OA\Property(property: 'name',       type: 'string',  example: 'Иван Иванов'),
                                                                                        new OA\Property(property: 'is_allowed', type: 'boolean', example: true),
                                                                                    ]
                                                                    )
                                                      ),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Настройки ежедневника успешно сохранены.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа или участник не найдены'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
