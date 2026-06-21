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
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};
use OpenApi\Attributes as OA;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly ExpenseService $expenseService,
    ) {
    }

    #[OA\Get(
        path: '/api/expenses',
        operationId: 'expenseList',
        summary: 'Список расходов',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'group_id',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Expenses list',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                ref: '#/components/schemas/ExpensesList'
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

    #[OA\Post(
        path: '/api/expenses',
        operationId: 'expenseStore',
        summary: 'Создать расход (template_id+amount | name+amount)',
        tags: ['Expenses'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'template_id', type: 'integer', example: 10, nullable: true),
                    new OA\Property(property: 'group_id', type: 'integer', example: 1, nullable: true),
                    new OA\Property(property: 'name', type: 'string', example: 'Проезд', nullable: true),
                    new OA\Property(property: 'amount', type: 'string', example: '120'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Expense has been created'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Template not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(ExpenseStoreRequest $request): JsonResponse
    {
        $groupId    = $request->input('group_id') !== null ? intval($request->input('group_id')) : null;
        $templateId = $request->input('template_id') !== null ? intval($request->input('template_id')) : null;
        $name       = $request->input('name') !== null ? strval($request->input('name')) : null;
        $amount     = intval($request->input('amount'));

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

    #[OA\Put(
        path: '/api/expenses/{id}',
        operationId: 'expenseUpdate',
        summary: 'Обновить расход',
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
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'template_id', type: 'integer', example: 10, nullable: true),
                    new OA\Property(property: 'group_id', type: 'integer', example: 1, nullable: true),
                    new OA\Property(property: 'name', type: 'string', example: 'Проезд', nullable: true),
                    new OA\Property(property: 'amount', type: 'string', example: '120'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Expense has been updated'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(int $id, ExpenseUpdateRequest $request): JsonResponse
    {
        $groupId    = $request->input('group_id') !== null ? intval($request->input('group_id')) : null;
        $templateId = $request->input('template_id') !== null ? intval($request->input('template_id')) : null;
        $name       = $request->input('name') !== null ? strval($request->input('name')) : null;
        $amount     = intval($request->input('amount'));

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

    #[OA\Delete(
        path: '/api/expenses/{id}',
        operationId: 'expenseDestroy',
        summary: 'Удалить расход',
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
                description: 'Updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Expense has been deleted'),
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
