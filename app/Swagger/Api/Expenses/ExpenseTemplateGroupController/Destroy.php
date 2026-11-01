<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups/{id}',
            operationId:      'expenseTemplateGroupDestroy',
            tags:             ['Expenses'],

            summary:      'Удалить группу шаблонов расходов',
            description:  'Удаляет группу шаблонов расходов текущего пользователя.',

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

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно удалена',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Категория удалена'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
            ]
        );
    }
}
