<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Services\Family\FamilyLinkService;
use App\Swagger\Api\Family\FamilyLinkController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class FamilyLinkController extends Controller
{
    public function __construct(
        private readonly FamilyLinkService $familyLinkService,
    ) {
    }

    #[Swagger\Create]
    public function create(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $link = $this->familyLinkService->create();
            DB::commit();

            return response()->json([
                'status'    => true,
                'data'      => $link,
                'message'   => 'Ссылка-приглашение успешно создана.',
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
            $link = $this->familyLinkService->get();

            return response()->json([
                'status'    => true,
                'data'      => $link,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}
