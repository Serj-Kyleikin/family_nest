<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Diary\{
    UserDiaryIndexRequest,
    UserDiaryStoreRequest,
    UserDiaryUpdateRequest,
};
use App\Services\Diary\DiaryService;
use App\Swagger\Api\User\UserDiaryController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Carbon,
};

class UserDiaryController extends Controller
{
    public function __construct(
        private readonly DiaryService $diaryService,
    ) {
    }

    #[Swagger\Index]
    public function index(UserDiaryIndexRequest $request): JsonResponse
    {
        $userIds    = array_map('intval', $request->array('user_ids'));
        $dateFrom   = $request->filled('date_from') ? Carbon::parse($request->input('date_from')) : null;
        $dateTo     = $request->filled('date_to') ? Carbon::parse($request->input('date_to')) : null;

        try {
            $items = $this->diaryService->list($userIds, $dateFrom, $dateTo);

            return response()->json([
                'status'    => true,
                'data'      => $items,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(int $id, UserDiaryUpdateRequest $request): JsonResponse
    {
        $text       = strval($request->input('text'));
        $date       = Carbon::parse($request->input('date'));
        $dateEnd    = $request->filled('date_end') ? Carbon::parse($request->input('date_end')) : null;

        try {
            $entry = $this->diaryService->update($id, $text, $date, $dateEnd);

            return response()->json([
                'status'    => true,
                'data'      => $entry,
                'message'   => 'Запись обновлена',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Destroy]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->diaryService->delete($id);

            return response()->json([
                'status'    => true,
                'message'   => 'Запись удалена',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Store]
    public function store(UserDiaryStoreRequest $request): JsonResponse
    {
        $text       = strval($request->input('text'));
        $date       = Carbon::parse($request->input('date'));
        $dateEnd    = $request->filled('date_end') ? Carbon::parse($request->input('date_end')) : null;

        try {
            $entry = $this->diaryService->create($text, $date, $dateEnd);

            return response()->json([
                'status'    => true,
                'data'      => $entry,
                'message'   => 'Запись добавлена',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
