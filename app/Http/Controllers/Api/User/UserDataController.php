<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UserDataUpdateRequest;
use App\Services\User\UserDataService;
use App\Swagger\Api\User\UserDataController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\Http\{
    JsonResponse,
    Response,
};

class UserDataController extends Controller
{
    public function __construct(
        private readonly UserDataService $userDataService,
    ) {
    }

    #[Swagger\Show]
    public function show(): JsonResponse
    {
        try {
            $data = $this->userDataService->get();

            return response()->json([
                'status'    => true,
                'data'      => $data,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(UserDataUpdateRequest $request): JsonResponse
    {
        try {
            $data = $this->userDataService->update($request->validated());

            return response()->json([
                'status'    => true,
                'data'      => $data,
                'message'   => 'Данные пользователя успешно обновлены.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
