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

            summary:      'Список категорий расходов',
            description:  'Возвращает категории расходов текущего пользователя и общие категории, доступные всем (у общих user_id равен null). Шаблоны в ответ не входят, их возвращает отдельный запрос по ID категории. Порядок задаёт пользователь: сначала категории с приоритетом (меньшее число priority выше), затем категории без приоритета в порядке создания. Если пользователь ничего не настраивал, порядок стандартный, по созданию.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Категории успешно получены',
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
                                                                                        new OA\Property(property: 'id',       type: 'integer', example: 1),
                                                                                        new OA\Property(property: 'user_id',  type: 'integer', example: 5,          nullable: true),
                                                                                        new OA\Property(property: 'name',     type: 'string',  example: 'Продукты'),
                                                                                        new OA\Property(property: 'priority', type: 'integer', example: 1,          nullable: true),
                                                                                    ]
                                                                    )
                                                      ),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
