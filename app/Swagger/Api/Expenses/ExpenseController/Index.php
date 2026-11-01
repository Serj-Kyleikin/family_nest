<?php

namespace App\Swagger\Api\Expenses\ExpenseController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/expenses',
            operationId:      'expenseList',
            tags:             ['Expenses'],

            summary:      'Список расходов',
            description:  'Возвращает расходы текущего пользователя и тех участников его семейной группы, которые открыли ему доступ, от новых к старым. Страница задаётся параметрами limit и last_id: без last_id возвращается верх списка, с last_id только расходы старше записи с этим id.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'limit',
                    in:           'query',
                    required:     true,
                    description:  'Количество записей на странице, от 1 до 100',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      20
                ),
                new OA\Parameter(
                    name:         'last_id',
                    in:           'query',
                    required:     false,
                    description:  'ID последнего расхода предыдущей страницы',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      100
                ),
                new OA\Parameter(
                    name:         'date_from',
                    in:           'query',
                    required:     false,
                    description:  'Начало периода: возвращаются только расходы, созданные в этот момент или позже. Дата и время, например 2026-11-01T00:00:00Z. Не может быть позже date_to',
                    schema:       new OA\Schema(type: 'string', format: 'date-time'),
                    example:      '2026-11-01T00:00:00Z'
                ),
                new OA\Parameter(
                    name:         'date_to',
                    in:           'query',
                    required:     false,
                    description:  'Конец периода, не включительно: возвращаются только расходы, созданные раньше этого момента. Вместе с date_from задаёт окно, например сутки',
                    schema:       new OA\Schema(type: 'string', format: 'date-time'),
                    example:      '2026-11-02T00:00:00Z'
                ),
                new OA\Parameter(
                    name:         'user_ids',
                    in:           'query',
                    required:     false,
                    description:  'ID владельцев расходов, чтобы показать только их',
                    schema:       new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
                    example:      [1, 5]
                ),
                new OA\Parameter(
                    name:         'template_group_ids',
                    in:           'query',
                    required:     false,
                    description:  'ID групп шаблонов расходов, чтобы показать только расходы из них',
                    schema:       new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
                    example:      [1, 7]
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Расходы успешно получены',
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
                                                                                        new OA\Property(property: 'id',                  type: 'integer', example: 1),
                                                                                        new OA\Property(property: 'user_id',             type: 'integer', example: 5),
                                                                                        new OA\Property(property: 'user_name',           type: 'string',  example: 'Иван Иванов'),
                                                                                        new OA\Property(property: 'template_group_id',   type: 'integer', example: 1,        nullable: true),
                                                                                        new OA\Property(property: 'template_group_name', type: 'string',  example: 'Продукты', nullable: true),
                                                                                        new OA\Property(property: 'name',                type: 'string',  example: 'Проезд',   nullable: true),
                                                                                        new OA\Property(property: 'amount',              type: 'string',  example: '120.00'),
                                                                                        new OA\Property(property: 'created_at',          type: 'string',  example: '2026-11-01T12:00:00.000000Z', format: 'date-time'),
                                                                                    ]
                                                                    )
                                                      ),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации или дата начала позже даты окончания'),
            ]
        );
    }
}
