<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups',
            operationId:      'expenseTemplateGroupList',
            tags:             ['Expenses'],

            summary:      'Список групп шаблонов расходов (с шаблонами)',
            description:  'Возвращает группы шаблонов расходов текущего пользователя вместе с их шаблонами (у шаблона есть цена price, может быть null).',

            security:         [['bearerAuth' => []]],

            responses: [
                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
