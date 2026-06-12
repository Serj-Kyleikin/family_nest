<?php

namespace App\Providers\Routes\Family;

use App\Http\Controllers\Api\Family\FamilyGroupController;
use Illuminate\Support\Facades\Route;

class FamilyRoutesProvider
{
    public function register(): void
    {
        Route::group(['prefix' => 'family'], function () {

            Route::group(['prefix' => 'group'], function () {

                Route::post('', [FamilyGroupController::class, 'create']);
                
                Route::group(['prefix' => 'members'], function () {

                    Route::put('/add', [FamilyGroupController::class, 'addMember']);
                    Route::get('', [FamilyGroupController::class, 'members']);
                });
            });
        });
    }
}
