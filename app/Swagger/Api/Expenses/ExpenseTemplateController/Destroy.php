<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/{id}',
            operationId:      'expenseTemplateDestroy',
            tags:             ['Expenses'],

            summary:      'Удалить шаблон расхода',
            description:  'Удаляет шаблон расхода текущего пользователя. Для общего шаблона удаляет копию семейной группы пользователя, после чего снова возвращается сам общий шаблон; удалить общий шаблон нельзя, а если копии не было, запрос тоже завершается успешно. Для общего шаблона нужна семейная группа.',

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

                new OA\Response(
                    response:     200,
                    description:  'Шаблон успешно удалён',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Шаблон удален'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Шаблон или семейная группа не найдены'),
            ]
        );
    }
}
