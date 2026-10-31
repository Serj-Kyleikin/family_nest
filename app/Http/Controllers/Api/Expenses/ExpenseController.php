<?php

namespace App\Http\Controllers\Api\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Expenses\Expenses\{
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
};

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService,
    ) {
    }

    #[Swagger\Index]
    public function index(): JsonResponse
    {
        $groupId = request()->query('group_id')
            ? intval(request()->query('group_id'))
            : null;

        try {
            $items = $this->expenseService->list($groupId);

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
        $groupId    = $request->input('group_id') !== null ? intval($request->input('group_id')) : null;
        $templateId = $request->input('template_id') !== null ? intval($request->input('template_id')) : null;
        $name       = $request->input('name') !== null ? strval($request->input('name')) : null;
        $amount     = strval($request->input('amount'));

        try {
            $this->expenseService->create($templateId, $name, $amount, $groupId);

            return response()->json([
                'status'    => true,
                'message'   => 'Expense has been created',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(int $id, ExpenseUpdateRequest $request): JsonResponse
    {
        $groupId    = $request->input('group_id') !== null ? intval($request->input('group_id')) : null;
        $templateId = $request->input('template_id') !== null ? intval($request->input('template_id')) : null;
        $name       = $request->input('name') !== null ? strval($request->input('name')) : null;
        $amount     = strval($request->input('amount'));

        try {
            $this->expenseService->update($id, $templateId, $name, $amount, $groupId);

            return response()->json([
                'status'    => true,
                'message'   => 'Expense has been updated',
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
                'message'   => 'Expense has been deleted',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
