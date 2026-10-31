<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template',
            operationId:      'expenseTemplateStore',
            tags:             ['Expenses'],

            summary:      'Создать шаблон расхода',
            description:  'Создаёт шаблон расхода в группе шаблонов текущего пользователя.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['group_id', 'name'],
                                properties: [
                                                new OA\Property(property: 'group_id',  type: 'integer', example: 1),
                                                new OA\Property(property: 'name',      type: 'string',  example: 'Молоко'),
                                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Шаблон успешно создан',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Expense template has been created'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
