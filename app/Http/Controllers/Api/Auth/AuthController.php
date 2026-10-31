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
use App\Swagger\Api\Auth\AuthController as Swagger;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    #[Swagger\SignUp]
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

    #[Swagger\SignIn]
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
