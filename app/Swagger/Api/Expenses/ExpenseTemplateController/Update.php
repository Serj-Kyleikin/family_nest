<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/{id}',
            operationId:      'expenseTemplateUpdate',
            tags:             ['Expenses'],

            summary:      'Обновить шаблон расхода',
            description:  'Изменяет шаблон расхода текущего пользователя.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'id',
                    in:           'path',
                    required:     true,
                    description:  'ID шаблона расхода',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      10
                ),
            ],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['group_id', 'name', 'is_active'],
                                properties: [
                                                new OA\Property(property: 'group_id',  type: 'integer', example: 1),
                                                new OA\Property(property: 'name',      type: 'string',  example: 'Молоко'),
                                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Шаблон успешно обновлён',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Expense template has been updated'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Шаблон не найден'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
