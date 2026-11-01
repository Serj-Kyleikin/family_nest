<?php

namespace App\Swagger\Api\User\UserDiaryController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class Store extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/user/diary',
            operationId:      'userDiaryStore',
            tags:             ['User Diary'],

            summary:      'Создать запись ежедневника',
            description:  'Создаёт запись ежедневника текущего пользователя на сегодняшнюю или будущую дату и время начала (формат Y-m-d H:i) и возвращает созданную запись. Время окончания date_end необязательно и должно быть позже начала.',

            security:         [['bearerAuth' => []]],

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['text', 'date'],
                                properties: [
                                                new OA\Property(property: 'text',     type: 'string', example: 'Записаться к врачу'),
                                                new OA\Property(property: 'date',     type: 'string', example: '2026-11-05 14:30'),
                                                new OA\Property(property: 'date_end', type: 'string', example: '2026-11-05 15:30', nullable: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Запись успешно создана',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status', type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property: 'data',
                                                          type:     'object',
                                                          properties: [
                                                                          new OA\Property(property: 'id',       type: 'integer', example: 1),
                                                                          new OA\Property(property: 'user_id',  type: 'integer', example: 5),
                                                                          new OA\Property(property: 'text',     type: 'string',  example: 'Записаться к врачу'),
                                                                          new OA\Property(property: 'date',     type: 'string',  example: '2026-11-05 14:30'),
                                                                          new OA\Property(property: 'date_end', type: 'string',  example: '2026-11-05 15:30', nullable: true),
                                                                      ]
                                                      ),
                                                      new OA\Property(property: 'message', type: 'string', example: 'Запись добавлена'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 401, description: 'Пользователь не авторизован'),
                new OA\Response(response: 422, description: 'Ошибка валидации, дата в прошлом или время окончания не позже начала'),
            ]
        );
    }
}
