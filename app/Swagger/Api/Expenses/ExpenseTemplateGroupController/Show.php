<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Show extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups/{id}',
            operationId:      'expenseTemplateGroupShow',
            tags:             ['Expenses'],

            summary:      'Получить группу шаблонов расходов',
            description:  'Возвращает категорию расходов текущего пользователя или общую категорию.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'id',
                    in:           'path',
                    required:     true,
                    description:  'ID группы шаблонов расходов',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      1
                ),
            ],

            responses: [
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
