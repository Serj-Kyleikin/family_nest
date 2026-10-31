<?php

namespace App\Swagger\Api\Expenses\ExpenseController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses',
            operationId:      'expenseList',
            tags:             ['Expenses'],

            summary:      'Список расходов',
            description:  'Возвращает список расходов текущего пользователя, при необходимости только по выбранной группе шаблонов.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'group_id',
                    in:           'query',
                    required:     false,
                    description:  'ID группы шаблонов расходов',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      1
                ),
            ],

            responses: [
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
