<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chat\ChatSocketAuthRequest;
use App\Services\Chat\ChatSocketService;
use App\Swagger\Api\Chat\ChatSocketController as Swagger;
use Illuminate\{
    Http\JsonResponse,
    Http\Response,
};

class ChatSocketController extends Controller
{
    public function __construct(
        private readonly ChatSocketService $chatSocketService,
    )
    {
    }

    #[Swagger\Config]
    public function config(): JsonResponse
    {
        return response()->json([
            'status'    => true,
            'data'      => ['app_key' => $this->chatSocketService->getAppKey()],
        ], Response::HTTP_OK);
    }

    #[Swagger\Auth]
    public function auth(ChatSocketAuthRequest $request): JsonResponse
    {
        $socketId       = strval($request->input('socket_id'));
        $channelName    = strval($request->input('channel_name'));

        try {
            $auth = $this->chatSocketService->authorize($socketId, $channelName);

            return response()->json([
                'status'    => true,
                'data'      => ['auth' => $auth],
            ], Response::HTTP_OK);

        } catch (\Exception $exception) {
            return $this->error($exception);
        }
    }
}
