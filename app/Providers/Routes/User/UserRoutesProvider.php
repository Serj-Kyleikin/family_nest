<?php

namespace App\Providers\Routes\User;

use App\Http\Controllers\Api\User\{
    UserController,
    UserDataController,
};
use Illuminate\Support\Facades\Route;

class UserRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'user'], function () {

            Route::get('current', [UserController::class, 'current']);

            Route::group(['prefix' => 'data'], function () {

                Route::get('', [UserDataController::class, 'show']);
                Route::put('', [UserDataController::class, 'update']);
            });

            app(UserDiaryRoutesProvider::class)->register();
        });
    }
}
