<?php

namespace App\Http\Controllers\Api\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Expenses\Expenses\{
    ExpenseIndexRequest,
    ExpenseStoreRequest,
    ExpenseUpdateRequest,
};
use App\Services\{
    Expenses\ExpenseService,
};
use App\Swagger\Api\Expenses\ExpenseController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Carbon,
};

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService,
    ) {
    }

    #[Swagger\Index]
    public function index(ExpenseIndexRequest $request): JsonResponse
    {
        $userIds            = array_map('intval', $request->array('user_ids'));
        $templateGroupIds   = array_map('intval', $request->array('template_group_ids'));
        $lastId             = $request->filled('last_id') ? intval($request->input('last_id')) : null;
        $dateFrom           = $request->filled('date_from') ? Carbon::parse($request->input('date_from')) : null;
        $dateTo             = $request->filled('date_to') ? Carbon::parse($request->input('date_to')) : null;
        $limit              = intval($request->input('limit'));

        try {
            $items = $this->expenseService->list($limit, $lastId, $userIds, $templateGroupIds, $dateFrom, $dateTo);

            return response()->json([
                'status'    => true,
                'data'      => $items,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Store]
    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        $templateGroupId = $request->input('template_group_id') !== null ? intval($request->input('template_group_id')) : null;
        $templateId      = $request->input('template_id')       !== null ? intval($request->input('template_id'))       : null;
        $name            = $request->input('name')              !== null ? strval($request->input('name'))              : null;
        $amount          = strval($request->input('amount'));
        $date            = $request->filled('date') ? Carbon::parse($request->input('date')) : null;

        try {
            $this->expenseService->create($templateId, $name, $amount, $templateGroupId, $date);

            return response()->json([
                'status'    => true,
                'message'   => 'Расход добавлен',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(int $id, ExpenseUpdateRequest $request): JsonResponse
    {
        $templateGroupId = $request->input('template_group_id') !== null ? intval($request->input('template_group_id')) : null;
        $templateId      = $request->input('template_id')       !== null ? intval($request->input('template_id'))       : null;
        $name            = $request->input('name')              !== null ? strval($request->input('name'))              : null;
        $amount          = strval($request->input('amount'));
        $date            = $request->filled('date') ? Carbon::parse($request->input('date')) : null;

        try {
            $this->expenseService->update($id, $templateId, $name, $amount, $templateGroupId, $date);

            return response()->json([
                'status'    => true,
                'message'   => 'Расход обновлен',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Destroy]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->expenseService->delete($id);

            return response()->json([
                'status'    => true,
                'message'   => 'Расход удален',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
