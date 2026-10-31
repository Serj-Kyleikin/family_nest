<?php

use App\Providers\Routes\{
    Auth\AuthRoutesProvider,
    Family\FamilyRoutesProvider,
    Chat\ChatRoutesProvider,
    Expense\ExpenseRoutesProvider,
    User\UserRoutesProvider,
};
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

    app(AuthRoutesProvider::class)->register();

    Route::group(['middleware' => ['auth:sanctum']], function () {

        app(UserRoutesProvider::class)->register();
        app(FamilyRoutesProvider::class)->register();
        app(ChatRoutesProvider::class)->register();
        app(ExpenseRoutesProvider::class)->register();
    });
});