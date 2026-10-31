<?php

namespace App\Swagger\Api\Auth\AuthController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class SignUp extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/auth/signup',
            operationId:      'authSignUp',
            tags:             ['Auth'],

            summary:      'Зарегистрировать пользователя',
            description:  'Создаёт нового пользователя и возвращает токен авторизации. Если передан join_token, пользователь сразу добавляется в семейную группу активной ссылки-приглашения без создания обращения о принятии. Если ссылка не найдена или неактивна, join_token игнорируется и пользователь создаётся без группы.',

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['name', 'email', 'password'],
                                properties: [
                                                new OA\Property(property: 'name',       type: 'string', example: 'Иван Иванов'),
                                                new OA\Property(property: 'email',      type: 'string', example: 'ivan@example.com', format: 'email'),
                                                new OA\Property(property: 'password',   type: 'string', example: 'password123',      format: 'password'),
                                                new OA\Property(property: 'join_token', type: 'string', example: 'k3J9xQ2mV8aLpR5tZ1wY7cBn4dFhGs6uEoXiAjKq', nullable: true),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     201,
                    description:  'Пользователь успешно зарегистрирован',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      required:   ['status', 'message'],
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: true),
                                                      new OA\Property(
                                                          property:   'data',
                                                          type:       'object',
                                                          properties: [
                                                                          new OA\Property(property: 'token',      type: 'string', example: '1|plainTextTokenExample'),
                                                                          new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
                                                                      ]
                                                      ),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'User registered successfully.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
