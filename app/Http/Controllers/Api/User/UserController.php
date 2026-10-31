<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use App\Swagger\Api\User\UserController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\Http\{
    JsonResponse,
    Response,
};

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    #[Swagger\Current]
    public function current(): JsonResponse
    {
        try {
            $user = $this->userService->getCurrent();

            return response()->json([
                'status'    => true,
                'data'      => $user,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
