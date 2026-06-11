<?php

namespace App\Providers\Routes\Auth;

use App\Http\Controllers\Api\Auth\{
    AuthController,
};
use Illuminate\Support\Facades\Route;

class AuthRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'auth'], function () {

            Route::post('signup', [AuthController::class, 'signUp']);
            Route::post('signin', [AuthController::class, 'signIn']);
        });
    }
}
