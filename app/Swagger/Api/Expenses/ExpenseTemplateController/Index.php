<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups/{id}/templates',
            operationId:      'expenseTemplateList',
            tags:             ['Expenses'],

            summary:      'Список шаблонов расходов категории',
            description:  'Возвращает шаблоны выбранной категории: своей или общей. Для общего шаблона возвращаются значения копии, сохранённой семейной группой пользователя (is_customized равен true), иначе значения самого шаблона. Цена price всегда присутствует, при её отсутствии возвращается 0.00.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'id',
                    in:           'path',
                    required:     true,
                    description:  'ID категории расходов',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      1
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Шаблоны успешно получены',
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
                                                                                        new OA\Property(property: 'id',                type: 'integer', example: 10),
                                                                                        new OA\Property(property: 'template_group_id', type: 'integer', example: 1),
                                                                                        new OA\Property(property: 'name',              type: 'string',  example: 'Молоко'),
                                                                                        new OA\Property(property: 'price',             type: 'string',  example: '89.90'),
                                                                                        new OA\Property(property: 'is_active',         type: 'boolean', example: true),
                                                                                        new OA\Property(property: 'is_customized',     type: 'boolean', example: false),
                                                                                    ]
                                                                    )
                                                      ),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Категория не найдена'),
            ]
        );
    }
}
