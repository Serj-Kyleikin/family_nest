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
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};
use OpenApi\Attributes as OA;

class ExpenseTemplateGroupController extends Controller
{
    public function __construct(
        private readonly ExpenseTemplateGroupService $expenseTemplateGroupService,
    ) 
    {
    }

    #[OA\Get(
        path: '/api/expenses/template/groups',
        operationId: 'expenseTemplateGroupList',
        summary: 'Список групп шаблонов расходов (с шаблонами)',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Groups list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                ref: '#/components/schemas/TemplateGroupWithTemplates'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
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

    #[OA\Post(
        path: '/api/expenses/template/groups',
        operationId: 'expenseTemplateGroupStore',
        summary: 'Создать группу шаблонов расходов',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Продукты'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Template group has been created'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/expenses/template/groups/{id}',
        operationId: 'expenseTemplateGroupShow',
        summary: 'Получить группу шаблонов расходов',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
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
                            ref: '#/components/schemas/TemplateGroup'
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
            $group = $this->expenseTemplateGroupService->get($id);

            return response()->json([
                'status'    => true,
                'data'      => $group,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Put(
        path: '/api/expenses/template/groups/{id}',
        operationId: 'expenseTemplateGroupUpdate',
        summary: 'Обновить группу шаблонов расходов',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Продукты'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Template group has been updated'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/expenses/template/groups/{id}',
        operationId: 'expenseTemplateGroupDestroy',
        summary: 'Удалить группу шаблонов расходов',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Template group has been deleted'),
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
