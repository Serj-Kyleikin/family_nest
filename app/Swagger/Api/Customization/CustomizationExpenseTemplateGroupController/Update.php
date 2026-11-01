<?php

namespace App\Swagger\Api\Customization\CustomizationExpenseTemplateGroupController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Update extends OA\Put
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/customization/expense-template-groups',
            operationId:      'customizationExpenseTemplateGroupUpdate',
            tags:             ['Customization'],

            summary:      'Сохранить порядок категории расходов',
            description:  'Задаёт текущему пользователю приоритет категории расходов: своей или общей. Чем меньше число priority, тем выше категория в списке категорий с шаблонами. Повторный запрос для той же категории заменяет приоритет.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['expense_template_group_id', 'priority'],
                                properties: [
                                                new OA\Property(property: 'expense_template_group_id', type: 'integer', example: 1),
                                                new OA\Property(property: 'priority',                  type: 'integer', example: 1),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Порядок категории успешно сохранён',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property: 'data',
                                                          type:     'object',
                                                          properties: [
                                                                          new OA\Property(property: 'id',                        type: 'integer', example: 1),
                                                                          new OA\Property(property: 'user_id',                   type: 'integer', example: 5),
                                                                          new OA\Property(property: 'expense_template_group_id', type: 'integer', example: 1),
                                                                          new OA\Property(property: 'priority',                  type: 'integer', example: 1),
                                                                      ]
                                                      ),
                                                      new OA\Property(property: 'message', type: 'string', example: 'Порядок категории сохранён'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Категория не найдена'),
                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
