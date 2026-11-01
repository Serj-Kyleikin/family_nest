<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups/{id}',
            operationId:      'expenseTemplateGroupUpdate',
            tags:             ['Expenses'],

            summary:      'Обновить группу шаблонов расходов',
            description:  'Изменяет название группы шаблонов расходов текущего пользователя.',

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

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                required:   ['name'],
                                properties: [
                                                new OA\Property(property: 'name', type: 'string', example: 'Продукты'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Группа успешно обновлена',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Категория обновлена'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Группа не найдена'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
