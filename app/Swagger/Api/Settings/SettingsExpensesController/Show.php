<?php

namespace App\Swagger\Api\Settings\SettingsExpensesController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/settings/expenses',
            operationId:      'settingsExpensesShow',
            tags:             ['Settings'],

            summary:      'Получить настройки доступа к расходам',
            description:  'Возвращает участников семейной группы текущего пользователя, кроме него самого, и признак, видит ли участник его расходы.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Настройки успешно получены',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
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
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
