<?php

namespace App\Swagger\Api\Expenses\ExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses/template/groups',
            operationId:      'expenseTemplateGroupStore',
            tags:             ['Expenses'],

            summary:      'Создать группу шаблонов расходов',
            description:  'Создаёт группу шаблонов расходов текущего пользователя.',

            security:         [['bearerAuth' => []]],

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
                    response:     201,
                    description:  'Группа успешно создана',
                    content:      new OA\JsonContent(
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Категория создана'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
