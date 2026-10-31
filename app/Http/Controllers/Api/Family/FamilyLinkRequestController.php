<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Family\Group\{
    FamilyLinkRequestStoreRequest,
};
use App\Services\Family\FamilyLinkRequestService;
use App\Swagger\Api\Family\FamilyLinkRequestController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class FamilyLinkRequestController extends Controller
{
    public function __construct(
        private readonly FamilyLinkRequestService $familyLinkRequestService,
    ) {
    }

    #[Swagger\Index]
    public function index(): JsonResponse
    {
        try {
            $requests = $this->familyLinkRequestService->getPendingRequests();

            return response()->json([
                'status'    => true,
                'data'      => $requests,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Store]
    public function store(FamilyLinkRequestStoreRequest $request): JsonResponse
    {
        $token = strval($request->input('token'));

        try {
            $linkRequest = $this->familyLinkRequestService->createRequestToJoinGroup($token);

            return response()->json([
                'status'    => true,
                'data'      => $linkRequest,
                'message'   => 'Обращение успешно создано.',
            ], Response::HTTP_CREATED);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\AddByUserId]
    public function addByUserId(int $userId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->familyLinkRequestService->addByUserId($userId);
            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => 'Пользователь успешно принят в группу.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {

            DB::rollBack();
            return $this->error($exception);
        }
    }
}
