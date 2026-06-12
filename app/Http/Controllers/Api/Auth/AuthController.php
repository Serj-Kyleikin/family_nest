<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\{
    SignUpRequest,
    SignInRequest,
};
use App\Services\Auth\{
    AuthService,
    DTO\Factory\SignUpDTOFactory,
};
use App\Exceptions\HandledException;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[OA\Post(
        path: '/api/auth/signup',
        summary: 'Register new user',
        description: 'Creates a new user and returns API auth token.',
        operationId: 'authSignUp',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Ivan'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'ivan@example.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        example: 'password123'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User registered successfully.'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'token',
                                    type: 'string',
                                    example: '1|plainTextTokenExample'
                                ),
                                new OA\Property(
                                    property: 'token_type',
                                    type: 'string',
                                    example: 'Bearer'
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function signUp(SignUpRequest $request, SignUpDTOFactory $signUpDTOFactory): JsonResponse
    {
        try { 
            $userDTO    = $signUpDTOFactory->fromRequest($request);
            $token      = $this->authService->signUp($userDTO);

            return response()->json([
                'status'    => true,
                'message'   => 'User registered successfully.',
                'data'      => [
                    'token'         => $token,
                    'token_type'    => 'Bearer',
                ],
            ], 201);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Post(
        path: '/api/auth/signin',
        summary: 'Authenticate user',
        description: 'Authenticates user and returns API auth token.',
        operationId: 'authSignIn',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'admin@mail.ru'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        example: '12341234'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User authenticated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User authenticated successfully.'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'token',
                                    type: 'string',
                                    example: '1|plainTextTokenExample'
                                ),
                                new OA\Property(
                                    property: 'token_type',
                                    type: 'string',
                                    example: 'Bearer'
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: false
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Invalid credentials.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function signIn(SignInRequest $request): JsonResponse
    {
        $email      = $request->string('email')->value();
        $password   = $request->string('password')->value();

        try {
            $token = $this->authService->signIn($email, $password);

            return response()->json([
                'status'    => true,
                'message'   => 'User authenticated successfully.',
                'data'      => [
                    'token'         => $token,
                    'token_type'    => 'Bearer',
                ],
            ]);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
