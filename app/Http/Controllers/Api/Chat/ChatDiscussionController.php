<?php

namespace App\Http\Controllers\API\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chat\{
    ChatDiscussionStoreRequest,
    ChatDiscussionUpdateRequest,
    ChatDiscussionIsReadRequest,
    ChatDiscussionSearchRequest,
};
use App\Services\{
    Chat\ChatDiscussionService,
};
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};
use OpenApi\Attributes as OA;

class ChatDiscussionController extends Controller
{
    public function __construct(
        private readonly ChatDiscussionService $chatDiscussionService,
    )
    {
    }

    #[OA\Post(
        path: '/api/chat/{chat_id}/discussion',
        operationId: 'chatDiscussionStore',
        summary: 'Создать сообщение в чате',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'text',
                            type: 'string',
                            example: 'Hello'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Message created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Chat message has been created'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'User is not a member of this chat'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function store(int $chatId, ChatDiscussionStoreRequest $request): JsonResponse
    {
        $text = $request->input('text') ? strval($request->input('text')) : null;

        try {
            $this->chatDiscussionService->create($chatId, $text);

            return response()->json([
                'status'    => true,
                'message'   => 'Chat message has been created',
            ], Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Put(
        path: '/api/chat/{chat_id}/discussion/{discussion_id}',
        operationId: 'chatDiscussionUpdate',
        summary: 'Обновить сообщение в чате',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1
                )
            ),
            new OA\Parameter(
                name: 'discussion_id',
                in: 'path',
                required: true,
                description: 'ID сообщения',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 15
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'text',
                            type: 'string',
                            example: 'Hello'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat message updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Chat message has been updated'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'User is not a member of this chat'
            ),
            new OA\Response(
                response: 404,
                description: 'Discussion not found'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function update(int $chatId, int $chatDiscussionId, ChatDiscussionUpdateRequest $request): JsonResponse
    {
        $text = $request->input('text') ? strval($request->input('text')) : null;

        try {
            $this->chatDiscussionService->update($chatId, $chatDiscussionId, $text);

            return response()->json([
                'status'    => true,
                'message'   => 'Chat message has been updated'
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Delete(
        path: '/api/chat/{chat_id}/discussion/{discussion_id}',
        operationId: 'chatDiscussionDelete',
        summary: 'Удалить сообщение в чате',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 5
                )
            ),
            new OA\Parameter(
                name: 'discussion_id',
                in: 'path',
                required: true,
                description: 'ID сообщения',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 15
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat message deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Message deleted successfully.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'User is not a member of this chat'
            ),
            new OA\Response(
                response: 404,
                description: 'Discussion not found'
            ),
        ]
    )]
    public function destroy(int $chatId, int $chatDiscussionId): JsonResponse
    {
        try {
            $this->chatDiscussionService->delete($chatId, $chatDiscussionId);

            return response()->json([
                'status'    => true,
                'message'   => 'Chat message has been deleted',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Get(
        path: '/api/chat/{chat_id}/discussion/search',
        operationId: 'chatDiscussionSearch',
        summary: 'Поиск сообщений в чате',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1
                )
            ),
            new OA\Parameter(
                name: 'text',
                in: 'query',
                required: true,
                description: 'Сообщение или имя для поиска',
                schema: new OA\Schema(
                    type: 'string',
                    example: 'Test'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(
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
                                ref: '#/components/schemas/ChatDiscussionWithUser'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'User is not a member of this chat'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function search(int $chatId, ChatDiscussionSearchRequest $request): JsonResponse
    {
        $text = strval($request->input('text'));

        try {
            $chatDiscussions = $this->chatDiscussionService->searchByText($chatId, $text);

            return response()->json([
                'status'    => true,
                'data'      => $chatDiscussions,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }

    #[OA\Post(
        path: '/api/chat/{chat_id}/discussion/mark_as_read',
        operationId: 'chatDiscussionMarkAsRead',
        summary: 'Изменение статуса сообщений в чате на прочитано',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer',
                    example: 1
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['ids'],
                properties: [
                    new OA\Property(
                        property: 'ids',
                        description: 'ID сообщений',
                        type: 'array',
                        items: new OA\Items(
                            type: 'integer',
                            example: 1
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Discussions marked as read',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'status',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Discussions is_read status was updated'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Can not update is_read status for this messages'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'User is not a member of this chat'
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function setIsRead(int $chatId, ChatDiscussionIsReadRequest $request): JsonResponse
    {
        $lookedDiscussionsIds = (array)$request->input('ids');

        try {
            $this->chatDiscussionService->setIsRead($chatId, $lookedDiscussionsIds);

            return response()->json([
                'status'    => true,
                'message'   => 'Discussions is_read status was updated',
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
