<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\{
    Chat\ChatStoreRequest,
};
use App\Services\{
    Chat\ChatService,
};
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};
use OpenApi\Attributes as OA;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
    )
    {
    }

    #[OA\Get(
        path: '/api/chat',
        operationId: 'chatList',
        summary: 'Получить чаты пользователя с последним сообщением',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Property(
                property: 'status',
                type: 'boolean',
                example: true
            ),
            new OA\Property(
                property: 'data',
                type: 'array',
                items: new OA\Items(
                    ref: '#/components/schemas/ChatWithLastDiscussionAndMembers'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        try {
            $chats = $this->chatService->index();

            return response()->json([
                'status'    => true,
                'data'      => $chats,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {

            return $this->error($exception);
        }
    }

    #[OA\Post(
        path: '/api/chat',
        operationId: 'chatStore',
        summary: 'Создать чат или получить ранее созданный чат',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_to_id'],
                properties: [
                    new OA\Property(
                        property: 'user_to_id',
                        type: 'integer',
                        example: 1,
                        description: 'Id пользователя с кем создаётся чат'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Chat created successfully',
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
                            type: 'object',
                            ref: '#/components/schemas/ChatWithDiscussionsAndMembers'
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
                description: 'Попытка создать чат с самим собой',
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error'
            ),
        ]
    )]
    public function store(ChatStoreRequest $request): JsonResponse
    {
        $userToId = intval($request->input('user_to_id'));

        try {
            DB::beginTransaction();
            $chat = $this->chatService->create($userToId);
            DB::commit();

            return response()->json([
                'status'    => true,
                'data'      => $chat,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {

            DB::rollback();
            return $this->error($exception);
        }
    }

    #[OA\Get(
        path: '/api/chat/{chat_id}',
        operationId: 'chatShow',
        summary: 'Получить чат',
        tags: ['Chat'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'chat_id',
                in: 'path',
                required: true,
                description: 'ID чата',
                schema: new OA\Schema(
                    type: 'integer'
                ),
                example: 5
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat created successfully',
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
                            type: 'object',
                            ref: '#/components/schemas/ChatWithDiscussionsAndMembers'
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
                description: 'This user is not member of this chat',
            ),
        ]
    )]
    public function show(int $chatId): JsonResponse
    {
        try {
            $chat = $this->chatService->getChatWithDiscussionsAndMembers($chatId);

            return response()->json([
                'status'    => true,
                'data'      => $chat,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
