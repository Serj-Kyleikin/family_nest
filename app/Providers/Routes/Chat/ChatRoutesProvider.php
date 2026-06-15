<?php

namespace App\Providers\Routes\Chat;

use App\Http\Controllers\Api\Chat\{
    ChatController,
    ChatDiscussionController,
};
use Illuminate\Support\Facades\Route;

class ChatRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'chat'], function () {

            Route::post('', [ChatController::class, 'store']);
            Route::get('', [ChatController::class, 'index']);
            Route::get('search', [ChatController::class, 'search']);
            Route::get('{chat_id}', [ChatController::class, 'show']);

            Route::group(['prefix' => '{chat_id}/discussion'], function () {

                Route::post('', [ChatDiscussionController::class, 'store']);
                Route::put('{discussion_id}', [ChatDiscussionController::class, 'update']);
                Route::delete('{discussion_id}', [ChatDiscussionController::class, 'destroy']);

                Route::get('search', [ChatDiscussionController::class, 'search']);
                Route::post('mark_as_read', [ChatDiscussionController::class, 'setIsRead']);
            });
        });
    }
}
