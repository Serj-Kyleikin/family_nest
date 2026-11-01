<?php

namespace App\Swagger\Api\User\UserDiaryController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Index extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/diary',
            operationId:      'userDiaryList',
            tags:             ['User Diary'],

            summary:      'Список записей ежедневника',
            description:  'Возвращает записи ежедневника текущего пользователя и тех участников его семейной группы, которые открыли ему доступ, от ранних дат и времени к поздним. Период задаётся необязательными параметрами date_from и date_to.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'date_from',
                    in:           'query',
                    required:     false,
                    description:  'Начало периода включительно, формат Y-m-d. Не может быть позже date_to',
                    schema:       new OA\Schema(type: 'string', format: 'date'),
                    example:      '2026-11-01'
                ),
                new OA\Parameter(
                    name:         'date_to',
                    in:           'query',
                    required:     false,
                    description:  'Конец периода включительно, формат Y-m-d',
                    schema:       new OA\Schema(type: 'string', format: 'date'),
                    example:      '2026-11-30'
                ),
                new OA\Parameter(
                    name:         'user_ids',
                    in:           'query',
                    required:     false,
                    description:  'ID владельцев записей, чтобы показать только их',
                    schema:       new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
                    example:      [1, 5]
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Записи успешно получены',
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
                                                                                        new OA\Property(property: 'id',        type: 'integer', example: 1),
                                                                                        new OA\Property(property: 'user_id',   type: 'integer', example: 5),
                                                                                        new OA\Property(property: 'user_name', type: 'string',  example: 'Иван Иванов'),
                                                                                        new OA\Property(property: 'text',      type: 'string',  example: 'Записаться к врачу'),
                                                                                        new OA\Property(property: 'date',      type: 'string',  example: '2026-11-05 14:30'),
                                                                                        new OA\Property(property: 'date_end',  type: 'string',  example: '2026-11-05 15:30', nullable: true),
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
