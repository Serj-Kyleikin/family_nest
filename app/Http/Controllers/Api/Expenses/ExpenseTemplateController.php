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
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};
use OpenApi\Attributes as OA;

class ExpenseTemplateController extends Controller
{
    public function __construct(
        private readonly ExpenseTemplateService $expenseTemplateService,
    ) {
    }

    #[OA\Post(
        path: '/api/expenses/template',
        operationId: 'expenseTemplateStore',
        summary: 'Создать шаблон расхода',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['group_id', 'name'],
                properties: [
                    new OA\Property(property: 'group_id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Молоко'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Expense template has been created'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function store(TemplateStoreRequest $request): JsonResponse
    {
        $groupId    = intval($request->input('group_id'));
        $name       = strval($request->input('name'));
        $isActive   = boolval($request->boolean('is_active', true));

        try {
            $this->expenseTemplateService->create($groupId, $name, $isActive);

            return response()->json([
                'status'    => true,
                'message'   => 'Expense template has been created',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Get(
        path: '/api/expenses/template/{id}',
        operationId: 'expenseTemplateShow',
        summary: 'Получить шаблон расхода',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 10
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Group show',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            ref: '#/components/schemas/ExpenseTemplate'
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        try {
            $template = $this->expenseTemplateService->get($id);

            return response()->json([
                'status'    => true,
                'data'      => $template,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Put(
        path: '/api/expenses/template/{id}',
        operationId: 'expenseTemplateUpdate',
        summary: 'Обновить шаблон расхода',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 10
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['group_id', 'name', 'is_active'],
                properties: [
                    new OA\Property(property: 'group_id', type: 'integer', example: 1),
                    new OA\Property(property: 'name', type: 'string', example: 'Молоко'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Expense template has been updated'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(int $id, TemplateUpdateRequest $request): JsonResponse
    {
        $groupId    = intval($request->input('group_id'));
        $name       = strval($request->input('name'));
        $isActive   = boolval($request->boolean('is_active', true));

        try {
            $this->expenseTemplateService->update($id, $groupId, $name, $isActive);

            return response()->json([
                'status'    => true,
                'message'   => 'Expense template has been updated',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Delete(
        path: '/api/expenses/template/{id}',
        operationId: 'expenseTemplateDestroy',
        summary: 'Удалить шаблон расхода',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 10
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Expense template has been deleted'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->expenseTemplateService->delete($id);

            return response()->json([
                'status' => true,
                'message' => 'Expense template has been deleted',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
