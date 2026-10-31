<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/{id}',
            operationId:      'expenseTemplateShow',
            tags:             ['Expenses'],

            summary:      'Получить шаблон расхода',
            description:  'Возвращает шаблон расхода текущего пользователя.',

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

            responses: [
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Шаблон не найден'),
            ]
        );
    }
}
