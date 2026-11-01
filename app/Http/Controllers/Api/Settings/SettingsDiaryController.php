<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\{
    SettingsDiaryUpdateRequest,
};
use App\Services\Settings\SettingsDiaryService;
use App\Swagger\Api\Settings\SettingsDiaryController as Swagger;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class SettingsDiaryController extends Controller
{
    public function __construct(
        private readonly SettingsDiaryService $settingsDiaryService,
    ) {
    }

    #[Swagger\Show]
    public function show(): JsonResponse
    {
        try {
            $members = $this->settingsDiaryService->get();

            return response()->json([
                'status'    => true,
                'data'      => $members,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(SettingsDiaryUpdateRequest $request): JsonResponse
    {
        $userIds = array_map('intval', $request->array('user_ids'));

        try {
            DB::beginTransaction();
            $members = $this->settingsDiaryService->update($userIds);
            DB::commit();

            return response()->json([
                'status'    => true,
                'data'      => $members,
                'message'   => 'Настройки ежедневника успешно сохранены.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {

            DB::rollBack();
            return $this->error($exception);
        }
    }
}
