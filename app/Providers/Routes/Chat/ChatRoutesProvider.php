<?php

namespace App\Providers\Routes\Chat;

use App\Http\Controllers\Api\Chat\{
    ChatController,
    ChatDiscussionController,
    ChatSocketController,
};
use Illuminate\Support\Facades\Route;

class ChatRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'chat'], function () {

            Route::post('', [ChatController::class, 'store']);
            Route::get('', [ChatController::class, 'index']);
            Route::get('{chat_id}', [ChatController::class, 'show'])
                ->whereNumber('chat_id');

            Route::get('search', [ChatController::class, 'search']);

            Route::get('socket', [ChatSocketController::class, 'config']);
            Route::post('socket/auth', [ChatSocketController::class, 'auth']);

            Route::group(['prefix' => '{chat_id}/discussion', 'where' => ['chat_id' => '[0-9]+']], function () {

                Route::post('', [ChatDiscussionController::class, 'store']);
                Route::put('{discussion_id}', [ChatDiscussionController::class, 'update'])
                    ->whereNumber('discussion_id');
                Route::delete('{discussion_id}', [ChatDiscussionController::class, 'destroy'])
                    ->whereNumber('discussion_id');

                Route::get('search', [ChatDiscussionController::class, 'search']);
                Route::post('mark_as_read', [ChatDiscussionController::class, 'setIsRead']);
            });
        });
    }
}
