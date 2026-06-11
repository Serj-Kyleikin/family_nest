<?php

use App\Providers\Routes\Auth\AuthRoutesProvider;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

    app(AuthRoutesProvider::class)->register();

});