<?php

namespace App\Providers\Routes\User;

use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

class UserRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'user'], function () {

            Route::get('current', [UserController::class, 'current']);
        });
    }
}
