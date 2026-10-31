<?php

namespace App\Http\Controllers\Api\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Expenses\TemplateGroups\{
    TemplateGroupStoreRequest,
    TemplateGroupUpdateRequest,
};
use App\Services\{
    Expenses\ExpenseTemplateGroupService,
};
use App\Swagger\Api\Expenses\ExpenseTemplateGroupController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};

class ExpenseTemplateGroupController extends Controller
{
    public function __construct(
        private readonly ExpenseTemplateGroupService $expenseTemplateGroupService,
    )
    {
    }

    #[Swagger\Index]
    public function index(): JsonResponse
    {
        try {
            $groups = $this->expenseTemplateGroupService->listWithTemplates();

            return response()->json([
                'status'    => true,
                'data'      => $groups,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Store]
    public function store(TemplateGroupStoreRequest $request): JsonResponse
    {
        $name = strval($request->input('name'));

        try {
            $this->expenseTemplateGroupService->create($name);

            return response()->json([
                'status'    => true,
                'message'   => 'Template group has been created',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Show]
    public function show(int $id): JsonResponse
    {
        try {
            $group = $this->expenseTemplateGroupService->get($id);

            return response()->json([
                'status'    => true,
                'data'      => $group,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(int $id, TemplateGroupUpdateRequest $request): JsonResponse
    {
        $name = strval($request->input('name'));

        try {
            $this->expenseTemplateGroupService->update($id, $name);

            return response()->json([
                'status'    => true,
                'message'   => 'Template group has been updated',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Destroy]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->expenseTemplateGroupService->delete($id);

            return response()->json([
                'status'    => true,
                'message'   => 'Template group has been deleted',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
