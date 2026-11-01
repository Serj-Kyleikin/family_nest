<?php

namespace App\Swagger\Api\Expenses\ExpenseController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/{id}',
            operationId:      'expenseUpdate',
            tags:             ['Expenses'],

            summary:      'Обновить расход',
            description:  'Изменяет расход текущего пользователя. Необязательное поле date перезаписывает дату и время расхода, без него дата не меняется.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'id',
                    in:           'path',
                    required:     true,
                    description:  'ID расхода',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      1
                ),
            ],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['amount'],
                                properties: [
                                                new OA\Property(property: 'template_id',       type: 'integer', example: 10,                     nullable: true),
                                                new OA\Property(property: 'template_group_id', type: 'integer', example: 1,                      nullable: true),
                                                new OA\Property(property: 'name',              type: 'string',  example: 'Проезд',               nullable: true),
                                                new OA\Property(property: 'amount',            type: 'string',  example: '120'),
                                                new OA\Property(property: 'date',              type: 'string',  example: '2026-11-01T12:00:00Z', nullable: true, format: 'date-time'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Расход успешно обновлён',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Расход обновлен'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Расход не найден'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
