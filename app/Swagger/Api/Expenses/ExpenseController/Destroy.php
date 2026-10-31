<?php

namespace App\Swagger\Api\Expenses\ExpenseController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/{id}',
            operationId:      'expenseDestroy',
            tags:             ['Expenses'],

            summary:      'Удалить расход',
            description:  'Удаляет расход текущего пользователя.',

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

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Расход успешно удалён',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Expense has been deleted'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Расход не найден'),
            ]
        );
    }
}
