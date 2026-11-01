<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Family\Group\FamilyMemberUpdateRequest;
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

    #[Swagger\Update]
    public function update(int $userId, FamilyMemberUpdateRequest $request): JsonResponse
    {
        $name = $request->filled('name') ? strval($request->input('name')) : null;

        try {
            $member = $this->familyMemberService->update($userId, $name);

            return response()->json([
                'status'    => true,
                'data'      => $member,
                'message'   => 'Имя участника успешно обновлено.',
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
