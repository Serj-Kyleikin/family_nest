<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Services\Family\FamilyMemberService;
use App\Swagger\Api\Family\FamilyMemberController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\Http\{
    JsonResponse,
    Response,
};

class FamilyMemberController extends Controller
{
    public function __construct(
        private readonly FamilyMemberService $familyMemberService,
    ) {
    }

    #[Swagger\Index]
    public function index(): JsonResponse
    {
        try {
            $members = $this->familyMemberService->list();

            return response()->json([
                'status'    => true,
                'data'      => $members,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Show]
    public function show(int $userId): JsonResponse
    {
        try {
            $member = $this->familyMemberService->get($userId);

            return response()->json([
                'status'    => true,
                'data'      => $member,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Destroy]
    public function destroy(int $userId): JsonResponse
    {
        try {
            $this->familyMemberService->delete($userId);

            return response()->json([
                'status'    => true,
                'message'   => 'Участник успешно исключён.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
