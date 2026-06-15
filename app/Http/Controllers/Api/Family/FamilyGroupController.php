<?php

namespace App\Http\Controllers\Api\Family;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Family\Group\{
    AddMemberRequest,
};
use App\Services\Family\FamilyGroupService;
use App\Exceptions\HandledException;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};
use OpenApi\Attributes as OA;

class FamilyGroupController extends Controller
{
    public function __construct(
        private FamilyGroupService $familyGroupService,
    ) {}

    #[OA\Post(
        path: '/api/family/group',
        summary: 'Create family group',
        description: 'Creates a new family group.',
        operationId: 'createFamilyGroup',
        tags: ['Family'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Group created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Group created successfully.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 403,
                description: 'Group is already created'
            ),
        ]
    )]
    public function create(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->familyGroupService->create();
            DB::commit();

            return response()->json([
                'status'    => true,
                'message'   => 'Group created successfully.',
            ], Response::HTTP_CREATED);

        } catch (HandledException $exception) {

            DB::rollBack();
            return $this->error($exception);
        }
    }

    #[OA\Get(
        path: '/api/family/group/members',
        summary: 'Get family group members',
        description: 'Returns all members of the authenticated user\'s family group.',
        operationId: 'getFamilyGroupMembers',
        tags: ['Family'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Members retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(
                                        property: 'id',
                                        type: 'integer',
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: 'name',
                                        type: 'string',
                                        example: 'John Doe'
                                    ),
                                    new OA\Property(
                                        property: 'email',
                                        type: 'string',
                                        format: 'email',
                                        example: 'john.doe@example.com'
                                    ),
                                    new OA\Property(
                                        property: 'group_id',
                                        type: 'integer',
                                        example: 1
                                    ),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 404,
                description: 'Group not found'
            ),
        ]
    )]
    public function members(): JsonResponse
    {
        try {
            $members = $this->familyGroupService->getMembers();

            return response()->json([
                'status'    => true,
                'data'      => $members,
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Post(
        path: '/api/family/group/members/add',
        summary: 'Add member to family group',
        description: 'Adds a new member to the authenticated user\'s family group.',
        operationId: 'addFamilyGroupMember',
        tags: ['Family'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['user_id'],
                properties: [
                    new OA\Property(
                        property: 'user_id',
                        type: 'integer',
                        example: 5
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member added successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Member added successfully.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 404,
                description: 'User or group not found'
            ),

            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function addMember(AddMemberRequest $request): JsonResponse
    {
        $userId = $request->user_id;

        try {
            $this->familyGroupService->addMember($userId);

            return response()->json([
                'status'    => true,
                'message'   => 'Member added successfully.',
            ], Response::HTTP_OK);

        } catch (HandledException $exception) {
            return $this->error($exception);
        }
    }
}