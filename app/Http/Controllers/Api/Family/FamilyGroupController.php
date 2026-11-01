<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Services\Family\FamilyGroupService;
use App\Swagger\Api\Family\FamilyGroupController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class FamilyGroupController extends Controller
{
    public function __construct(
        private FamilyGroupService $familyGroupService,
    ) {}

    #[Swagger\Create]
    public function create(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $group = $this->familyGroupService->create();
            DB::commit();

            return response()->json([
                'status'    => true,
                'data'      => $group,
                'message'   => 'Группа успешно создана.',
            ], Response::HTTP_CREATED);

        } catch (HandledException $exception) {

            DB::rollBack();
            return $this->error($exception);
        }
    }

    #[Swagger\Show]
    public function show(): JsonResponse
    {
        try {
            $group = $this->familyGroupService->get();

            return response()->json([
                'status'    => true,
                'data'      => $group,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
