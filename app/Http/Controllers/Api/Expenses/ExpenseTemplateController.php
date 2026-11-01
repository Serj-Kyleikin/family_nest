<?php

namespace App\Http\Controllers\Api\Expenses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Expenses\Templates\{
    TemplateStoreRequest,
    TemplateUpdateRequest,
};
use App\Services\{
    Expenses\ExpenseTemplateService,
};
use App\Swagger\Api\Expenses\ExpenseTemplateController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};

class ExpenseTemplateController extends Controller
{
    public function __construct(
        private readonly ExpenseTemplateService $expenseTemplateService,
    ) {
    }

    #[Swagger\Store]
    public function store(TemplateStoreRequest $request): JsonResponse
    {
        $templateGroupId = intval($request->input('template_group_id'));
        $name            = strval($request->input('name'));
        $price           = $request->filled('price') ? strval($request->input('price')) : null;
        $isActive        = boolval($request->boolean('is_active', true));

        try {
            $this->expenseTemplateService->create($templateGroupId, $name, $price, $isActive);

            return response()->json([
                'status'    => true,
                'message'   => 'Шаблон создан',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Index]
    public function index(int $id): JsonResponse
    {
        try {
            $templates = $this->expenseTemplateService->list($id);

            return response()->json([
                'status'    => true,
                'data'      => $templates,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Show]
    public function show(int $id): JsonResponse
    {
        try {
            $template = $this->expenseTemplateService->show($id);

            return response()->json([
                'status'    => true,
                'data'      => $template,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Update]
    public function update(int $id, TemplateUpdateRequest $request): JsonResponse
    {
        $templateGroupId = intval($request->input('template_group_id'));
        $name            = strval($request->input('name'));
        $price           = $request->filled('price') ? strval($request->input('price')) : null;
        $isActive        = boolval($request->boolean('is_active', true));

        try {
            $this->expenseTemplateService->update($id, $templateGroupId, $name, $price, $isActive);

            return response()->json([
                'status'    => true,
                'message'   => 'Шаблон обновлён',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[Swagger\Destroy]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->expenseTemplateService->delete($id);

            return response()->json([
                'status'    => true,
                'message'   => 'Шаблон удален',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
