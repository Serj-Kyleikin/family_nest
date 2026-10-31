<?php

namespace App\Swagger\Api\Auth\AuthController;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class SignIn extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            path:             '/api/auth/signin',
            operationId:      'authSignIn',
            tags:             ['Auth'],

            summary:      'Авторизовать пользователя',
            description:  'Проверяет email и пароль пользователя и возвращает токен авторизации.',

            requestBody: new OA\RequestBody(
                required:   true,
                content:    new OA\JsonContent(
                                type:       'object',
                                required:   ['email', 'password'],
                                properties: [
                                                new OA\Property(property: 'email',    type: 'string', example: 'admin@mail.ru', format: 'email'),
                                                new OA\Property(property: 'password', type: 'string', example: '12341234',      format: 'password'),
                                            ]
                            )
            ),

            responses: [

                new OA\Response(
                    response:     200,
                    description:  'Пользователь успешно авторизован',
                    content:      new OA\JsonContent(
                                      type:       'object',
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
                                                      new OA\Property(property: 'message', type: 'string',  example: 'User authenticated successfully.'),
                                                  ]
                                  )
                ),

                new OA\Response(
                    response:     401,
                    description:  'Неверный email или пароль',
                    content:      new OA\JsonContent(
                                      type:       'object',
                                      properties: [
                                                      new OA\Property(property: 'status',  type: 'boolean', example: false),
                                                      new OA\Property(property: 'message', type: 'string',  example: 'Invalid credentials.'),
                                                  ]
                                  )
                ),

                new OA\Response(response: 422, description: 'Ошибка валидации'),
            ]
        );
    }
}
