<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\{
    Chat\ChatSearchRequest,
    Chat\ChatShowRequest,
    Chat\ChatStoreRequest,
};
use App\Services\{
    Chat\ChatService,
};
use App\Swagger\Api\Chat\ChatController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
    Support\Facades\DB,
};

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
    )
    {
    }

    #[Swagger\Index]
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

    #[Swagger\Search]
    public function search(ChatSearchRequest $request): JsonResponse
    {
        $text = strval($request->input('text'));

        try {
            $chats = $this->chatService->search($text);

            return response()->json([
                'status'    => true,
                'data'      => $chats,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {

            return $this->error($exception);
        }
    }

    #[Swagger\Store]
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

    #[Swagger\Show]
    public function show(ChatShowRequest $request, int $chatId): JsonResponse
    {
        $beforeId = $request->filled('before_id') ? intval($request->input('before_id')) : null;

        try {
            $chat = $this->chatService->getChatWithDiscussionsAndMembers($chatId, $beforeId);

            return response()->json([
                'status'    => true,
                'data'      => $chat,
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
