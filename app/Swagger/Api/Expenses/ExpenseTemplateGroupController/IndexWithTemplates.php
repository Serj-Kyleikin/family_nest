<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class IndexWithTemplates extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups/with-templates',
            operationId:      'expenseTemplateGroupListWithTemplates',
            tags:             ['Expenses'],

            summary:      'Список категорий расходов с шаблонами',
            description:  'Возвращает категории расходов текущего пользователя и общие категории вместе со всеми их шаблонами. Для общего шаблона возвращаются значения копии, сохранённой семейной группой пользователя (is_customized равен true), иначе значения самого шаблона. Цена price всегда присутствует, при её отсутствии возвращается 0.00. Неактивные шаблоны тоже возвращаются с признаком is_active. Категории отсортированы по приоритету, который пользователь задал в настройке порядка: сначала меньшее число priority, затем категории без приоритета в порядке создания.',

            security:         [['bearerAuth' => []]],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Категории с шаблонами успешно получены',
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
                                                                                        new OA\Property(
                                                                                            property: 'templates',
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
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
            ]
        );
    }
}
