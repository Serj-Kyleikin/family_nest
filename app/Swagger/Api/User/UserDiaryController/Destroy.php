<?php

namespace App\Swagger\Api\User\UserDiaryController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Destroy extends OA\Delete
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/diary/{id}',
            operationId:      'userDiaryDestroy',
            tags:             ['User Diary'],

            summary:      'Удалить запись ежедневника',
            description:  'Удаляет запись ежедневника текущего пользователя. Чужие записи удалить нельзя.',

            security:         [['bearerAuth' => []]],

            parameters: [
                new OA\Parameter(
                    name:         'id',
                    in:           'path',
                    required:     true,
                    description:  'ID записи ежедневника',
                    schema:       new OA\Schema(type: 'integer'),
                    example:      1
                ),
            ],

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Запись успешно удалена',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Запись удалена'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 404, description: 'Запись не найдена или принадлежит другому пользователю'),
            ]
        );
    }
}
