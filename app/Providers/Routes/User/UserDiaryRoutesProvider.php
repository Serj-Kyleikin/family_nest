<?php

namespace App\Providers\Routes\User;

use App\Http\Controllers\Api\User\UserDiaryController;
use Illuminate\Support\Facades\Route;

class UserDiaryRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'diary'], function () {

            Route::get('', [UserDiaryController::class, 'index']);
            Route::post('', [UserDiaryController::class, 'store']);

            Route::group(['prefix' => '{id}', 'where' => ['id' => '[0-9]+']], function () {

                Route::put('', [UserDiaryController::class, 'update']);
                Route::delete('', [UserDiaryController::class, 'destroy']);
            });
        });
    }
}
