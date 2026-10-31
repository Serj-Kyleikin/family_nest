<?php

namespace App\Swagger\Api\Expenses\ExpenseController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses',
            operationId:      'expenseStore',
            tags:             ['Expenses'],

            summary:      'Создать расход (template_id+amount | name+amount)',
            description:  'Создаёт расход текущего пользователя по шаблону (template_id) или по названию (name).',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['amount'],
                                properties: [
                                                new OA\Property(property: 'template_id', type: 'integer', example: 10,       nullable: true),
                                                new OA\Property(property: 'group_id',    type: 'integer', example: 1,        nullable: true),
                                                new OA\Property(property: 'name',        type: 'string',  example: 'Проезд', nullable: true),
                                                new OA\Property(property: 'amount',      type: 'string',  example: '120'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Расход успешно создан',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Expense has been created'),
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
