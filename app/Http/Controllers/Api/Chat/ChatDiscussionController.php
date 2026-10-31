<?php

namespace App\Http\Controllers\Api\Chat;

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
use App\Swagger\Api\Chat\ChatDiscussionController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};

class ChatDiscussionController extends Controller
{
    public function __construct(
        private readonly ChatDiscussionService $chatDiscussionService,
    )
    {
    }

    #[Swagger\Store]
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

    #[Swagger\Update]
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

    #[Swagger\Destroy]
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

    #[Swagger\Search]
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

    #[Swagger\SetIsRead]
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
