<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\{
    SettingsExpensesUpdateRequest,
};
use App\Services\Settings\SettingsExpensesService;
use App\Swagger\Api\Settings\SettingsExpensesController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class SettingsExpensesController extends Controller
{
    public function __construct(
        private readonly SettingsExpensesService $settingsExpensesService,
    ) {
    }

    #[Swagger\Show]
    public function show(): JsonResponse
    {
        try {
            $members = $this->settingsExpensesService->get();

            return response()->json([
                'status'    => true,
                'data'      => $members,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(SettingsExpensesUpdateRequest $request): JsonResponse
    {
        $userIds = array_map('intval', $request->array('user_ids'));

        try {
            DB::beginTransaction();
            $members = $this->settingsExpensesService->update($userIds);
            DB::commit();

            return response()->json([
                'status'    => true,
                'data'      => $members,
                'message'   => 'Настройки расходов успешно сохранены.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {

            DB::rollBack();
            return $this->error($exception);
        }
    }
}
